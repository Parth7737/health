<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PreauthRejectReason;
use Illuminate\Http\Request;

class PreauthRejectReasonController extends Controller
{
    public function index()
    {
        $reasons = PreauthRejectReason::all();
        return view('admin-views.preauth_reject_reasons.index', compact('reasons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        PreauthRejectReason::create($request->all());

        return response()->json(['msg' => 'Preauth Reject Reason Added Successfully']);
    }

    public function show(PreauthRejectReason $preauthRejectReason)
    {
        return response()->json(['data' => $preauthRejectReason]);
    }

    public function update(Request $request, PreauthRejectReason $preauthRejectReason)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $preauthRejectReason->update($request->all());

        return response()->json(['msg' => 'Preauth Reject Reason Updated Successfully']);
    }

    public function destroy(PreauthRejectReason $preauthRejectReason)
    {
        $preauthRejectReason->delete();

        return redirect()->back()->with('success', 'Preauth Reject Reason Deleted Successfully');
    }
}
