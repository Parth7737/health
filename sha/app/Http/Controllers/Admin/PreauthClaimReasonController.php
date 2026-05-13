<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PreauthClaimReason;
use Illuminate\Http\Request;

class PreauthClaimReasonController extends Controller
{
    public function index()
    {
        $preauthClaimReasons = PreauthClaimReason::all();
        return view('admin-views.preauth_claim_reasons.index', compact('preauthClaimReasons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Reject,Query',
        ]);

        PreauthClaimReason::create($request->all());

        return response()->json(['msg' => 'Preauth Claim Reason Created Successfully']);
    }

    public function show($id)
    {
        $preauthClaimReason = PreauthClaimReason::findOrFail($id);
        return response()->json(['data' => $preauthClaimReason]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Reject,Query',
        ]);

        $preauthClaimReason = PreauthClaimReason::findOrFail($id);
        $preauthClaimReason->update($request->all());

        return response()->json(['msg' => 'Preauth Claim Reason Updated Successfully']);
    }

    public function destroy($id)
    {
        $preauthClaimReason = PreauthClaimReason::findOrFail($id);
        $preauthClaimReason->delete();

        return redirect()->back()->with('success', 'Preauth Claim Reason Deleted Successfully');
    }
}
