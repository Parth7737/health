<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PreauthCancelReason;

class PreauthCancelReasonController extends Controller
{
    public function index()
    {
        $reasons = PreauthCancelReason::all();
        return view('admin-views.preauth_cancel_reasons.index', compact('reasons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:preauth_cancel_reasons,name',
        ]);

        PreauthCancelReason::create(['name' => $request->name]);

        return response()->json(['msg' => 'Preauth Cancel Reason Added Successfully.']);
    }

    public function show(PreauthCancelReason $preauthCancelReason)
    {
        return response()->json(['data' => $preauthCancelReason]);
    }

    public function update(Request $request, PreauthCancelReason $preauthCancelReason)
    {
        $request->validate([
            'name' => 'required|unique:preauth_cancel_reasons,name,' . $preauthCancelReason->id,
        ]);

        $preauthCancelReason->update(['name' => $request->name]);

        return response()->json(['msg' => 'Preauth Cancel Reason Updated Successfully.']);
    }

    public function destroy(PreauthCancelReason $preauthCancelReason)
    {
        $preauthCancelReason->delete();
        return redirect()->back()->with('success', 'Preauth Cancel Reason Deleted.');
    }
}
