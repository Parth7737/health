<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseHospitalController extends Controller
{
    protected $hospital_id;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->hospital_id = auth()->user()->hospital_id;
            return $next($request);
        });
    }
}