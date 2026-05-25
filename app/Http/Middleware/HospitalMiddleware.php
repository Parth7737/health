<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class HospitalMiddleware
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
        if (Auth::check()) {
            
            if(Auth::user()->hasRole('Administrator') && auth()->user()->is_complete_registration == 0){
                return redirect(route('hospital.empanelmentRegistration.create'));
            }else{
                if (Auth::user()->hospital_id) {
                    return $next($request);
                } else {
                    auth()->logout();
                }
            }
        }
        return redirect('/');
    }
}
