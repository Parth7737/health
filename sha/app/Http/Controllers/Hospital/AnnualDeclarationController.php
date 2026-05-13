<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Hospitals, AnnualDeclaration};

class AnnualDeclarationController extends Controller
{
    public function index(Request $request, $uuid) {
        $hospital = Hospitals::where('uuid', base64_decode($uuid))->first();

        return view('hospital.annualdeclaration.index', compact('hospital'));
    }

    public function savedeclaration(Request $request, $uuid, $hospitalId) {
        $hospital = Hospitals::where('uuid', base64_decode($uuid))->first();

        $validatedData = $request->validate([
            'is_accept' => 'required',
        ]);

        $array = [
            'year' => date('Y'),
            'submitted_date' => date('Y-m-d'),
            'hospital_id' => $hospital->id,
            'status' => 1
        ];

        $hospital->annualdeclaration()->updateOrCreate(
            ['hospital_id' => $hospital->id, 'year' => date('Y')],
            $array
        );

        return response()->json(['success' => true, 'message' => 'Annual Declaration Submitted Successfully!!']);
    }
}
