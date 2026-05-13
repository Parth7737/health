<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hospitals;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HospitalController extends Controller
{
    public function index()
    {
        $hospitals = Hospitals::latest()->get();
        return view('admin-views.hospitals.hospital', compact('hospitals'));
    }

    public function show($id)
    {
        $hospital = Hospitals::where('id',$id)->with('schemeType')->first();
        return view('admin-views.hospitals.show', compact('hospital'));
    }

   
}
