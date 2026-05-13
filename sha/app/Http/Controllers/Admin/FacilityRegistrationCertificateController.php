<?php

namespace App\Http\Controllers\Admin;

use App\Models\FacilityRegistrationCertificate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FacilityRegistrationCertificateController extends Controller
{
    public function index()
    {
        $certificates = FacilityRegistrationCertificate::all();
        return view('admin-views.facility_certificates.index', compact('certificates'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:facility_registration_certificates,name']);
        FacilityRegistrationCertificate::create(['name' => $request->name]);
        return response()->json(['msg' => 'Certificate added successfully.']);
    }

    public function show(FacilityRegistrationCertificate $facilityCertificate)
    {
        return response()->json(['data' => $facilityCertificate]);
    }

    public function update(Request $request, FacilityRegistrationCertificate $facilityCertificate)
    {
        $request->validate(['name' => 'required|unique:facility_registration_certificates,name,' . $facilityCertificate->id]);
        $facilityCertificate->update(['name' => $request->name]);
        return response()->json(['msg' => 'Certificate updated successfully.']);
    }

    public function destroy(FacilityRegistrationCertificate $facilityCertificate)
    {
        $facilityCertificate->delete();
        return redirect()->back()->with('success', 'Certificate deleted successfully.');
    }
}
