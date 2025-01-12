<?php

namespace App\Http\Controllers\Api;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\VendorResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\VendorStoreRequest;
use App\Http\Requests\VendorUpdateRequest;

class VendorController extends Controller
{


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vendors = Vendor::with(['products', 'orders'])
            ->approved()
            ->paginate(10);

        return VendorResource::collection($vendors);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VendorStoreRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('store_logo')) {
            $validated['store_logo'] = $request->file('store_logo')
                ->store('store-logos', 'public');
        }

        $vendor = Vendor::create($validated);

        return new VendorResource($vendor);
    }

    /**
     * Display the specified resource.
     */
    public function show(Vendor $vendor)
    {
        return new VendorResource($vendor->load(['products', 'orders']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VendorUpdateRequest $request, Vendor $vendor)
    {
        $validated = $request->validated();

        if ($request->hasFile('store_logo')) {
            // Delete old logo if exists
            if ($vendor->store_logo) {
                Storage::disk('public')->delete($vendor->store_logo);
            }

            $validated['store_logo'] = $request->file('store_logo')
                ->store('store-logos', 'public');
        }

        $vendor->update($validated);

        return new VendorResource($vendor);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vendor $vendor)
    {
        // Delete store logo if exists
        if ($vendor->store_logo) {
            Storage::disk('public')->delete($vendor->store_logo);
        }

        $vendor->delete();

        return response()->json(['message' => 'Vendor deleted successfully']);
    }

    /**
     * Get vendor analytics data.
     */
    public function analytics(Vendor $vendor)
    {
        // Will implement later
    }

    /**
     * Get vendor revenue data.
     */
    public function revenue(Vendor $vendor)
    {
        // Will implement later
    }

    /**
     * Get the authenticated vendor's profile
     * This includes completion status and profile data
     */

    public function profile()
    {
        $vendor = auth('vendor')->user();
        $vendor->load(['products', 'orders']); // Eager load relationships

        // Calculate profile completion percentage
        $completionStatus = $this->calculateProfileCompletion($vendor);

        return response()->json([
            'vendor' => new VendorResource($vendor),
            'completion_status' => $completionStatus
        ]);
    }

    /**
     * Calculate profile completion percentage
     * This helps vendors know what information they still need to provide
     */
    private function calculateProfileCompletion($vendor)
    {
        $fields = [
            'full_name' => 10,
            'email' => 10,
            'phone' => 10,
            'store_name' => 15,
            'store_description' => 10,
            'business_category' => 10,
            'address' => 10,
            'business_hours' => 10,
            'payment_details' => 10,
            'store_logo' => 5
        ];

        $completedScore = 0;
        foreach ($fields as $field => $weight) {
            if (!empty($vendor->$field)) {
                $completedScore += $weight;
            }
        }

        return [
            'percentage' => $completedScore,
            'missing_fields' => collect($fields)
                ->filter(fn($weight, $field) => empty($vendor->$field))
                ->keys()
                ->values()
                ->all()
        ];
    }

    /**
     * Get store details including business hours and settings
     */
    public function getStore()
    {
        $vendor = auth('vendor')->user();
        return response()->json([
            'store' => [
                'name' => $vendor->store_name,
                'description' => $vendor->store_description,
                'logo' => $vendor->store_logo,
                'business_category' => $vendor->business_category,
                'address' => $vendor->address,
                'business_hours' => $vendor->business_hours,
                'is_active' => $vendor->is_approved && $vendor->is_verified,
                'payment_details' => $vendor->payment_details,
            ],
            'stats' => [
                'total_products' => $vendor->products()->count(),
                'total_orders' => $vendor->orders()->count(),
                'pending_orders' => $vendor->orders()->where('status', 'pending')->count(),
            ]
        ]);
    }

    /**
     * Update store details
     * This handles store-specific updates separately from profile updates
     */
    public function updateStore(Request $request, Vendor $vendor)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'store_name' => 'sometimes|string|max:255',
            'store_description' => 'nullable|string|max:1000',
            'business_category' => 'sometimes|string|max:100',
            'address' => 'sometimes|string|max:500',
            'business_hours' => 'nullable|array',
            'business_hours.*.day' => 'required_with:business_hours|string',
            'business_hours.*.open' => 'required_with:business_hours|string',
            'business_hours.*.close' => 'required_with:business_hours|string',
            'payment_details' => 'nullable|array',
            'payment_details.bank_name' => 'required_with:payment_details|string',
            'payment_details.account_number' => 'required_with:payment_details|string',
            'payment_details.account_name' => 'required_with:payment_details|string',
        ]);

        try {
            // Begin transaction for data consistency
            DB::beginTransaction();

            // Update store information
            $vendor->update($validated);

            // If logo is included in the request
            if ($request->hasFile('store_logo')) {
                // Delete old logo if exists
                if ($vendor->store_logo) {
                    Storage::disk('public')->delete($vendor->store_logo);
                }

                // Store new logo
                $vendor->update([
                    'store_logo' => $request->file('store_logo')
                        ->store('store-logos', 'public')
                ]);
            }

            DB::commit();

            // Return updated store details
            return response()->json([
                'message' => 'Store details updated successfully',
                'store' => new VendorResource($vendor)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update store details',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
