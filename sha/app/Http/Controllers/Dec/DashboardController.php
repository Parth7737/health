<?php

namespace App\Http\Controllers\Dec;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Validator;
use App\Models\Hospitals;
use DataTables;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    { 
       

        return view('dec.index');
    }

    public function loadstatitacs(Request $request) {
        $district = auth()->user()->district;

        // Base hospital query
        $baseQuery = Hospitals::with('hospitalAddress')->whereHas('hospitalAddress', function ($query) use ($district) {
            $query->where('district', $district);
        });
    
        // Apply optional filters to a query clone
        $filterQuery = function ($query) use ($request) {
            if ($request->scheme_id) {
                $query->where('scheme', $request->scheme_id);
            }
            if ($request->facility_type) {
                $query->where('facility_type', $request->facility_type);
            }
            return $query;
        };
    
        // Create counts with filters applied
        $empanelled = $filterQuery(clone $baseQuery)->where('status', 'Empanelled')->count();
        $submitted = $filterQuery(clone $baseQuery)->whereIn('status', ['Submitted', 'Re-Submitted'])->count();
        $queried = $filterQuery(clone $baseQuery)->whereIn('status', ['Response Required From Facility', 'Queried'])->count();
        $qryreplied = $filterQuery(clone $baseQuery)->where('status', 'Query Replied')->count();
        $upgradation = $filterQuery(clone $baseQuery)->where('status', 'Upgradation Request')->count();
        $rejected = $filterQuery(clone $baseQuery)->whereIn('status', ['Empanelment Not Recommended by DEC', 'Rejected'])->count();
        $recommendedbydec = $filterQuery(clone $baseQuery)->where('status', 'Empanelment Recommended by DEC')->count();
        $queriedbysec = $filterQuery(clone $baseQuery)->where('status', 'Query Raised by SEC')->count();
        $approveupgradationrequest = $filterQuery(clone $baseQuery)->where('status', 'Approved Upgradation Request')->count();
        $queryupgradationrequest = $filterQuery(clone $baseQuery)->where('status', 'Query On Upgradation Request From Facility')->count();
        $rejectupgradationrequest = $filterQuery(clone $baseQuery)->where('status', 'Rejected Upgradation Request')->count();
    
        return view('dec.loadstatitacs', compact(
            'empanelled', 'submitted', 'queried', 'upgradation', 'rejected',
            'recommendedbydec', 'queriedbysec', 'qryreplied',
            'approveupgradationrequest', 'queryupgradationrequest', 'rejectupgradationrequest'
        ));
    }

    public function worklist() {
        $district = auth()->user()->district;
        $empanelled = Hospitals::with('hospitalAddress')->whereHas('hospitalAddress', function ($query) use ($district) {
            $query->where('district', $district);
        })->where('status', 'Empanelled')->count();

        $submitted = Hospitals::with('hospitalAddress')->whereHas('hospitalAddress', function ($query) use ($district) {
            $query->where('district', $district);
        })->whereIn('status', ['Submitted', 'Re-Submitted'])->count();

        $qryreplied = Hospitals::with('hospitalAddress')->whereHas('hospitalAddress', function ($query) use ($district) {
            $query->where('district', $district);
        })->whereIn('status', ['Query Replied'])->count();

        $queried = Hospitals::with('hospitalAddress')->whereHas('hospitalAddress', function ($query) use ($district) {
            $query->where('district', $district);
        })->whereIn('status', ['Response Required From Facility', 'Queried'])->count();

        $upgradation = Hospitals::with('hospitalAddress')->whereHas('hospitalAddress', function ($query) use ($district) {
            $query->where('district', $district);
        })->where('status', 'Upgradation Request')->count();

        $rejected = Hospitals::with('hospitalAddress')->whereHas('hospitalAddress', function ($query) use ($district) {
            $query->where('district', $district);
        })->whereIn('status', ['Empanelment Not Recommended by DEC', 'Rejected'])->count();

        $recommendedbydec = Hospitals::with('hospitalAddress')->whereHas('hospitalAddress', function ($query) use ($district) {
            $query->where('district', $district);
        })->where('status', 'Empanelment Recommended by DEC')->count();

        $queriedbysec = Hospitals::with('hospitalAddress')->whereHas('hospitalAddress', function ($query) use ($district) {
            $query->where('district', $district);
        })->where('status', 'Query Raised by SEC')->count();
        
        $approveupgradationrequest = Hospitals::with('hospitalAddress')->whereHas('hospitalAddress', function ($query) use ($district) {
            $query->where('district', $district);
        })->where('status', 'Approved Upgradation Request')->count();

        $queryupgradationrequest = Hospitals::with('hospitalAddress')->whereHas('hospitalAddress', function ($query) use ($district) {
            $query->where('district', $district);
        })->where('status', 'Query On Upgradation Request From Facility')->count();

        $rejectupgradationrequest = Hospitals::with('hospitalAddress')->whereHas('hospitalAddress', function ($query) use ($district) {
            $query->where('district', $district);
        })->where('status', 'Rejected Upgradation Request')->count();  

        return view('dec.worklist', compact('empanelled', 'submitted', 'queried', 'upgradation', 'rejected', 'recommendedbydec', 'queriedbysec', 'qryreplied', 'approveupgradationrequest', 'queryupgradationrequest', 'rejectupgradationrequest'));
    }

    public function getFacilityData(Request $request)
    {
        $district = auth()->user()->district;
        if ($request->ajax()) {
            $data = Hospitals::with('facilityType','hospitalAddress')->whereHas('hospitalAddress', function ($query) use ($district) {
                $query->where('district', $district);
            })->select([
                'id',
                'uuid',
                'hospital_id',
                'facility_name',
                'facility_type',
                'scheme',
                'status',
                'remark',
                'updated_at'
            ])->where('status', '!=', 'Draft')->orderBy('id', 'DESC');

            if(auth()->user()->role_id == 2) {
                $data->where('user_id' , auth()->user()->id);
            }
            
            if (!empty($request->status)) {
                if($request->status == 'Queried') {
                    $data->whereIn('status', [$request->status, 'Response Required From Facility']);
                } else if($request->status == 'Submitted' || $request->status == 'Re-Submitted') {
                    $data->whereIn('status', [$request->status, "Re-Submitted"]);
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
                ->addColumn('updated_at', function ($row) {
                    // Access the related facilityType name
                    return date('d/m/Y', strtotime($row->updated_at));
                })
                // ->addColumn('specialities', function ($row) {
                //     // Access the related facilityType name
                //     return "";
                // })
                ->addColumn('action', function ($row) {
                    $route = route('dec.gethospital', [base64_encode($row->id), base64_encode($row->uuid)]);
                    return '<a href="'.$route.'" class="btn btn-primary btn-sm">></a>';
                })
                ->rawColumns(['action']) // Indicates that the 'action' column contains raw HTML
                ->make(true);
        }
    }
}
