<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'store_details' => $this->store_details,
            'business_category' => $this->business_category,
            'status' => $this->status,
            'is_verified' => $this->is_verified,
            'is_approved' => $this->is_approved,
            'email_verified_at' => $this->email_verified_at,
            'onboarding_step' => $this->onboarding_step,
            'has_completed_onboarding' => $this->has_completed_onboarding,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'products_count' => $this->when($this->products_count !== null, $this->products_count),
            'orders_count' => $this->when($this->orders_count !== null, $this->orders_count),
            'products' => ProductResource::collection($this->whenLoaded('products')),
            'orders' => OrderResource::collection($this->whenLoaded('orders')),
            // Change this line to use request user type check
            'verification_documents' => $this->when($request->user() && $request->user()->role === 'admin', $this->verification_documents)
        ];
    }
}
