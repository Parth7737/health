<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmpanelmentDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmpanelmentDocumentController extends Controller
{
    public function index()
    {
        $documents = EmpanelmentDocument::all();
        return view('admin-views.empanelment-documents.index', compact('documents'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:empanelment_documents,name',
            'is_required' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        EmpanelmentDocument::create($request->all());
        return response()->json(['msg' => 'Document Added Successfully.']);
    }

    public function show($id)
    {
        $document = EmpanelmentDocument::findOrFail($id);
        return response()->json(['data' => $document]);
    }

    public function update(Request $request, EmpanelmentDocument $empanelment_document)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:empanelment_documents,name,' . $empanelment_document->id,
            'is_required' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $empanelment_document->update($request->all());
        return response()->json(['msg' => 'Document Updated Successfully.']);
    }

    public function destroy(EmpanelmentDocument $empanelment_document)
    {
        $empanelment_document->delete();
        return redirect()->back()->with('success', 'Document Deleted Successfully.');
    }
}
