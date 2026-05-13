<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class MedicalCommitteeMiddleware
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
        if(Auth::user() && Auth::user()->role_id != 20){
            auth()->logout();
        }
        if (Auth::check()) {
            return $next($request);
        }
        return redirect()->route('hospital.login');;
    }
}
