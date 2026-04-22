<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmeData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminSmeDataController extends Controller
{
    /**
     * API Configuration Helpers
     */
    private function getApiBaseUrl()
    {
        return env('AREWA_BASE_URL', 'https://api.arewasmart.com.ng/api/v1');
    }

    private function getApiToken()
    {
        return env('AREWA_API_TOKEN');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $smeData = SmeData::latest()->paginate(20);

        // Dynamic stats calculation
        $stats = [
            'total' => SmeData::count(),
            'mtn' => SmeData::where('network', 'MTN')->count(),
            'airtel' => SmeData::where('network', 'AIRTEL')->count(),
            'glo' => SmeData::where('network', 'GLO')->count(),
            'mobile' => SmeData::where('network', '9MOBILE')->count(),
        ];

        return view('admin.data-variations.sme-data', [
            'smeData' => $smeData,
            'stats' => $stats
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'data_id' => 'required|string|unique:sme_datas,data_id',
            'network' => 'required|string',
            'plan_type' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'size' => 'required|string',
            'validity' => 'required|string',
            'status' => 'nullable',
        ]);

        $validated['status'] = $request->has('status');

        SmeData::create($validated);

        return back()->with('success', 'SME Data plan added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SmeData $smeData)
    {
        $validated = $request->validate([
            'data_id' => 'required|string|unique:sme_datas,data_id,' . $smeData->id,
            'network' => 'required|string',
            'plan_type' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'size' => 'required|string',
            'validity' => 'required|string',
            'status' => 'nullable',
        ]);

        $validated['status'] = $request->has('status');

        $smeData->update($validated);

        return back()->with('success', 'SME Data plan updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SmeData $smeData)
    {
        $smeData->delete();
        return back()->with('success', 'SME Data plan deleted successfully.');
    }

    /**
     * Sync data from Smart Idea API
     */
    public function sync()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->getApiToken(),
                'Accept' => 'application/json',
            ])->get($this->getApiBaseUrl() . '/sme-data/variations');

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['status']) && $data['status'] === 'success' && isset($data['data'])) {
                    $plans = $data['data'];

                    foreach ($plans as $plan) {
                        SmeData::updateOrCreate(
                            ['data_id' => $plan['data_id']],
                            [
                                'network' => strtoupper($plan['network']),
                                'plan_type' => $plan['plan_type'],
                                'amount' => $plan['amount'],
                                'size' => $plan['size'],
                                'validity' => $plan['validity'],
                                'status' => true, // Default to true on sync
                            ]
                        );
                    }

                    return back()->with('success', count($plans) . ' SME Data plans synced successfully.');
                }

                return back()->with('error', 'API returned an unexpected response format.');
            }

            return back()->with('error', 'Failed to connect to provider. Status: ' . $response->status());

        } catch (\Exception $e) {
            Log::error('SME Data Sync Error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred during sync: ' . $e->getMessage());
        }
    }
}

