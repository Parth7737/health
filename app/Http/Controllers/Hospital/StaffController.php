<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\Staff;
use App\Models\User;
use App\Models\HrDepartment;
use App\Models\HrDesignation;
use App\Models\HrSpecialist;
use App\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use League\Csv\Reader;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class StaffController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-staff', ['only' => ['store']]);
        $this->middleware('permission:edit-staff', ['only' => ['update']]);
        $this->middleware('permission:delete-staff', ['only' => ['destroy']]);
        $this->routes = [
            'destroy' => route('hospital.hr.staff.destroy', ['staff' => '__STAFF__']),            
            'store'   => route('hospital.hr.staff.store'),   
            'loadtable'   => route('hospital.hr.staff-load'),
            'showform'   => route('hospital.hr.staff.showform'),
            'load-units'   => route('hospital.load-units'),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::hospitalRole()->get();
        return view('hospital.hr.staff.index', ['pathurl' => 'staff', 'routes' => $this->routes, 'roles' => $roles]);
    }

    /**
     * Load staff data for DataTable
     */
    public function loaddata(Request $request)
    {
        $data = Staff::select(['id','hr_department_id','hr_designation_id','hr_specialist_id','role_id', 'staff_id', 'first_name', 'last_name', 'email', 'phone', 'image', 'status'])
            ->with(['designation', 'department','role']);

        // Apply filters if provided
        if ($request->has('role_filter') && !empty($request->role_filter)) {
            $data->where('role_id', $request->role_filter);
        }

        if ($request->has('keyword') && !empty($request->keyword)) {
            $keyword = $request->keyword;
            $data->where(function ($query) use ($keyword) {
                $query->where('first_name', 'like', "%{$keyword}%")
                    ->orWhere('last_name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('phone', 'like', "%{$keyword}%")
                    ->orWhere('staff_id', 'like', "%{$keyword}%");
            });
        }

        return DataTables::of($data)
            ->addColumn('photo', function ($row) {
                $imagePath = $row->image ? url('public/storage/' . $row->image) : asset('front/assets/images/dashboard/profile.png');
                return '<img src="' . $imagePath . '" class="rounded-circle staff-avatar" width="48" height="48">';
            })
            ->addColumn('image', function ($row) {
                $imagePath = $row->image ? url('public/storage/' . $row->image) : asset('front/assets/images/dashboard/profile.png');
                return $imagePath;
            })
            ->addColumn('full_name', function ($row) {
                return $row->first_name . ' ' . $row->last_name;
            })
            ->addColumn('department_name', function ($row) {
                return $row->department ? $row->department->name : '<span class="badge bg-secondary">N/A</span>';
            })
            ->addColumn('designation_name', function ($row) {
                return $row->designation ? $row->designation->name : '<span class="badge bg-secondary">N/A</span>';
            })
            ->addColumn('status_badge', function ($row) {
                $statusClass = $row->status === 'Active' ? 'bg-success' : 'bg-danger';
                return '<span class="badge ' . $statusClass . '">' . $row->status . '</span>';
            })
            ->addColumn('actions', function ($row) {
                return view('hospital.hr.staff.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['photo', 'department_name', 'designation_name', 'status_badge', 'actions'])
            ->make(true);
    }

    /**
     * Show the form for creating or editing a staff member
     */
    public function showform(Request $request)
    {
        $id = $request->id;
        $data = null;
        $departments = HrDepartment::where('hospital_id', $this->hospital_id)->get();
        $designations = HrDesignation::where('hospital_id', $this->hospital_id)->get();
        $specialists = HrSpecialist::where('hospital_id', $this->hospital_id)->get();
        $roles = Role::hospitalRole()->get();
        $superiors = Staff::where('hospital_id', $this->hospital_id)
            ->select('id', 'first_name', 'last_name')
            ->get();

        if ($id) {
            $data = Staff::findOrFail($id);
        }

        return view('hospital.hr.staff.form', compact('data', 'id', 'departments', 'designations', 'specialists', 'roles', 'superiors'));
    }

    /**
     * Store a newly created or update an existing resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . ($request->id ? Staff::find($request->id)?->user_id : 'NULL'), 
            'phone' => 'required|numeric|digits:10',
            'date_of_birth' => 'required|date|before:today',
            'date_of_joining' => 'required|date',
            'gender' => 'required|in:Male,Female',
            'blood_group' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'marital_status' => 'nullable|in:Single,Married,Divorced,Not Specified',
            'role_id' => [
                'required',
                Rule::exists('roles', 'id')->where(function ($query) {
                    $query->where('name', '!=', 'Master Admin')
                        ->where(function ($q) {
                            $q->whereNull('hospital_id')
                                ->orWhere('hospital_id', $this->hospital_id);
                        });
                }),
            ],
            'hr_department_id' => 'required|exists:hr_departments,id',
            'hr_department_unit_id' => 'nullable|exists:hr_department_units,id',
            'hr_designation_id' => 'nullable|exists:hr_designations,id',
            'hr_specialist_id' => 'nullable|exists:hr_specialists,id',
            'current_address' => 'required|string',
            'permanent_address' => 'nullable|string',
            'qualifications' => 'nullable|string',
            'work_experience' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:Active,Inactive',
            'work_timings' => 'nullable|array',
            'work_timings.*.start_time' => 'required_with:work_timings|string',
            'work_timings.*.end_time' => 'required_with:work_timings|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        try {
            $staff = null;

            if ($request->id) {
                // Update existing staff
                $staff = Staff::findOrFail($request->id);
                
                // Update user if email changed
                if ($staff->user && $staff->user->email !== $request->email) {
                    $staff->user->update(['email' => $request->email]);
                }
                $staff->user->update(['name' => $request->first_name . ' ' . $request->last_name]);
            } else {
                // Create new user account
                $password = Str::random(6);
                $user = User::create([
                    'name' => $request->first_name . ' ' . $request->last_name,
                    'email' => $request->email,
                    'password' => Hash::make(123456),
                    'hospital_id' => $this->hospital_id,
                    'userid' => $this->generateUserId(),
                ]);
                $role = Role::hospitalRole()->findOrFail($request->role_id);
                $user->assignRole($role);
                // Store password in session for display
                session(['staff_password' => $password]);
            }

            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                if($request->id && $staff->image) {
                    // Delete old image if exists
                    Storage::disk('public')->delete($staff->image);
                }
                $imagePath = $request->file('image')->store('staff', 'public');
            } elseif ($request->id && $staff->image) {
                $imagePath = $staff->image;
            }

            // Handle work timings
            $workTimings = null;
            if ($request->has('work_timings') && is_array($request->work_timings)) {
                $workTimings = json_encode($request->work_timings);
            }

            // Prepare staff data
            $staffData = [
                'hospital_id' => $this->hospital_id,
                'role_id' => $request->role_id,
                'hr_department_id' => $request->hr_department_id,
                'hr_department_unit_id' => $request->hr_department_unit_id,
                'hr_designation_id' => $request->hr_designation_id,
                'hr_specialist_id' => $request->hr_specialist_id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'father_name' => $request->father_name,
                'mother_name' => $request->mother_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'date_of_birth' => Carbon::parse($request->date_of_birth)->format('Y-m-d'),
                'date_of_joining' => Carbon::parse($request->date_of_joining)->format('Y-m-d'),
                'gender' => $request->gender,
                'blood_group' => $request->blood_group,
                'marital_status' => $request->marital_status,
                'current_address' => $request->current_address,
                'permanent_address' => $request->permanent_address,
                'qualifications' => $request->qualifications,
                'work_experience' => $request->work_experience,
                'specialization' => $request->specialization,
                'note' => $request->note,
                'pto' => $request->pto ?? 0,
                'vacation' => $request->vacation ?? 0,
                'fmla' => $request->fmla ?? 0,
                'education_leave' => $request->education_leave ?? 0,
                'test_leave' => $request->test_leave ?? 0,
                'bank_account_number' => $request->bank_account_number,
                'bank_name' => $request->bank_name,
                'bank_branch' => $request->bank_branch,
                'bank_ifsc_code' => $request->bank_ifsc_code,
                'bank_account_holder_name' => $request->bank_account_holder_name,
                'status' => $request->status,
                'slot_duration' => $request->slot_duration
            ];

            if ($imagePath) {
                $staffData['image'] = $imagePath;
            }

            if ($workTimings !== null) {
                $staffData['work_timings'] = $workTimings;
            }

            if ($request->id) {
                $staff->update($staffData);
            } else {
                $staffData['staff_id'] = $this->generateStaffId();
                $staffData['user_id'] = $user->id;
                $staff = Staff::create($staffData);
            }

            $msg = $request->id ? 'Staff updated successfully.' : 'Staff created successfully.';
            return response()->json([
                'status' => true, 
                'message' => $msg,
                'password' => session('staff_password') ?? null
            ]);

        } catch (\Exception $e) {
            \Log::error('Staff creation/update error: ' . $e->getMessage());
            return response()->json([
                'status' => false, 
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Staff $staff)
    {
        try {
            
            if($staff->image) {
                // Delete old image if exists
                Storage::disk('public')->delete($staff->image);
            }
            if ($staff->user) {
                $staff->user->delete();
            }

            $staff->delete();

            return response()->json(['status' => true, 'message' => 'Staff deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error deleting staff.'], 500);
        }
    }

    /**
     * Generate a unique staff ID for the hospital
     */
    private function generateStaffId()
    {
        $hospital = \App\Models\Hospital::find($this->hospital_id);
        $hospitalCode = strtoupper(substr($hospital->name, 0, 3));
        
        $lastStaff = Staff::where('hospital_id', $this->hospital_id)
            ->latest('id')
            ->first();

        $nextNumber = $lastStaff ? intval(substr($lastStaff->staff_id, -4)) + 1 : 1;
        $staffId = $hospitalCode . '-STF-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // Ensure uniqueness
        while (Staff::where('staff_id', $staffId)->exists()) {
            $nextNumber++;
            $staffId = $hospitalCode . '-STF-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        }

        return $staffId;
    }

    /**
     * Generate a unique user ID
     */
    private function generateUserId()
    {
        $userId = 'STF' . time() . random_int(1000, 9999);
        
        while (User::where('userid', $userId)->exists()) {
            $userId = 'STF' . time() . random_int(1000, 9999);
        }

        return $userId;
    }
    /**
     * Return units for a given department (AJAX)
     */
    public function loadDoctors(Request $request)
    {
        $staff = Staff::where('hr_department_id', $request->hr_department_id)->doctor()->active()
                ->get(['id','first_name','last_name'])->map(function($row){
                    $row->full_name = $row->full_name;
                    return $row;
                });
        return response()->json($staff);
    }
    public function loadDoctorSlots(Request $request){
        $staff = Staff::where('id', $request->doctor_id)->doctor()->active()->first();
        $current_time = date('H:i');
        if($staff){
            $timings = json_decode($staff->work_timings, true);
            $currentDay = strtolower(date('l')); // e.g., 'tuesday'
            $dayTiming = null;
            foreach ($timings as $day => $timing) {
                if ($day && strtolower($day) === $currentDay) {
                    $dayTiming = $timing;
                    break;
                }
            }
            if ($dayTiming) {
                $slotDuration = $staff->slot_duration ?: 20;
                $currentTime = now();
                $startTime = Carbon::createFromFormat('H:i', $dayTiming['start_time'])->setDateFrom($currentTime);
                $endTime = Carbon::createFromFormat('H:i', $dayTiming['end_time'])->setDateFrom($currentTime);
                $slots = [];
                $currentSlot = $startTime->copy();
                while ($currentSlot->lt($endTime)) {
                    // only include slots that are not before the current time
                    if ($currentSlot->gte($currentTime)) {
                        $slotStart = $currentSlot->format('h:i A');
                        // calculate slot end but do not go beyond the defined endTime
                        $nextSlot = $currentSlot->copy()->addMinutes($slotDuration);
                        $slotEnd = $nextSlot->gt($endTime) ? $endTime->format('h:i A') : $nextSlot->format('h:i A');

                        // push an object with start and end timestamps
                        $slots[] = [
                            'start' => $slotStart,
                            'end'   => $slotEnd,
                        ];
                    }
                    $currentSlot->addMinutes($slotDuration);
                }
                return response()->json($slots);
            }
        }
        return response()->json([]);
    }
}

