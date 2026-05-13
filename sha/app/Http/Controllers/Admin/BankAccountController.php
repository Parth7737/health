<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HospitalState;
use App\Models\StateBankDetail;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index()
    {
        $states = HospitalState::all();
        $banks = StateBankDetail::all();
        return view('admin-views.banks.index', compact('banks', 'states'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'state_id' => 'required|integer',
            'bank_name' => 'required|string',
            'ifsc_code' => 'required|string',
            'account_name' => 'required|string',
            'account_number' => 'required|string',
        ]);

        StateBankDetail::create($request->all());

        return response()->json(['msg' => 'Bank added successfully!']);
    }

    public function show($id)
    {
        $bank = StateBankDetail::findOrFail($id);
        return response()->json(['data' => $bank]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'state_id' => 'required|integer',
            'bank_name' => 'required|string',
            'ifsc_code' => 'required|string',
            'account_name' => 'required|string',
            'account_number' => 'required|string',
        ]);

        $StateBankDetail = StateBankDetail::findOrFail($id);
        $StateBankDetail->update($request->all());

        return response()->json(['msg' => 'Bank updated successfully!']);
    }

    public function destroy($id)
    {
        $StateBankDetail = StateBankDetail::findOrFail($id);
        $StateBankDetail->delete();

        return redirect()->back()->with('success', 'Bank deleted successfully!');
    }
}
