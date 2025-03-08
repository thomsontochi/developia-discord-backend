<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // // return $next($request);
        // if (!auth()->check() || auth()->user()->role !== 'admin') {
        //     return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        // }

        // return $next($request);

        \Log::info('Admin middleware check', [
            'is_authenticated' => auth()->check(),
            'user' => auth()->user(),
            'role' => auth()->user()?->role
        ]);

        if (!auth()->check() || !in_array(auth()->user()->role, ['super_admin', 'admin'])) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            return redirect('/')->with('error', 'Unauthorized access');
        }
    
        return $next($request);
    }
}
