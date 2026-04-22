<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DataVariationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Define the standard services based on service_id as requested
        $availableServices = [
            'mtn-data' => ['name' => 'MTN Data', 'icon' => 'ti ti-device-mobile', 'color' => 'warning'],
            'airtel-data' => ['name' => 'Airtel Data', 'icon' => 'ti ti-device-mobile', 'color' => 'danger'],
            'glo-data' => ['name' => 'Glo Data', 'icon' => 'ti ti-device-mobile', 'color' => 'success'],
            'etisalat-data' => ['name' => '9mobile Data', 'icon' => 'ti ti-device-mobile', 'color' => 'dark'],
            'waec' => ['name' => 'WAEC PIN', 'icon' => 'ti ti-school', 'color' => 'secondary'],
            'smile-direct' => ['name' => 'Smile Direct', 'icon' => 'ti ti-wifi', 'color' => 'info'],
            'dstv' => ['name' => 'DStv Subscription', 'icon' => 'ti ti-device-tv-old', 'color' => 'primary'],
            'gotv' => ['name' => 'GOtv Subscription', 'icon' => 'ti ti-device-tv-old', 'color' => 'primary'],
            'startimes' => ['name' => 'StarTimes Subscription', 'icon' => 'ti ti-device-tv-old', 'color' => 'primary'],
            'showmax' => ['name' => 'Showmax', 'icon' => 'ti ti-brand-netflix', 'color' => 'danger'],
        ];

        // Get counts for each service using service_id
        $serviceCounts = DataVariation::select('service_id', \DB::raw('count(*) as total'))
            ->groupBy('service_id')
            ->pluck('total', 'service_id')
            ->toArray();

        // Stats for the index page
        $totalVariationsCount = DataVariation::count();
        $activeVariationsCount = DataVariation::where('status', 'enabled')->count();
        $inactiveVariationsCount = DataVariation::where('status', 'disabled')->count();

        return view('admin.data-variations.index', compact(
            'availableServices',
            'serviceCounts',
            'totalVariationsCount',
            'activeVariationsCount',
            'inactiveVariationsCount'
        ));
    }

    /**
     * Display the specified resource.
     */
    public function show($serviceId)
    {
        $services = [
            'mtn-data' => 'MTN Data',
            'airtel-data' => 'Airtel Data',
            'glo-data' => 'Glo Data',
            'etisalat-data' => '9mobile Data',
            'waec' => 'WAEC PIN',
            'smile-direct' => 'Smile Direct',
            'dstv' => 'DStv Subscription',
            'gotv' => 'GOtv Subscription',
            'startimes' => 'StarTimes Subscription',
            'showmax' => 'Showmax',
        ];

        if (!isset($services[$serviceId])) {
            return redirect()->route('admin.data-variations.index')->with('error', 'Invalid service specified.');
        }

        $serviceName = $services[$serviceId];
        $variations = DataVariation::where('service_id', $serviceId)
            ->latest()
            ->paginate(15);

        return view('admin.data-variations.show', compact('variations', 'serviceId', 'serviceName'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|string',
            'name' => 'required|string|max:255',
            'variation_amount' => 'required|numeric|min:0',
            'variation_code' => 'required|string|unique:data_variations,variation_code',
            'convinience_fee' => 'nullable|numeric|min:0',
        ]);

        $validated['convinience_fee'] = $validated['convinience_fee'] ?? 0;
        $validated['status'] = $request->has('status') ? 'enabled' : 'disabled';
        $validated['fixedPrice'] = $request->has('fixedPrice') ? 'true' : 'false';

        DataVariation::create($validated);

        return back()->with('success', 'Variation added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DataVariation $dataVariation)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'variation_amount' => 'required|numeric|min:0',
            'variation_code' => 'required|string|unique:data_variations,variation_code,' . $dataVariation->id,
            'convinience_fee' => 'nullable|numeric|min:0',
            'service_id' => 'nullable|string',
        ]);

        $validated['convinience_fee'] = $validated['convinience_fee'] ?? 0;
        $validated['status'] = $request->has('status') ? 'enabled' : 'disabled';
        $validated['fixedPrice'] = $request->has('fixedPrice') ? 'true' : 'false';

        $dataVariation->update($validated);

        return back()->with('success', 'Variation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DataVariation $dataVariation)
    {
        $dataVariation->delete();
        return back()->with('success', 'Variation deleted successfully.');
    }

    /**
     * Sync data variations from Smart Idea API.
     */
    public function sync()
    {
        $apiToken = env('AREWA_API_TOKEN');
        $apiUrl = env('AREWA_BASE_URL', 'https://api.arewasmart.com.ng/api/v1') . '/data/variations';

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiToken,
                'Accept' => 'application/json',
            ])->get($apiUrl);

            if (!$response->successful()) {
                Log::error('Smart Idea Sync Error', ['response' => $response->json()]);
                return back()->with('error', 'Failed to fetch data from API: ' . ($response->json()['message'] ?? 'Unknown Error'));
            }

            $data = $response->json();

            if ($data['status'] !== 'success' || !isset($data['data'])) {
                return back()->with('error', 'API returned unsuccessful status.');
            }

            $syncedCount = 0;
            foreach ($data['data'] as $plan) {
                // Determine if variation already exists to preserve convenience fee
                $existingVariation = DataVariation::where('variation_code', $plan['variation_code'])->first();
                $convinienceFee = $existingVariation ? $existingVariation->convinience_fee : 0;

                DataVariation::updateOrCreate(
                    ['variation_code' => $plan['variation_code']],
                    [
                        'service_name' => $plan['service_name'],
                        'service_id' => $plan['service_id'],
                        'name' => $plan['name'],
                        'variation_amount' => $plan['variation_amount'],
                        'fixedPrice' => (isset($plan['fixedPrice']) && (strtolower($plan['fixedPrice']) === 'yes' || strtolower($plan['fixedPrice']) === 'true')) ? 'true' : 'false',
                        'status' => strtolower($plan['status'] ?? 'enabled') === 'enabled' ? 'enabled' : 'disabled',
                        'convinience_fee' => $convinienceFee,
                    ]
                );
                $syncedCount++;
            }

            return back()->with('success', "Successfully synced {$syncedCount} variations.");

        } catch (\Exception $e) {
            Log::error('Smart Idea Sync Exception', ['error' => $e->getMessage()]);
            return back()->with('error', 'An error occurred during sync: ' . $e->getMessage());
        }
    }
}
