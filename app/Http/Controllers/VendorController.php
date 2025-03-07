<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VendorController extends Controller
{
    // public function index(){

    //     // get aa vendors
    //     $vendors = Vendor::query()
    //         ->latest()
    //         ->paginate(10);

    //         $stats = [
    //             'total' => Vendor::count(),
    //             'active' => Vendor::where('status', 'approved')->count(),
    //             'inactive' => Vendor::where('status', 'suspended')->count(),
    //             'pending' => Vendor::where('status', 'pending')->count(),
    //             'newThisMonth' => Vendor::where('status', 'approved')
    //                 ->where('onboarding_step->current_step', '>', 1)
    //                 ->whereMonth('created_at', now()->month)
    //                 ->count(),
    //             'suspended' => Vendor::where('status', 'suspended')
    //                 ->where('onboarding_step->current_step', '>', 1)
    //                 ->count(),
    //         ];

    //     return Inertia::render('Vendors/Index',  [
    //         'vendors' => $vendors,
    //         'stats' => $stats,
    //     ]);

        
    // }

    public function index(Request $request)
    {
        $vendors = Vendor::query()
            ->with(['activityLogs' => fn($q) => $q->latest()->limit(5), 'communications'])
            ->when($request->search, function($query, $search) {
                $query->where('store_name', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($request->status, function($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->date_from, function($query, $date) {
                $query->whereDate('created_at', '>=', $date);
            })
            ->when($request->date_to, function($query, $date) {
                $query->whereDate('created_at', '<=', $date);
            })
            ->when($request->performance, function($query, $performance) {
                switch($performance) {
                    case 'high':
                        $query->where('average_rating', '>=', 4);
                        break;
                    case 'medium':
                        $query->whereBetween('average_rating', [3, 4]);
                        break;
                    case 'low':
                        $query->where('average_rating', '<', 3);
                        break;
                }
            })
            ->latest()
            ->paginate(10)
            ->through(function($vendor) {
                return [
                    'id' => $vendor->id,
                    'full_name' => $vendor->full_name,
                    'email' => $vendor->email,
                    'store_name' => $vendor->store_name,
                    'business_category' => $vendor->business_category,
                    'status' => $vendor->status,
                    'store_logo' => $vendor->store_logo,
                    'created_at' => $vendor->created_at->format('M d, Y'),
                    'metrics' => [
                        'total_sales' => $vendor->total_sales,
                        'products_count' => $vendor->total_products,
                        'rating' => $vendor->average_rating,
                        'fulfillment_rate' => $vendor->fulfillment_rate . '%',
                        'commission_rate' => $vendor->commission_rate . '%',
                        'pending_payout' => $vendor->pending_payout,
                        'return_rate' => $vendor->return_rate . '%'
                    ],
                    'verification' => [
                        'is_verified' => $vendor->is_verified,
                        'documents' => $vendor->verification_documents ?? []
                    ],
                    'recent_activities' => $vendor->activityLogs->map(fn($log) => [
                        'action' => $log->action,
                        'date' => $log->created_at->diffForHumans(),
                        'details' => $log->details
                    ]),
                    'unread_communications' => $vendor->getUnreadCommunicationsCount()
                ];
            });

        $stats = [
            'total' => Vendor::count(),
            'active' => Vendor::where('status', 'approved')->count(),
            'inactive' => Vendor::where('status', 'suspended')->count(),
            'pending' => Vendor::where('status', 'pending')->count(),
            'newThisMonth' => Vendor::where('status', 'approved')
                ->whereMonth('created_at', now()->month)
                ->count(),
            'metrics' => [
                'average_rating' => number_format(Vendor::avg('average_rating'), 1),
                'total_sales' => '$' . number_format(Vendor::sum('total_sales'), 2),
                'commission_earned' => '$' . number_format(Vendor::sum('total_sales') * 0.1, 2),
                'return_rate' => number_format(Vendor::avg('return_rate'), 1) . '%'
            ]
        ];

        return Inertia::render('Vendors/Index', [
            'vendors' => $vendors,
            'stats' => $stats,
            'filters' => $request->only(['search', 'status', 'date_from', 'date_to', 'performance'])
        ]);
    }

    

    public function create()
    {
        return Inertia::render('Admin/Vendors/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:vendors',
            'store_name' => 'required|string|max:255',
            'business_category' => 'required|string',
            'password' => 'required|min:8|confirmed'
        ]);

        $vendor = Vendor::create([
            ...$validated,
            'password' => bcrypt($validated['password']),
            'status' => 'pending',
            'onboarding_step' => [
                'current_step' => 1,
                'completed_steps' => []
            ]
        ]);

        return redirect()->route('admin.vendors.index')
            ->with('success', 'Vendor created successfully');
    }

    public function show(Vendor $vendor)
    {
        return Inertia::render('Admin/Vendors/Show', [
            'vendor' => $vendor->load('products', 'orders')
        ]);
    }

    // public function updateStatus(Request $request, Vendor $vendor)
    // {
    //     $validated = $request->validate([
    //         'status' => 'required|in:pending,approved,suspended',
    //         'rejection_reason' => 'required_if:status,suspended'
    //     ]);

    //     $vendor->update($validated);

    //     return back()->with('success', 'Vendor status updated');
    // }

    public function updateStatus(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,suspended,rejected',
            'rejection_reason' => 'required_if:status,rejected'
        ]);

        $oldStatus = $vendor->status;
        $vendor->update($validated);

        // Log activity
        $vendor->logActivity('status_updated', [
            'old_status' => $oldStatus,
            'new_status' => $validated['status'],
            'reason' => $validated['rejection_reason'] ?? null
        ]);

        // Send communication
        if ($validated['status'] === 'approved') {
            $vendor->sendCommunication(
                'notification',
                'Account Approved',
                'Your vendor account has been approved. You can now start selling.'
            );
        }

        return back()->with('success', 'Vendor status updated successfully');
    }

    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'vendor_ids' => 'required|array',
            'action' => 'required|in:approve,suspend,delete',
            'reason' => 'required_if:action,suspend'
        ]);

        foreach($validated['vendor_ids'] as $id) {
            $vendor = Vendor::find($id);
            if(!$vendor) continue;

            switch($validated['action']) {
                case 'approve':
                    $vendor->update(['status' => 'approved']);
                    break;
                case 'suspend':
                    $vendor->update([
                        'status' => 'suspended',
                        'rejection_reason' => $validated['reason']
                    ]);
                    break;
                case 'delete':
                    $vendor->delete();
                    break;
            }
        }

        return back()->with('success', 'Bulk action completed successfully');
    }


}
