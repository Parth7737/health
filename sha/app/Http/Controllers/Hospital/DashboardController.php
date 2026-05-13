<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Validator;
use DataTables;
use App\Models\{Hospitals, AnnualDeclaration, ExpiredDocument, HospitalAccreditation, HospitalLicense, HospitalTeam};
use DB;


class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ExpiredDocument = ExpiredDocument::where('user_id', auth()->user()->id)->where('is_updated', 0)->get();
        return view('hospital.index', compact('ExpiredDocument'));
    }

    public function getFacilityData(Request $request)
    {
        if ($request->ajax()) {
            $data = Hospitals::with('facilityType')->select([
                'id',
                'uuid',
                'facility_name',
                'hospital_id',
                'facility_type',
                'scheme',
                'status',
                'remark',
                'created_at',
                'is_empanelled'
            ])->orderBy('id', 'DESC');

            if(auth()->user()->role_id == 2) {
                $data->where('user_id' , auth()->user()->id);
            }
            
            if (!empty($request->status)) {
                if($request->status == 'Queried') {
                    $data->whereIn('status', [$request->status, 'Response Required From Facility', 'Query On Upgradation Request From Facility']);
                } else if($request->status == 'Submitted' || $request->status == 'Re-Submitted') {
                    $data->whereIn('status', [$request->status, "Re-Submitted"]);
                } else if($request->status == "Rejected") {
                    $data->whereIn('status', [$request->status, 'Empanelment Not Recommended by DEC']);
                } else {
                    $data->where('status', $request->status); // Apply filter if status is provided
                }
            }

            return DataTables::of($data)
                ->addIndexColumn() // Adds a serial number column
                ->addColumn('facility_type', function ($row) {
                    // Access the related facilityType name
                    return $row->facilityType ? $row->facilityType->name : '-';
                })
                ->addColumn('scheme', function ($row) {
                    // Access the related facilityType name
                    return $row->schemeType ? $row->schemeType->name : '-';
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at ? date('d/m/Y', strtotime($row->created_at)) : '-';
                })
                // ->addColumn('specialities', function ($row) {
                //     // Access the related facilityType name
                //     return "";
                // })
                ->addColumn('action', function ($row) {
                    if($row->status != "Empanelment Not Recommended by DEC" || $row->status != "Empanelment Not Recommended by DEC" || $row->status != "Rejected Upgradation Request") {
                        if($row->is_empanelled == '1' || $row->is_empanelled == '2' || $row->is_empanelled == '3' || $row->is_empanelled == '4' || $row->is_empanelled == '5') {
                            $route = route('hospital.single-empanelment-dashboard', base64_encode($row->uuid));
                        } else {
                            $route = route('hospital.empanelmentRegistration.establismentDetails', base64_encode($row->uuid));
                        }
                        return '<a href="'.$route.'" class="btn btn-primary btn-sm">></a>';
                    }
                })
                ->rawColumns(['action']) // Indicates that the 'action' column contains raw HTML
                ->make(true);
        }
    }

    public function singleDocument(Request $request) {
        $getdata = ExpiredDocument::where('id', $request->id)->first();
        $table = $getdata->document_ref_table;
       
        $data = [];

        if($table == 'hospital_accreditations') {
            $docdata = HospitalAccreditation::with('accred')->where('id', $getdata->document_id)->first();
            $data['document_name'] = $docdata->accred->name;
            $data['document_id'] = $docdata->id;
            $data['hospital_id'] = $getdata->hospital_id;
            $data['expired_doc_id'] = $getdata->id;
            $data['document_expire_date'] = $docdata->valid_till;
            $data['file'] = $docdata->certificate;
        }

        if($table == 'hospital_licenses') {
            $docdata = HospitalLicense::with('licenseType')->where('id', $getdata->document_id)->first();

            $data['document_name'] = $docdata->licenseType->name;
            $data['document_id'] = $docdata->id;
            $data['hospital_id'] = $getdata->hospital_id;
            $data['expired_doc_id'] = $getdata->id;
            $data['document_expire_date'] = $docdata->expiry_date;
            $data['file'] = $docdata->document;
        }

        if($table == 'hospital_teams') {
            $docdata = HospitalTeam::where('id', $getdata->document_id)->first();

            $data['document_name'] = 'Registration Certificate';
            $data['document_id'] = $docdata->id;
            $data['hospital_id'] = $getdata->hospital_id;
            $data['expired_doc_id'] = $getdata->id;
            $data['document_expire_date'] = $docdata->registration_certificate_expiry;
            $data['file'] = $docdata->registration_certificate;
        }

        return view('hospital.empanelment.getdocument', compact('getdata', 'data'));
    }

    public function updateDocument(Request $request) {
        $validatedData = $request->validate([
            'start_date' => 'nullable',
            'expiry_date' => 'required',
            'document' => 'required|mimes:pdf|max:10240',
            'document_expired_id' => 'required',
            'hospital_id' => 'required',
            'document_id' => 'required',
        ]);

        $getdata = ExpiredDocument::where('id', $request->document_expired_id)->first();
        $table = $getdata->document_ref_table;

        if($table == 'hospital_accreditations') {
            $docdata = HospitalAccreditation::with('accred')->where('id', $request->document_id)->first();

            if ($request->hasFile('document')) {
                $filePath = $request->file('document')->store('certificate', 'public'); 
                $docdata->certificate = $filePath;
                $docdata->valid_from = $request->start_date;
                $docdata->valid_till = $request->expiry_date;
                $docdata->save();

                $hospital = Hospitals::where('id', $request->hospital_id)->first();
                $hospital->status = 'Submitted';
                $hospital->save();
                $logarray = [
                    'action' => 'EXPIRED DOCUMENT UPDATE BY HOSPITAL',
                    'remark' => 'The hospital have updated the '.$docdata->accred->name.' expired document',
                    'created_by' => auth()->user()->id
                ];
                Helpers::addWorkflowForHospital($hospital, $logarray);
            }
        }

        if($table == 'hospital_licenses') {
            $docdata = HospitalLicense::with('licenseType')->where('id', $request->document_id)->first();

            if ($request->hasFile('document')) {
                $filePath = $request->file('document')->store('certificate', 'public'); 
                $docdata->document = $filePath;
                $docdata->issue_date = $request->start_date;
                $docdata->expiry_date = $request->expiry_date;
                $docdata->save();

                $hospital = Hospitals::where('id', $request->hospital_id)->first();
                $hospital->status = 'Submitted';
                $hospital->save();
                $logarray = [
                    'action' => 'EXPIRED DOCUMENT UPDATE BY HOSPITAL',
                    'remark' => 'The hospital have updated the '.$docdata->licenseType->name.' expired document',
                    'created_by' => auth()->user()->id
                ];
                Helpers::addWorkflowForHospital($hospital, $logarray);
            }
        }

        if($table == 'hospital_teams') {
            $docdata = HospitalTeam::where('id', $request->document_id)->first();

            if ($request->hasFile('document')) {
                $filePath = $request->file('document')->store('certificate', 'public'); 
                $docdata->registration_certificate = $filePath;
                // $docdata->issue_date = $request->start_date;
                $docdata->registration_certificate_expiry = $request->expiry_date;
                $docdata->save();

                $hospital = Hospitals::where('id', $request->hospital_id)->first();
                $hospital->status = 'Submitted';
                $hospital->save();
                $logarray = [
                    'action' => 'EXPIRED DOCUMENT UPDATE BY HOSPITAL',
                    'remark' => 'The hospital have updated the expired Registration Certificate',
                    'created_by' => auth()->user()->id
                ];
                Helpers::addWorkflowForHospital($hospital, $logarray);
            }
        }
       
        if($getdata) {
            $getdata->is_updated = 1;
            $getdata->save();
            return response()->json(['success' => false, 'message' => 'Document Updated Successfully!!']);
        } else {
            return response()->json(['success' => false, 'message' => 'Something Wrong!!']);
        }

    }

    public function withdraw(Request $request, $uuid) {
        $hospital = Hospitals::where('uuid', base64_decode($uuid))->first();

        return view('hospital.withdraw.index', compact('hospital'));
    }
}
