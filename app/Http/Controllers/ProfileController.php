<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Update the user's required profile information (KYC).
     */
    public function updateRequired(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'phone_no' => ['required', 'string', 'min:11', 'max:11', 'unique:users,phone_no,' . $user->id],
            'state' => ['required', 'string', 'max:20'],
            'lga' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
            'birthdate' => ['required', 'date', 'before:today'],
            'bvn' => ['required', 'string', 'min:11', 'max:11'],
            'pin' => ['required', 'string', 'min:5', 'max:5', 'regex:/^[0-9]+$/'],
            'termsCheck' => ['required', 'accepted'],
        ], [
            'pin.regex' => 'The transaction PIN must be numeric.',
            'termsCheck.accepted' => 'You must agree to the terms and conditions.',
        ]);

        $user->forceFill([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'phone_no' => $request->phone_no,
            'state' => $request->state,
            'lga' => $request->lga,
            'address' => $request->address,
            'birthdate' => $request->birthdate,
            'bvn' => $request->bvn,
            'pin' => $request->pin,
        ])->save();

        return Redirect::back()->with('success', 'Profile updated successfully!');
    }

   /**
     * Upload or update profile photo.
     */
    public function updatePhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048', // 2MB max
        ]);

        $user = Auth::user();

        try {
            // ✅ Delete old photo if exists (with improved logic)
            if ($user->photo) {
                $this->deleteOldProfilePhoto($user->photo);
            }

            // ✅ Store new image using Laravel's Storage facade
            $file = $request->file('photo');
            $fileName = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Store in storage/app/public/uploads/profile_photos
            $path = $file->storeAs('uploads/profile_photos', $fileName, 'public');
            
            // ✅ Build full HTTP link
            $fullUrl = Storage::disk('public')->url($path);

            // ✅ Save to database
            $user->update([
                'photo' => $fullUrl,
            ]);

            return back()->with('status', '✅ Profile photo updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', '❌ Failed to update profile photo: ' . $e->getMessage());
        }
    }

    /**
     * Delete old profile photo with proper handling
     */
    private function deleteOldProfilePhoto(string $photoUrl): void
    {
        // Skip external URLs (like gravatar)
        if (Str::startsWith($photoUrl, 'http') && !Str::contains($photoUrl, '/storage/')) {
            return;
        }

        try {
            // If it's a storage URL, extract the path
            if (Str::contains($photoUrl, '/storage/')) {
                // Remove the base URL to get the storage path
                $baseUrl = config('app.url') . '/storage/';
                $path = str_replace($baseUrl, '', $photoUrl);
                Storage::disk('public')->delete($path);
            } 
            // If it's already a storage path (not full URL)
            elseif (Storage::disk('public')->exists($photoUrl)) {
                Storage::disk('public')->delete($photoUrl);
            }
            // For old-style public/uploads paths
            elseif (Str::contains($photoUrl, '/uploads/')) {
                // Extract filename from URL
                $filename = basename($photoUrl);
                Storage::disk('public')->delete('uploads/profile_photos/' . $filename);
            }
        } catch (\Exception $e) {
            // Log error but don't fail the upload
            \Log::error('Failed to delete old profile photo: ' . $e->getMessage());
        }
    }

    /**
     * Update the user's transaction PIN.
     */
    public function updatePin(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'pin' => ['required', 'confirmed', 'string', 'min:5', 'max:5', 'regex:/^[0-9]+$/'],
        ], [
            'pin.regex' => 'The transaction PIN must be exactly 5 digits.',
        ]);

        $user = Auth::user();
        $user->pin = $request->pin;
        $user->save();

        return Redirect::back()->with('status', 'Transaction PIN updated successfully!');
    }
}
