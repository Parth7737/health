<?php

namespace App\Http\Controllers\Sec;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Validator;
use App\Models\{ Hospitals, HospitalState};
use DataTables;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    { 
        $state_name = auth()->user()->parent_entity;
        $state = HospitalState::where('name', $state_name)->first();
        $state_id = '';
        if($state) {
            $state_id = $state->id;
        } else {
            $state_id = 34;
        }
      
        return view('sec.index', compact('state_id'));
    }

    public function worklist() {
        $state_name = auth()->user()->parent_entity;
        $state = HospitalState::where('name', $state_name)->first();
        $state_id = '';
        if($state) {
            $state_id = $state->id;
        } else {
            $state_id = 34;
        }
        $empanelled = Hospitals::with('hospitalAddress')->whereHas('hospitalAddress', function ($query) use ($state_id) {
            $query->where('state', $state_id);
        })->where('status', 'Empanelled')->count();

        $submitted = Hospitals::with('hospitalAddress')->whereHas('hospitalAddress', function ($query) use ($state_id) {
            $query->where('state', $state_id);
        })->where('status', 'Empanelment Recommended by DEC')->count();

        $decquery = Hospitals::with('hospitalAddress')->whereHas('hospitalAddress', function ($query) use ($state_id) {
            $query->where('state', $state_id);
        })->where('status', 'Query Raised by SEC')->count();

        // $facilityQuery = Hospitals::with('hospitalAddress')->whereHas('hospitalAddress', function ($query) use ($state_id) {
        //     $query->where('state', $state_id);
        // })->where('status', 'Queried')->count();

        $upgradationRequest =  Hospitals::with('hospitalAddress')->whereHas('hospitalAddress', function ($query) use ($state_id) {
            $query->where('state', $state_id);
        })->where('status', 'Approved Upgradation Request')->count();

        $Withdrawn = Hospitals::with('hospitalAddress')->whereHas('hospitalAddress', function ($query) use ($state_id) {
            $query->where('state', $state_id);
        })->where('status', 'Withdrawn')->count();

        return view('sec.worklist', compact('empanelled', 'submitted', 'decquery', 'Withdrawn', 'upgradationRequest'));
    }

    public function getFacilityData(Request $request)
    {
        $state_name = auth()->user()->parent_entity;
        $state = HospitalState::where('name', $state_name)->first();
        $state_id = '';
        if($state) {
            $state_id = $state->id;
        } else {
            $state_id = 34;
        }

        if ($request->ajax()) {
            $data = Hospitals::with('facilityType','hospitalAddress')->whereHas('hospitalAddress', function ($query) use ($state_id) {
                $query->where('state', $state_id);
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
            ])->where('status', '!=', 'Draft')->where('status', '!=', 'Submitted')->orderBy('id', 'DESC');

            if(auth()->user()->role_id == 2) {
                $data->where('user_id' , auth()->user()->id);
            }
            
            if (!empty($request->status)) {
                $data->where('status', $request->status); // Apply filter if status is provided
            }
            //  else {
            //     $data->whereIn('status', ['Empanelled', 'Rejected', 'Withdrawn', 'In-Active', 'Re-Empanelled', 'Empanelment Recommended by DEC', 'Response Required From Facility', 'Empanelment Not Recommended by DEC']); 
            // }

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
                    $route = route('sec.gethospital', [base64_encode($row->id), base64_encode($row->uuid)]);
                    return '<a href="'.$route.'" class="btn btn-primary btn-sm">></a>';
                })
                ->rawColumns(['action']) // Indicates that the 'action' column contains raw HTML
                ->make(true);
        }
    }

    public function loadstatitacs(Request $request) {

        $state_name = auth()->user()->parent_entity;
        $state = HospitalState::where('name', $state_name)->first();
        $state_id = '';
        if($state) {
            $state_id = $state->id;
        } else {
            $state_id = 34;
        }

        // Base hospital query
        $baseQuery = Hospitals::with('hospitalAddress')->whereHas('hospitalAddress', function ($query) use ($state_id, $request) {
            $query->where('state', $state_id);
            if ($request->district) {
                $query->where('district', $request->district);
            }
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
        $submitted = $filterQuery(clone $baseQuery)->where('status', 'Empanelment Recommended by DEC')->count();
        $decquery = $filterQuery(clone $baseQuery)->where('status', 'Query Raised by SEC')->count();
        $upgradationRequest = $filterQuery(clone $baseQuery)->where('status', 'Approved Upgradation Request')->count();
        $Withdrawn = $filterQuery(clone $baseQuery)->where('status', 'Withdrawn')->count();
       // $facilityQuery = Hospitals::with('hospitalAddress')->whereHas('hospitalAddress', function ($query) use ($state_id) {
        //     $query->where('state', $state_id);
        // })->where('status', 'Queried')->count();
    
        return view('sec.loadstatitacs', compact(
            'empanelled', 'submitted', 'decquery', 'upgradationRequest', 'Withdrawn',
        ));
    }
}
