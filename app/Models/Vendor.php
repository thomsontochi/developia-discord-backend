<?php

namespace App\Models;

use App\Models\Product;
use App\Models\VendorActivityLog;
use Laravel\Sanctum\HasApiTokens;
use App\Models\VendorCommunication;
use Illuminate\Database\Eloquent\Model;
use App\Notifications\VendorVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Notifications\VendorVerificationNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;



class Vendor extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, MustVerifyEmailTrait, Notifiable, HasApiTokens;

    protected $fillable = [
        'full_name',
        'email',
        'phone',

        'password',
        'store_name',
        'store_description',
        'business_category',
        'address',
        'business_hours',
        'payment_details',
        'store_logo',
        'status',
        'verification_documents',
        'approved_at',
        'rejection_reason',
        'has_completed_onboarding',
        'onboarding_step'
    ];

    protected $casts = [
        'business_hours' => 'array',
        'payment_details' => 'array',
        'is_verified' => 'boolean',
        'is_approved' => 'boolean',
        'email_verified_at' => 'datetime',
        'verification_documents' => 'array',
        'onboarding_step' => 'array',
        'approved_at' => 'datetime',
        'has_completed_onboarding' => 'boolean'
    ];

    // Relationships
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // Query Scopes
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    // Accessor for full store details
    public function getStoreDetailsAttribute()
    {
        return [
            'name' => $this->store_name,
            'description' => $this->store_description,
            'logo' => $this->store_logo,
            'business_hours' => $this->business_hours,
            'address' => $this->address,
        ];
    }

    // Helper methods for status checks
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    // Helper for onboarding progress
    public function getCurrentOnboardingStep()
    {
        return $this->onboarding_step['current_step'] ?? 1;
    }

    public function hasCompletedStep($step)
    {
        return in_array($step, $this->onboarding_step['completed_steps'] ?? []);
    }

    public function updateOnboardingStep($step)
    {
        $currentSteps = $this->onboarding_step['completed_steps'] ?? [];
        if (!in_array($step, $currentSteps)) {
            $currentSteps[] = $step;
        }

        $this->update([
            'onboarding_step' => [
                'current_step' => $step + 1,
                'completed_steps' => $currentSteps
            ]
        ]);
    }

    // public function sendEmailVerificationNotification()
    // {
    //     $this->notify(new VendorVerificationNotification);
    // }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VendorVerifyEmail);
    }


   

    public function activityLogs(): HasMany
    {
        return $this->hasMany(VendorActivityLog::class);
    }

    public function communications(): HasMany
    {
        return $this->hasMany(VendorCommunication::class);
    }

    // Helper methods for logging activities
    public function logActivity(string $action, array $details = [])
    {
        return $this->activityLogs()->create([
            'action' => $action,
            'details' => $details
        ]);
    }

    // Helper methods for communications
    public function sendCommunication(string $type, string $subject, string $content)
    {
        return $this->communications()->create([
            'type' => $type,
            'subject' => $subject,
            'content' => $content
        ]);
    }

    public function getUnreadCommunicationsCount(): int
    {
        return $this->communications()->whereNull('read_at')->count();
    }
}
