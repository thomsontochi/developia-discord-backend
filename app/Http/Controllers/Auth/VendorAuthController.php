<?php

namespace App\Http\Controllers\Auth;

use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class VendorAuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:vendors',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string',
            'store_name' => 'required|string|max:255',
            'store_description' => 'nullable|string',
            'business_category' => 'required|string',
            'address' => 'required|string',
            
        ]);

        $vendor = Vendor::create([
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'store_name' => $validated['store_name'],
            'store_description' => $validated['store_description'],
            'business_category' => $validated['business_category'],
            'address' => $validated['address'],
        ]);

        event(new Registered($vendor));

        Auth::guard('vendor')->login($vendor);

        return response()->json([
            'message' => 'Vendor registered successfully',
            'vendor' => $vendor
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::guard('vendor')->attempt($credentials)) {
            $request->session()->regenerate();

            return response()->json([
                'message' => 'Logged in successfully',
                'vendor' => Auth::guard('vendor')->user()
            ]);
        }

        return response()->json([
            'message' => 'The provided credentials do not match our records.'
        ], 401);
    }

    public function logout(Request $request)
    {
        Auth::guard('vendor')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
