<?php

namespace App\Http\Controllers\Auth;

use Exception;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\VendorResource;
use Illuminate\Auth\Events\Registered;
use App\Notifications\VendorVerifyEmail;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;

class VendorAuthController extends Controller
{
    /**
     * Step 1: Initial Account Creation
     */


    public function register(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validate request
            $validated = $request->validate([
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|unique:vendors',
                'password' => 'required|string|min:8|confirmed',
            ]);

            // Create vendor
            $vendor = Vendor::create([
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'status' => 'pending',
                'onboarding_step' => [
                    'current_step' => 1,
                    'completed_steps' => []
                ]
            ]);

            // Generate token - this is the Laravel Sanctum way
            $token = $vendor->createToken('vendor-token')->plainTextToken;

            // Send verification email
            $vendor->notify((new VendorVerifyEmail)->onQueue('emails'));

            DB::commit();

            // Return response with token
            return response()->json([
                'message' => 'Registration successful. Please check your email for verification.',
                'vendor' => new VendorResource($vendor),
                'token' => $token, // Include token in response
                'verification' => [
                    'sent' => true,
                    'email' => $vendor->email,
                ]
            ], 201);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Vendor registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => config('app.debug')
                    ? $e->getMessage()
                    : 'Registration failed. Please try again later.'
            ], 500);
        }
    }


    public function login(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        // Check if login is via email or store ID
        if ($request->has('email')) {
            $credentials = $request->validate([
                'email' => 'required|email',
            ]);
            $field = 'email';
        } else {
            $credentials = $request->validate([
                'storeId' => 'required|string',
            ]);
            $field = 'store_id'; // Assuming this is your database column name
        }

        try {
            if (Auth::guard('vendor')->attempt([
                $field => $credentials[$field === 'email' ? 'email' : 'storeId'],
                'password' => $request->password
            ])) {
                $vendor = Auth::guard('vendor')->user();
                $token = $vendor->createToken('vendor-token')->plainTextToken;

                return response()->json([
                    'user' => $vendor,
                    'token' => $token,
                    'message' => 'Login successful'
                ]);
            }

            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        } catch (\Exception $e) {
            \Log::error('Vendor login error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred during login'
            ], 500);
        }
    }

    /**
     * Step 2: Store Setup
     */
    

    public function setupStore(Request $request)
    {
        // Change this line
        // $vendor = auth('vendor')->user();
        // To this
        $vendor = $request->user();

        if (!$vendor) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        if (!$vendor->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Please verify your email first'
            ], 403);
        }

        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'store_description' => 'required|string',
            'business_category' => 'required|string',
            'address' => 'required|string',
            'store_logo' => 'nullable|image|max:2048'
        ]);

        // Handle store logo upload if provided
        if ($request->hasFile('store_logo')) {
            $validated['store_logo'] = $request->file('store_logo')
                ->store('store-logos', 'public');
        }

        $vendor->update($validated);
        $vendor->updateOnboardingStep(2);

        return response()->json([
            'message' => 'Store setup completed',
            'next_step' => 3,
            'vendor' => new VendorResource($vendor)
        ]);
    }

    // public function setupPayment(Request $request)
    // {
    //     $vendor = auth('vendor')->user();

    //     // Check previous step completion
    //     if (!$vendor->hasCompletedStep(2)) {
    //         return response()->json([
    //             'message' => 'Please complete store setup first'
    //         ], 403);
    //     }

    //     $validated = $request->validate([
    //         'payment_details.bank_name' => 'required|string',
    //         'payment_details.account_number' => 'required|string',
    //         'payment_details.account_name' => 'required|string',
    //     ]);

    //     $vendor->update([
    //         'payment_details' => $validated['payment_details']
    //     ]);

    //     $vendor->updateOnboardingStep(3);

    //     // Mark onboarding as completed after payment setup
    //     $vendor->update(['has_completed_onboarding' => true]);

    //     return response()->json([
    //         'message' => 'Payment setup completed',
    //         'vendor' => new VendorResource($vendor)
    //     ]);
    // }
    public function setupPayment(Request $request)
    {
        $vendor = $request->user();

        if (!$vendor) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        // Simplified validation matching our schema
        $validated = $request->validate([
            'bank_name' => 'required|string',
            'account_number' => 'required|string',
            'account_name' => 'required|string',
        ]);

        // Store payment details as JSON
        $vendor->update([
            'payment_details' => $validated
        ]);

        $vendor->updateOnboardingStep(3);
        $vendor->update(['has_completed_onboarding' => true]);

        return response()->json([
            'message' => 'Payment setup completed',
            'vendor' => new VendorResource($vendor)
        ]);
    }

    public function uploadDocuments(Request $request)
    {
        // $vendor = auth('vendor')->user();
        $vendor = $request->user();
        if (!$vendor) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        $validated = $request->validate([
            'business_license' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'id_proof' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'address_proof' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $documents = [];

        foreach ($validated as $type => $file) {
            $path = $file->store('vendor-documents/' . $vendor->id, 'public');
            $documents[$type] = [
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'uploaded_at' => now(),
            ];
        }

        $vendor->update([
            'verification_documents' => $documents,
            'status' => 'pending' // Reset to pending for admin review
        ]);

        return response()->json([
            'message' => 'Documents uploaded successfully, waiting for review',
            'vendor' => new VendorResource($vendor)
        ]);
    }


    public function verify(Request $request)
    {
        $vendor = Vendor::find($request->route('id'));

        if (!$vendor) {
            return redirect(config('app.frontend_url') . '/vendor/email-verification?status=invalid');
        }

        if (!hash_equals(
            (string) $request->route('hash'),
            sha1($vendor->getEmailForVerification())
        )) {
            return redirect(config('app.frontend_url') . '/vendor/email-verification?status=invalid-hash');
        }

        if ($vendor->hasVerifiedEmail()) {
            return redirect(config('app.frontend_url') . '/vendor/email-verification?status=already-verified');
        }

        if ($vendor->markEmailAsVerified()) {
            event(new Verified($vendor));
        }

        // Redirect to your actual frontend URL
        return redirect('http://localhost:5174/vendor/email-verification?status=success');
    }

    public function resendVerification(Request $request)
    {
        $vendor = $request->user();

        if ($vendor->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified']);
        }

        $vendor->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification link sent']);
    }

    public function logout(Request $request)
    {
        Auth::guard('vendor')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out successfully']);
    }



    public function checkVerificationStatus($email)
    {
        $vendor = Vendor::where('email', $email)->firstOrFail();

        return response()->json([
            'verified' => $vendor->hasVerifiedEmail(),
            'message' => $vendor->hasVerifiedEmail()
                ? 'Email is verified'
                : 'Email is not verified',

            'vendor' => [
                'email' => $vendor->email,
                'full_name' => $vendor->full_name,
                'status' => $vendor->status,
                'onboarding_step' => $vendor->onboarding_step
            ]
        ]);
    }

}
