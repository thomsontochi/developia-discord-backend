<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Order;
use App\Models\Dispute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    

    public function index(Request $request)
    {
        $query = Order::with(['customer', 'vendor', 'orderItems.product'])
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('id', 'LIKE', "%{$search}%")
                        ->orWhereHas('orderItems.product', function ($query) use ($search) {
                            $query->where('name', 'LIKE', "%{$search}%");
                        });
                });
            })
            ->when($request->date_range, function ($query, $dateRange) {
                $dates = explode(',', $dateRange);
                if (count($dates) === 2 && !empty($dates[0]) && !empty($dates[1])) {
                    return $query->whereBetween('created_at', [
                        $dates[0] . ' 00:00:00',
                        $dates[1] . ' 23:59:59'
                    ]);
                }
                return $query;
            });

        $orders = $query->latest()->paginate(10);

        // Get the stats (using the same filter conditions)
        $stats = [
            'total_orders' => Order::count(),
            'total_pending' => Order::where('status', 'pending')->count(),
            'total_completed' => Order::where('status', 'completed')->count(),
            'total_cancelled' => Order::where('status', 'cancelled')->count(),
            'total_revenue' => Order::sum('total_amount'),
        ];
        

        return Inertia::render('Orders/Index', [
            'orders' => $orders,
            'stats' => $stats,
            'filters' => $request->only(['status', 'search', 'date_range'])
        ]);
    }

    public function show(Order $order)
    {
        $order->load([
            'customer',
            'vendor',
            'orderItems.product',
            'disputes',
            'payments'
        ]);

        return Inertia::render('Orders/Show', [
            'order' => $order
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,completed,cancelled'
        ]);

        $order->update([
            'status' => $request->status
        ]);

        return back()->with('success', 'Order status updated successfully');
    }

    public function disputes()
    {
        $disputes = Dispute::with(['order.customer', 'order.vendor'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Admin/Orders/Disputes', [
            'disputes' => $disputes
        ]);
    }

    public function resolveDispute(Request $request, Order $order)
    {
        $request->validate([
            'action' => 'required|in:refund,close',
            'reason' => 'required|string|max:500'
        ]);

        DB::transaction(function () use ($request, $order) {
            // Update dispute status
            $dispute = $order->disputes()->latest()->first();
            $dispute->update([
                'status' => 'resolved',
                'resolved_at' => now(),
                'resolution_notes' => $request->reason
            ]);

            // If action is refund, process the refund
            if ($request->action === 'refund') {
                // Process refund logic here
                $order->update([
                    'status' => 'refunded',
                    'refunded_at' => now()
                ]);
            }
        });

        return back()->with('success', 'Dispute resolved successfully');
    }

    public function export(Request $request)
    {
        $orders = Order::with(['customer', 'vendor'])
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->get();

        // Implementation for CSV export
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="orders.csv"',
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Order ID', 'Customer', 'Vendor', 'Status', 'Amount', 'Date']);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->id,
                    $order->customer->name,
                    $order->vendor->name,
                    $order->status,
                    $order->total_amount,
                    $order->created_at->format('Y-m-d')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}