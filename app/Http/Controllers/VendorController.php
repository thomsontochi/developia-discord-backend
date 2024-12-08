<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
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
     * Update vendor store details.
     */
    public function updateStore(Request $request, Vendor $vendor)
    {
        // Will implement later
    }
}
