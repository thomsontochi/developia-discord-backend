<?php

namespace App\Jobs;

use App\Models\Vendor;
use Illuminate\Auth\Events\Registered;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class SendVendorVerificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $vendor;
    public $tries = 3;

    public function __construct(Vendor $vendor)
    {
        $this->vendor = $vendor;
    }

    public function handle()
    {
        try {
          
            event(new Registered($this->vendor));
        } catch (\Exception $e) {
            Log::error('Failed to send verification email: ' . $e->getMessage(), [
                'vendor_id' => $this->vendor->id,
                'email' => $this->vendor->email
            ]);
            throw $e;
        }
    }

    public function failed(\Exception $e)
    {
        Log::error('Vendor verification email job failed: ' . $e->getMessage(), [
            'vendor_id' => $this->vendor->id
        ]);
    }
}
