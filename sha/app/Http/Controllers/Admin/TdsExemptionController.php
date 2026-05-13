<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TdsExemption;
use Illuminate\Http\Request;

class TdsExemptionController extends Controller
{
    public function index()
    {
        $tdsExemptions = TdsExemption::all();
        return view('admin-views.tds_exemptions.index', compact('tdsExemptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:tds_exemptions,name',
        ]);

        TdsExemption::create(['name' => $request->name]);

        return response()->json(['msg' => 'TDS Exemption added successfully.']);
    }

    public function show(TdsExemption $tdsExemption)
    {
        return response()->json(['data' => $tdsExemption]);
    }

    public function update(Request $request, TdsExemption $tdsExemption)
    {
        $request->validate([
            'name' => 'required|unique:tds_exemptions,name,' . $tdsExemption->id,
        ]);

        $tdsExemption->update(['name' => $request->name]);

        return response()->json(['msg' => 'TDS Exemption updated successfully.']);
    }

    public function destroy(TdsExemption $tdsExemption)
    {
        $tdsExemption->delete();

        return redirect()->back()->with('success', 'TDS Exemption deleted successfully.');
    }
}
