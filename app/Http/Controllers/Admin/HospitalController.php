<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Hospital, User};
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\CentralLogics\Helpers;
use Yajra\DataTables\Facades\DataTables;
use Auth;

class HospitalController extends Controller
{
    public $routes = [];
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user() || !auth()->user()->hasRole('Master Admin')) {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        });

        $this->routes = [
            'destroy' => route('admin.hospitals.destroy', ['hospital' => '__HOSPITAL__']),            
            'store'   => route('admin.hospitals.store'),
            'loadtable'   => route('admin.hospitalload'),
            'viewstatusmodal'   => route('admin.viewstatusmodal'),
            'changestatus'   => route('admin.changestatus'),
            'approve'   => route('admin.hospitals.approve', ['hospital' => '__HOSPITAL__']),
            'reject'   => route('admin.hospitals.reject', ['hospital' => '__HOSPITAL__']),
            'autoin'   => route('admin.autoin'),
        ];
    }

    public function index()
    {
        $pathurl = 'hospitals';
        $routes = $this->routes;
        return view('admin-views.hospitals.hospital', compact('pathurl', 'routes'));
    }
    
    public function loadhospital(Request $request)
    {
        $data = Hospital::with('user')->select(['id', 'name', 'email', 'phone', 'status', 'hospital_type', 'user_id', 'is_approve']);
        return DataTables::of($data)
            ->addColumn('hospital_type', function ($row) {
                return @$row->hospital_type ? @$row->hospital_type : '';
            })
            ->addColumn('parent_id', function ($row) {
                return @$row->parent ? @$row->parent->name :'-';
            })
            ->addColumn('status', function ($row) {
                $status = '';
                if($row->status == 'Draft') {
                    // loadmodal" data-targetid="' . $row->id . ' data-status="0" style="cursor:pointer;"
                    $status = '<span class="badge badge-warning " >Draft</span>';
                } else if($row->status == 'Submitted') {
                    $status = '<span class="badge badge-info " >Submitted</span>';
                } else if($row->status == 'Approved') {
                    $status = '<span class="badge badge-success">Approved</span>';
                } else if($row->status == 'Rejected') {
                    $status = '<span class="badge badge-danger">Rejected</span>';
                } else {
                    $status = '<span class="badge badge-danger">'.$row->status.'</span>';
                }
                return $status;
            })
            ->addColumn('actions', function ($row) {
                return view('admin-views.hospitals.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions', 'status'])
            ->make(true);
    }

    public function edit($id)
    {
        $hospital = Hospital::where('id',$id)->first();
        $pathurl = 'hospitals';
        $routes = $this->routes;
        return view('admin-views.hospitals.show', compact('hospital','pathurl','routes'));
    }

    public function show($id)
    {
        $hospital = Hospital::where('id',$id)->with('documents','documents.doc:id,name','specialities','specialities.speciality:id,name,code','services','services.action','services.subService:id,name','licenses','licenses.licenseType:id,name')->first();
        return view('admin-views.hospitals.show', compact('hospital'));
    }

    public function viewstatusmodal(Request $request) {
        $id = $request->id;
        $status = $request->status;

        return view('admin-views.hospitals.partials.statusmodal', compact('id'));
    }

    public function changestatus(Request $request) {
        $request->validate([
            'status' => 'required',
            'hospital_id' => 'required',
        ]);
        $hospital = Hospital::where('id',$request->hospital_id)->first();
        if($hospital) {
            $hospital->status = $request->status;
            $hospital->save();
            if($request->status == 'Approved'){
                $user = $hospital->hospital_admin;
                $user->is_complete_registration = 1;
                $user->save();
            }
            return response()->json(['status' => true, 'message' => 'Hospital Status Change Successfully!!']);
        } else {
            return response()->json(['status' => false, 'message' => 'Hospital Not Found!!']);
        }
    }


    public function approve(Request $request, $hospital)
    {
        $hospital = Hospital::where('id', $hospital)->first();
        if (!$hospital) {
            return response()->json(['status' => false, 'message' => 'Hospital Not Found!!']);
        }
        if ($hospital->status !== 'Submitted') {
            return response()->json(['status' => false, 'message' => 'Only Submitted hospitals can be approved.']);
        }
        $hospital->status = 'Approved';
        $hospital->is_approve = 1;
        $hospital->reject_reason = null;
        $hospital->status_update_date = now();
        $hospital->save();

        $hospital->user->is_complete_registration = 1;
        $hospital->user->save();
        return response()->json(['status' => true, 'message' => 'Hospital Approved Successfully!!']);
    }

    public function reject(Request $request, $hospital)
    {
        $request->validate([
            'reject_reason' => 'required|string|max:1000',
        ]);
        $hospital = Hospital::where('id', $hospital)->first();
        if (!$hospital) {
            return response()->json(['status' => false, 'message' => 'Hospital Not Found!!']);
        }
        if ($hospital->status !== 'Submitted') {
            return response()->json(['status' => false, 'message' => 'Only Submitted hospitals can be rejected.']);
        }
        $hospital->status = 'Rejected';
        $hospital->is_approve = 2;
        $hospital->reject_reason = $request->reject_reason;
        $hospital->status_update_date = now();
        $hospital->save();
        return response()->json(['status' => true, 'message' => 'Hospital Rejected Successfully!!']);
    }

    public function destroy(Request $request, $id) {
        
        $hospital = Hospital::where('id',$id)->first();
        if($hospital) {
            $hospital->delete();
            return response()->json(['status' => true, 'message' => 'Hospital deleted successfully!!']);
        } else {
            return response()->json(['status' => false, 'message' => 'Hospital Not Found!!']);
        }
    }

    public function permission(Request $request, $id) {
        $hospital = Hospital::where('id',base64_decode($id))->first();

        return view('admin-views.hospitals.permission', ['pathurl' => 'hospitals', 'routes' => $this->routes, 'hospital' => $hospital]);
    }

    public function autoin(Request $request) {
        try {

            $id = base64_decode($request->id);

            $user = User::find($id);

            if ($user) {

                Auth::logout(); 
                Auth::login($user);

                $url = Helpers::getDashboardRedirect($user);

                return response()->json([
                    'status' => true,
                    'message' => 'Login successfully!',
                    'url' => $url
                ]);

            } else {

                return response()->json([
                    'status' => false,
                    'message' => 'User not found!'
                ]);
            }

        } catch (\Exception $e) {
            Log::error("Auto login failed: " . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Auto login failed!!'
            ]);
        }
    }
}
