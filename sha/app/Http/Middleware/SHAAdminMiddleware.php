<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class SHAAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('hospital.login');
        }

        $user = Auth::user();
        
        if (!$user->role || !in_array($user->role->name, ['SHA Admin', 'ISA Admin'])) {
            auth()->logout();
            return redirect()->route('hospital.login')->withErrors(['error' => 'Unauthorized Access']);
        }

        if ($user->role->name == 'ISA Admin' && !$request->is('isa-admin/*')) {
            return redirect('/isa-admin/dashboard');
        }
    
        if ($user->role->name == 'SHA Admin' && !$request->is('sha-admin/*')) {
            return redirect('/sha-admin/dashboard');
        }

        if (Auth::check()) {
            return $next($request);
        }
    }
}
