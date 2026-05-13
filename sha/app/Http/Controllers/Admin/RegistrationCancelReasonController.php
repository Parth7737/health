<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RegistrationCancelReason;
use Illuminate\Http\Request;

class RegistrationCancelReasonController extends Controller
{
    public function index()
    {
        $reasons = RegistrationCancelReason::all();
        return view('admin-views.registration_cancel_reasons.index', compact('reasons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:registration_cancel_reasons,name',
        ]);

        RegistrationCancelReason::create(['name' => $request->name]);
        return response()->json(['msg' => 'Registration Cancel Reason Added Successfully.']);
    }

    public function show(RegistrationCancelReason $registrationCancelReason)
    {
        return response()->json(['data' => $registrationCancelReason]);
    }

    public function update(Request $request, RegistrationCancelReason $registrationCancelReason)
    {
        $request->validate([
            'name' => 'required|unique:registration_cancel_reasons,name,' . $registrationCancelReason->id,
        ]);

        $registrationCancelReason->update(['name' => $request->name]);
        return response()->json(['msg' => 'Registration Cancel Reason Updated Successfully.']);
    }

    public function destroy(RegistrationCancelReason $registrationCancelReason)
    {
        $registrationCancelReason->delete();
        return redirect()->back()->with('success', 'Registration Cancel Reason Deleted.');
    }
}
