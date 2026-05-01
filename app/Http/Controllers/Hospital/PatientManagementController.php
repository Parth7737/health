<?php

namespace App\Http\Controllers\Hospital;

use App\CentralLogics\Helpers;
use App\Http\Controllers\BaseHospitalController;
use App\Models\Bed;
use App\Models\BedAllocation;
use App\Models\BedStatus;
use App\Models\Allergy;
use App\Models\AllergyReaction;
use App\Models\DiagnosticOrderItem;
use App\Models\Disease;
use App\Models\DoctorSlot;
use App\Models\Habit;
use App\Models\HrDepartment;
use App\Models\IndianDistrict;
use App\Models\IndianState;
use App\Models\OpdPatient;
use App\Models\IpdPrescription;
use App\Models\IpdProgressNote;
use App\Models\OpdPrescription;
use App\Models\Patient;
use App\Models\PatientCategory;
use App\Models\PatientCharge;
use App\Models\PatientPayment;
use App\Models\PatientPaymentAllocation;
use App\Models\PatientTimeline;
use App\Models\Staff;
use App\Models\DoctorOpdCharge;
use App\Models\Tpa;
use App\Models\TpaOpdCharge;
use App\Models\BusinessSetting;
use App\Services\BedAllocationService;
use App\Services\ChargeLedgerService;
use App\Services\PatientTimelineService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PatientManagementController extends BaseHospitalController
{
    // ─────────────────────────────────────────────────────────────────────────
    // VIEWS
    // ─────────────────────────────────────────────────────────────────────────

    public function index()
    {
        $departments = HrDepartment::query()
            ->where('hospital_id', $this->hospital_id)
            ->orderBy('name')
            ->get(['id', 'name']);

        $states = IndianState::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
        return view('hospital.patient-management.index', compact('departments', 'states'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // AJAX – STATS
    // ─────────────────────────────────────────────────────────────────────────

    public function stats()
    {
        $today = now()->toDateString();

        $opdToday = OpdPatient::query()
            ->where('hospital_id', $this->hospital_id)
            ->whereDate('appointment_date', $today)
            ->count();

        $ipdActive = BedAllocation::query()
            ->where('hospital_id', $this->hospital_id)
            ->whereNull('discharge_date')
            ->count();

        $dischargedToday = BedAllocation::query()
            ->where('hospital_id', $this->hospital_id)
            ->whereDate('discharge_date', $today)
            ->count();

        $transferredToday = BedAllocation::query()
            ->where('hospital_id', $this->hospital_id)
            ->whereDate('discharge_date', $today)
            ->where('discharge_status', 'transferred')
            ->count();

        $opdEmergencyPatientIds = OpdPatient::query()
            ->where('hospital_id', $this->hospital_id)
            ->where('casualty', 'Yes')
            ->whereDate('appointment_date', $today)
            ->pluck('patient_id');

        $ipdEmergencyPatientIds = BedAllocation::query()
            ->where('hospital_id', $this->hospital_id)
            ->whereNull('discharge_date')
            ->where('admission_source', 'emergency')
            ->pluck('patient_id');

        $emergency = $opdEmergencyPatientIds
            ->merge($ipdEmergencyPatientIds)
            ->filter()
            ->unique()
            ->count();

        $totalActive = $opdToday + $ipdActive;

        return response()->json([
            'opd_today'        => $opdToday,
            'ipd_active'       => $ipdActive,
            'emergency'        => $emergency,
            'discharged_today' => $dischargedToday,
            'transferred_today' => $transferredToday,
            'total_active'     => $totalActive,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // AJAX – PATIENT LIST (paginated + search + filter)
    // ─────────────────────────────────────────────────────────────────────────

    public function loadPatients(Request $request)
    {
        $q        = $request->input('q', '');
        $filter   = $request->input('filter', '');   // opd | ipd | emergency | discharged
        $deptId   = $request->input('dept_id', '');
        $perPage  = min((int) $request->input('per_page', 20), 100);
        $page     = max(1, (int) $request->input('page', 1));

        $query = Patient::query()
            ->where('patients.hospital_id', $this->hospital_id)
            ->select(
                'patients.id',
                'patients.mrn',
                'patients.patient_id as reg_no',
                'patients.name',
                'patients.age_years',
                'patients.gender',
                'patients.phone',
                'patients.blood_group',
                'patients.created_at'
            );

        // Search
        if ($q !== '') {
            $query->where(function ($sq) use ($q) {
                $sq->where('patients.mrn', 'like', "%{$q}%")
                    ->orWhere('patients.name', 'like', "%{$q}%")
                    ->orWhere('patients.phone', 'like', "%{$q}%")
                    ->orWhere('patients.aadhar_no', 'like', "%{$q}%")
                    ->orWhere('patients.ayushman_bharat_id', 'like', "%{$q}%");
            });
        }

        // Visit type filter via subquery
        if ($filter === 'opd') {
            $query->whereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('opd_patients')
                    ->whereColumn('opd_patients.patient_id', 'patients.id')
                    ->where('opd_patients.hospital_id', $this->hospital_id)
                    ->whereDate('opd_patients.appointment_date', now()->toDateString());
            });
        } elseif ($filter === 'ipd') {
            $query->whereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('bed_allocations')
                    ->whereColumn('bed_allocations.patient_id', 'patients.id')
                    ->where('bed_allocations.hospital_id', $this->hospital_id)
                    ->whereNull('bed_allocations.discharge_date');
            });
        } elseif ($filter === 'emergency') {
            $query->where(function ($sq) {
                $sq->whereExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('opd_patients')
                        ->whereColumn('opd_patients.patient_id', 'patients.id')
                        ->where('opd_patients.hospital_id', $this->hospital_id)
                        ->where('opd_patients.casualty', 'Yes')
                        ->whereDate('opd_patients.appointment_date', now()->toDateString());
                })->orWhereExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('bed_allocations')
                        ->whereColumn('bed_allocations.patient_id', 'patients.id')
                        ->where('bed_allocations.hospital_id', $this->hospital_id)
                        ->whereNull('bed_allocations.discharge_date')
                        ->where('bed_allocations.admission_source', 'emergency');
                });
            });
        } elseif ($filter === 'discharged') {
            $query->whereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('bed_allocations')
                    ->whereColumn('bed_allocations.patient_id', 'patients.id')
                    ->where('bed_allocations.hospital_id', $this->hospital_id)
                    ->whereDate('bed_allocations.discharge_date', now()->toDateString());
            });
        }

        if ($deptId) {
            $query->where(function ($sq) use ($deptId) {
                $sq->whereExists(function ($sub) use ($deptId) {
                    $sub->select(DB::raw(1))
                        ->from('opd_patients')
                        ->whereColumn('opd_patients.patient_id', 'patients.id')
                        ->where('opd_patients.hospital_id', $this->hospital_id)
                        ->where('opd_patients.hr_department_id', $deptId);
                })->orWhereExists(function ($sub) use ($deptId) {
                    $sub->select(DB::raw(1))
                        ->from('bed_allocations')
                        ->whereColumn('bed_allocations.patient_id', 'patients.id')
                        ->where('bed_allocations.hospital_id', $this->hospital_id)
                        ->where('bed_allocations.hr_department_id', $deptId);
                });
            });
        }

        $total    = (clone $query)->count();
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page     = min($page, $lastPage);

        $patients = $query->orderByDesc('patients.id')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        // Enrich with visit info
        $patientIds = $patients->pluck('id')->all();

        // Latest OPD visits
        $latestOpd = OpdPatient::query()
            ->whereIn('patient_id', $patientIds)
            ->where('hospital_id', $this->hospital_id)
            ->select('id', 'patient_id', 'status', 'appointment_date', 'hr_department_id', 'visit_type', 'casualty')
            ->orderByDesc('id')
            ->get()
            ->unique('patient_id')
            ->keyBy('patient_id');

        // Active IPD
        $activeIpd = BedAllocation::query()
            ->whereIn('patient_id', $patientIds)
            ->where('hospital_id', $this->hospital_id)
            ->whereNull('discharge_date')
            ->select('patient_id', 'admission_no', 'admission_date', 'hr_department_id', 'admission_source')
            ->get()
            ->keyBy('patient_id');

        $departmentIds = collect($latestOpd)->pluck('hr_department_id')
            ->merge(collect($activeIpd)->pluck('hr_department_id'))
            ->filter()
            ->unique()
            ->values();

        $departments = HrDepartment::query()
            ->whereIn('id', $departmentIds)
            ->pluck('name', 'id');

        $rows = $patients->map(function ($p) use ($latestOpd, $activeIpd, $departments) {
            $opd = $latestOpd->get($p->id);
            $ipd = $activeIpd->get($p->id);
            $sourceVisitType = $opd
                ? ($opd->casualty === 'Yes' ? 'Emergency' : ($opd->visit_type ?: 'OPD'))
                : null;

            $visitType = '-';
            $status    = 'registered';
            $dept      = '-';

            if ($ipd) {
                // Keep original source type visible for admitted patients (e.g. Emergency admission).
                $visitType = $sourceVisitType ?: ($ipd->admission_source === 'emergency' ? 'Emergency' : 'IPD');
                $status    = 'admitted';
                $dept      = $departments[$ipd->hr_department_id] ?? '-';
            } elseif ($opd) {
                $visitType = $sourceVisitType;
                $status    = $opd->status ?: 'waiting';
                $dept      = $departments[$opd->hr_department_id] ?? '-';
            }

            return [
                'id'         => $p->id,
                'mrn'        => $p->mrn ?: $p->reg_no,
                'name'       => $p->name,
                'age_sex'    => ($p->age_years ?? '-') . ' / ' . ($p->gender ? strtoupper(substr($p->gender, 0, 1)) : '-'),
                'phone'      => $p->phone,
                'blood_group' => $p->blood_group ?: '-',
                'visit_type' => $visitType,
                'dept'       => $dept,
                'status'     => $status,
                'registered' => $p->created_at ? Carbon::parse($p->created_at)->diffForHumans() : '-',
            ];
        });

        return response()->json([
            'data'       => $rows,
            'total'      => $total,
            'page'       => $page,
            'per_page'   => $perPage,
            'last_page'  => $lastPage,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // AJAX – OPD QUEUE (today)
    // ─────────────────────────────────────────────────────────────────────────

    public function opdQueue(Request $request)
    {
        $deptId        = $request->input('dept_id');
        $q             = trim((string) $request->input('q', ''));
        $today         = now()->toDateString();
        $perPage       = min((int) $request->input('per_page', 12), 100);
        $page          = max(1, (int) $request->input('page', 1));
        $emergencyOnly = $request->boolean('emergency_only');

        if ($emergencyOnly) {
            $opdEmergency = OpdPatient::query()
                ->where('opd_patients.hospital_id', $this->hospital_id)
                ->whereDate('opd_patients.appointment_date', $today)
                ->where('opd_patients.casualty', 'Yes')
                ->leftJoin('patients', 'patients.id', '=', 'opd_patients.patient_id')
                ->leftJoin('staff', 'staff.id', '=', 'opd_patients.doctor_id')
                ->leftJoin('hr_departments', 'hr_departments.id', '=', 'opd_patients.hr_department_id')
                ->select(
                    'opd_patients.id',
                    'opd_patients.patient_id',
                    'opd_patients.token_no',
                    'opd_patients.case_no',
                    'opd_patients.status',
                    'opd_patients.slot',
                    'opd_patients.appointment_date as event_at',
                    'patients.name as patient_name',
                    'patients.mrn',
                    'patients.phone',
                    'patients.age_years',
                    'patients.gender',
                    DB::raw("TRIM(CONCAT(COALESCE(staff.first_name,''),' ',COALESCE(staff.last_name,''))) as doctor_name"),
                    'hr_departments.name as dept_name'
                );

            $ipdEmergency = BedAllocation::query()
                ->where('bed_allocations.hospital_id', $this->hospital_id)
                ->whereNull('bed_allocations.discharge_date')
                ->where('bed_allocations.admission_source', 'emergency')
                ->leftJoin('patients', 'patients.id', '=', 'bed_allocations.patient_id')
                ->leftJoin('staff', 'staff.id', '=', 'bed_allocations.consultant_doctor_id')
                ->leftJoin('hr_departments', 'hr_departments.id', '=', 'bed_allocations.hr_department_id')
                ->select(
                    DB::raw('-bed_allocations.id as id'),
                    'bed_allocations.patient_id',
                    DB::raw('NULL as token_no'),
                    'bed_allocations.admission_no as case_no',
                    DB::raw("'admitted' as status"),
                    DB::raw("'IPD Emergency Admission' as slot"),
                    'bed_allocations.admission_date as event_at',
                    'patients.name as patient_name',
                    'patients.mrn',
                    'patients.phone',
                    'patients.age_years',
                    'patients.gender',
                    DB::raw("TRIM(CONCAT(COALESCE(staff.first_name,''),' ',COALESCE(staff.last_name,''))) as doctor_name"),
                    'hr_departments.name as dept_name'
                );

            if ($q !== '') {
                $opdEmergency->where(function ($sq) use ($q) {
                    $sq->where('opd_patients.token_no', 'like', "%{$q}%")
                        ->orWhere('opd_patients.case_no', 'like', "%{$q}%")
                        ->orWhere('patients.name', 'like', "%{$q}%")
                        ->orWhere('patients.mrn', 'like', "%{$q}%")
                        ->orWhere('patients.phone', 'like', "%{$q}%")
                        ->orWhereRaw("TRIM(CONCAT(COALESCE(staff.first_name,''),' ',COALESCE(staff.last_name,''))) like ?", ["%{$q}%"]);
                });

                $ipdEmergency->where(function ($sq) use ($q) {
                    $sq->where('bed_allocations.admission_no', 'like', "%{$q}%")
                        ->orWhere('patients.name', 'like', "%{$q}%")
                        ->orWhere('patients.mrn', 'like', "%{$q}%")
                        ->orWhere('patients.phone', 'like', "%{$q}%")
                        ->orWhereRaw("TRIM(CONCAT(COALESCE(staff.first_name,''),' ',COALESCE(staff.last_name,''))) like ?", ["%{$q}%"]);
                });
            }

            if ($deptId) {
                $opdEmergency->where('opd_patients.hr_department_id', $deptId);
                $ipdEmergency->where('bed_allocations.hr_department_id', $deptId);
            }

            $combined = $opdEmergency->get()->concat($ipdEmergency->get())
                ->sortByDesc(function ($row) {
                    return $row->event_at ? Carbon::parse($row->event_at)->timestamp : 0;
                })
                ->values();

            $total = $combined->count();
            $lastPage = max(1, (int) ceil($total / $perPage));
            $page = min($page, $lastPage);

            $pageRows = $combined
                ->slice(($page - 1) * $perPage, $perPage)
                ->values();

            $rows = $pageRows->map(function ($row) {
                return [
                    'id'          => $row->id,
                    'token'       => $row->token_no ? str_pad($row->token_no, 3, '0', STR_PAD_LEFT) : 'IPD-EMG',
                    'case_no'     => $row->case_no,
                    'patient'     => $row->patient_name,
                    'mrn'         => $row->mrn,
                    'phone'       => $row->phone,
                    'age_sex'     => ($row->age_years ?? '-') . '/' . ($row->gender ? strtoupper(substr($row->gender, 0, 1)) : '-'),
                    'doctor'      => $row->doctor_name ?: '-',
                    'dept'        => $row->dept_name ?: '-',
                    'status'      => $row->status ?: 'waiting',
                    'slot'        => $row->slot,
                    'time'        => $row->event_at ? Carbon::parse($row->event_at)->format('h:i A') : '-',
                    'is_emergency' => true,
                ];
            });

            return response()->json([
                'data'      => $rows,
                'total'     => $total,
                'page'      => $page,
                'per_page'  => $perPage,
                'last_page' => $lastPage,
            ]);
        }

        $query = OpdPatient::query()
            ->where('opd_patients.hospital_id', $this->hospital_id)
            ->whereDate('opd_patients.appointment_date', $today)
            ->leftJoin('patients', 'patients.id', '=', 'opd_patients.patient_id')
            ->leftJoin('staff', 'staff.id', '=', 'opd_patients.doctor_id')
            ->leftJoin('hr_departments', 'hr_departments.id', '=', 'opd_patients.hr_department_id')
            ->select(
                'opd_patients.id',
                'opd_patients.token_no',
                'opd_patients.booking_number',
                'opd_patients.case_no',
                'opd_patients.status',
                'opd_patients.appointment_date',
                'opd_patients.casualty',
                'opd_patients.visit_type',
                'opd_patients.slot',
                'patients.name as patient_name',
                'patients.mrn',
                'patients.phone',
                'patients.age_years',
                'patients.gender',
                DB::raw("TRIM(CONCAT(COALESCE(staff.first_name,''),' ',COALESCE(staff.last_name,''))) as doctor_name"),
                'hr_departments.name as dept_name'
            );

        if ($q !== '') {
            $query->where(function ($sq) use ($q) {
                $sq->where('opd_patients.token_no', 'like', "%{$q}%")
                    ->orWhere('opd_patients.booking_number', 'like', "%{$q}%")
                    ->orWhere('opd_patients.case_no', 'like', "%{$q}%")
                    ->orWhere('patients.name', 'like', "%{$q}%")
                    ->orWhere('patients.mrn', 'like', "%{$q}%")
                    ->orWhere('patients.phone', 'like', "%{$q}%")
                    ->orWhereRaw("TRIM(CONCAT(COALESCE(staff.first_name,''),' ',COALESCE(staff.last_name,''))) like ?", ["%{$q}%"]);
            });
        }

        if ($deptId) {
            $query->where('opd_patients.hr_department_id', $deptId);
        }

        if ($emergencyOnly) {
            $query->where('opd_patients.casualty', 'Yes');
        }

        $total = (clone $query)->count('opd_patients.id');
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = min($page, $lastPage);

        $queue = $query
            ->orderByRaw("CASE WHEN COALESCE(opd_patients.status,'waiting')='completed' THEN 1 ELSE 0 END ASC")
            ->orderByRaw('COALESCE(opd_patients.token_no, 999999) ASC')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        $rows = $queue->map(function ($row) {
            $appointmentDate = $row->appointment_date ? Carbon::parse($row->appointment_date) : null;
            $slotStart = $appointmentDate ? $this->resolveSlotStartDateTime($appointmentDate, $row->slot) : null;
            $windowOpenAt = $slotStart ? $slotStart->copy()->subMinutes(max(0, (int) Helpers::getBeforeTime())) : null;
            $isBooking = empty($row->token_no);
            $canIssueNextToken = $isBooking && $windowOpenAt && now()->greaterThanOrEqualTo($windowOpenAt);

            return [
                'id'               => $row->id,
                'token'            => $row->token_no ? str_pad($row->token_no, 3, '0', STR_PAD_LEFT) : '-',
                'token_no'         => $row->token_no,
                'booking_number'   => $row->booking_number,
                'case_no'          => $row->case_no,
                'patient'          => $row->patient_name,
                'mrn'              => $row->mrn,
                'phone'            => $row->phone,
                'age_sex'          => ($row->age_years ?? '-') . '/' . ($row->gender ? strtoupper(substr($row->gender, 0, 1)) : '-'),
                'doctor'           => $row->doctor_name ?: '-',
                'dept'             => $row->dept_name ?: '-',
                'status'           => $row->status ?: 'waiting',
                'queue_type'       => $isBooking ? 'booking' : 'waiting',
                'slot'             => $row->slot,
                'visit_type'       => $row->visit_type,
                'time'             => $appointmentDate ? $appointmentDate->format('h:i A') : '-',
                'appointment_date' => $appointmentDate ? $appointmentDate->format('d M Y') : '-',
                'slot_start_at'    => $slotStart ? $slotStart->toIso8601String() : null,
                'window_open_at'   => $windowOpenAt ? $windowOpenAt->toIso8601String() : null,
                'can_issue_next_token' => (bool) $canIssueNextToken,
                'is_emergency'     => $row->casualty === 'Yes',
            ];
        });

        return response()->json([
            'data'      => $rows,
            'total'     => $total,
            'page'      => $page,
            'per_page'  => $perPage,
            'last_page' => $lastPage,
        ]);
    }

    public function bookingAppointments(Request $request)
    {
        $deptId  = $request->input('dept_id');
        $q       = trim((string) $request->input('q', ''));
        $today   = now()->toDateString();
        $perPage = min((int) $request->input('per_page', 12), 100);
        $page    = max(1, (int) $request->input('page', 1));

        $query = OpdPatient::query()
            ->where('opd_patients.hospital_id', $this->hospital_id)
            ->whereDate('opd_patients.appointment_date', '>', $today)
            ->whereNull('opd_patients.token_no')
            ->leftJoin('patients', 'patients.id', '=', 'opd_patients.patient_id')
            ->leftJoin('staff', 'staff.id', '=', 'opd_patients.doctor_id')
            ->leftJoin('hr_departments', 'hr_departments.id', '=', 'opd_patients.hr_department_id')
            ->select(
                'opd_patients.id',
                'opd_patients.token_no',
                'opd_patients.booking_number',
                'opd_patients.case_no',
                'opd_patients.status',
                'opd_patients.appointment_date',
                'opd_patients.casualty',
                'opd_patients.visit_type',
                'opd_patients.slot',
                'patients.name as patient_name',
                'patients.mrn',
                'patients.phone',
                'patients.age_years',
                'patients.gender',
                DB::raw("TRIM(CONCAT(COALESCE(staff.first_name,''),' ',COALESCE(staff.last_name,''))) as doctor_name"),
                'hr_departments.name as dept_name'
            );

        if ($q !== '') {
            $query->where(function ($sq) use ($q) {
                $sq->where('opd_patients.booking_number', 'like', "%{$q}%")
                    ->orWhere('opd_patients.case_no', 'like', "%{$q}%")
                    ->orWhere('patients.name', 'like', "%{$q}%")
                    ->orWhere('patients.mrn', 'like', "%{$q}%")
                    ->orWhere('patients.phone', 'like', "%{$q}%")
                    ->orWhereRaw("TRIM(CONCAT(COALESCE(staff.first_name,''),' ',COALESCE(staff.last_name,''))) like ?", ["%{$q}%"]);
            });
        }

        if ($deptId) {
            $query->where('opd_patients.hr_department_id', $deptId);
        }

        $total = (clone $query)->count('opd_patients.id');
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = min($page, $lastPage);

        $queue = $query
            ->orderBy('opd_patients.appointment_date', 'asc')
            ->orderByRaw('STR_TO_DATE(SUBSTRING_INDEX(opd_patients.slot, " - ", 1), "%h:%i %p") ASC')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        $rows = $queue->map(function ($row) {
            $appointmentDate = $row->appointment_date ? Carbon::parse($row->appointment_date) : null;
            $slotStart = $appointmentDate ? $this->resolveSlotStartDateTime($appointmentDate, $row->slot) : null;
            $windowOpenAt = $slotStart ? $slotStart->copy()->subMinutes(max(0, (int) Helpers::getBeforeTime())) : null;
            $isBooking = empty($row->token_no);
            $canIssueNextToken = $isBooking && $windowOpenAt && now()->greaterThanOrEqualTo($windowOpenAt);

            return [
                'id'               => $row->id,
                'token'            => $row->token_no ? str_pad($row->token_no, 3, '0', STR_PAD_LEFT) : '-',
                'token_no'         => $row->token_no,
                'booking_number'   => $row->booking_number,
                'case_no'          => $row->case_no,
                'patient'          => $row->patient_name,
                'mrn'              => $row->mrn,
                'phone'            => $row->phone,
                'age_sex'          => ($row->age_years ?? '-') . '/' . ($row->gender ? strtoupper(substr($row->gender, 0, 1)) : '-'),
                'doctor'           => $row->doctor_name ?: '-',
                'dept'             => $row->dept_name ?: '-',
                'status'           => $row->status ?: 'booking',
                'queue_type'       => 'booking',
                'slot'             => $row->slot,
                'visit_type'       => $row->visit_type,
                'time'             => $appointmentDate ? $appointmentDate->format('h:i A') : '-',
                'appointment_date' => $appointmentDate ? $appointmentDate->format('d M Y') : '-',
                'slot_start_at'    => $slotStart ? $slotStart->toIso8601String() : null,
                'window_open_at'   => $windowOpenAt ? $windowOpenAt->toIso8601String() : null,
                'can_issue_next_token' => (bool) $canIssueNextToken,
                'is_emergency'     => $row->casualty === 'Yes',
            ];
        });

        return response()->json([
            'data'      => $rows,
            'total'     => $total,
            'page'      => $page,
            'per_page'  => $perPage,
            'last_page' => $lastPage,
        ]);
    }

    public function issueNextToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'opd_patient_id' => 'required|integer|exists:opd_patients,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        try {
            $result = DB::transaction(function () use ($request) {
                $opdPatient = OpdPatient::query()
                    ->lockForUpdate()
                    ->where('id', $request->opd_patient_id)
                    ->where('hospital_id', $this->hospital_id)
                    ->first();

                if (!$opdPatient) {
                    return ['status' => false, 'message' => 'OPD booking not found.'];
                }

                if (!empty($opdPatient->token_no)) {
                    return [
                        'status' => true,
                        'message' => 'Token already assigned.',
                        'token' => str_pad((string) $opdPatient->token_no, 3, '0', STR_PAD_LEFT),
                        'booking_number' => $opdPatient->booking_number,
                        'opd_patient_id' => $opdPatient->id,
                    ];
                }

                if (!in_array($opdPatient->status, ['waiting', 'booking', 'in_room'], true)) {
                    return ['status' => false, 'message' => 'Token can only be issued for active booking rows.'];
                }

                $appointmentDate = Carbon::parse($opdPatient->appointment_date);

                $force = filter_var($request->input('force', false), FILTER_VALIDATE_BOOLEAN);

                if (!$force && !$this->shouldAssignTokenNow($appointmentDate, $opdPatient->slot)) {
                    $slotStart = $this->resolveSlotStartDateTime($appointmentDate, $opdPatient->slot);
                    $windowOpenAt = $slotStart->copy()->subMinutes(max(0, (int) Helpers::getBeforeTime()));

                    return [
                        'status' => false,
                        'message' => 'Token can be issued only within configured pre-slot window.',
                        'window_open_at' => $windowOpenAt->toIso8601String(),
                    ];
                }

                $tokenNo = $this->generateSlotWiseTokenNo($appointmentDate, $opdPatient->slot);
                $opdPatient->update([
                    'token_no' => $tokenNo,
                    'status' => 'waiting',
                ]);

                return [
                    'status' => true,
                    'message' => 'Next token assigned successfully.',
                    'token' => str_pad((string) $tokenNo, 3, '0', STR_PAD_LEFT),
                    'booking_number' => $opdPatient->booking_number,
                    'opd_patient_id' => $opdPatient->id,
                ];
            });

            $code = !empty($result['status']) ? 200 : 422;
            return response()->json($result, $code);
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function cancelBookingAppointment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'opd_patient_id' => 'required|integer|exists:opd_patients,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        try {
            $opdPatient = OpdPatient::query()
                ->where('id', $request->opd_patient_id)
                ->where('hospital_id', $this->hospital_id)
                ->first();

            if (!$opdPatient) {
                return response()->json([
                    'status' => false,
                    'message' => 'Booking appointment not found.',
                ], 404);
            }

            $appointmentDate = Carbon::parse($opdPatient->appointment_date)->toDateString();
            $today = now()->toDateString();

            if ($appointmentDate <= $today) {
                return response()->json([
                    'status' => false,
                    'message' => 'Only future booking appointments can be cancelled.',
                ], 422);
            }

            if (!empty($opdPatient->token_no)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Token-issued records cannot be cancelled from booking appointments.',
                ], 422);
            }

            $opdPatient->delete();

            return response()->json([
                'status' => true,
                'message' => 'Future booking appointment cancelled successfully.',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // AJAX – IPD ADMISSIONS
    // ─────────────────────────────────────────────────────────────────────────

    public function ipdAdmissions(Request $request)
    {
        $q       = trim((string) $request->input('q', ''));
        $perPage = min((int) $request->input('per_page', 10), 100);
        $page    = max(1, (int) $request->input('page', 1));

        $query = BedAllocation::query()
            ->where('bed_allocations.hospital_id', $this->hospital_id)
            ->whereNull('bed_allocations.discharge_date')
            ->leftJoin('patients', 'patients.id', '=', 'bed_allocations.patient_id')
            ->leftJoin('beds', 'beds.id', '=', 'bed_allocations.bed_id')
            ->leftJoin('bed_types', 'bed_types.id', '=', 'beds.bed_type_id')
            ->leftJoin('staff', 'staff.id', '=', 'bed_allocations.consultant_doctor_id')
            ->leftJoin('hr_departments', 'hr_departments.id', '=', 'bed_allocations.hr_department_id')
            ->select(
                'bed_allocations.id',
                'bed_allocations.patient_id',
                'bed_allocations.admission_no',
                'bed_allocations.admission_date',
                'bed_allocations.admission_source',
                'patients.name as patient_name',
                'patients.mrn',
                'patients.age_years',
                'patients.gender',
                'beds.bed_code as bed_no',
                'bed_types.type_name as ward_name',
                DB::raw("TRIM(CONCAT(COALESCE(staff.first_name,''),' ',COALESCE(staff.last_name,''))) as doctor_name"),
                'hr_departments.name as dept_name'
            );

        if ($q !== '') {
            $query->where(function ($sq) use ($q) {
                $sq->where('bed_allocations.admission_no', 'like', "%{$q}%")
                    ->orWhere('patients.name', 'like', "%{$q}%")
                    ->orWhere('patients.mrn', 'like', "%{$q}%")
                    ->orWhere('patients.phone', 'like', "%{$q}%")
                    ->orWhere('beds.bed_code', 'like', "%{$q}%")
                    ->orWhereRaw("TRIM(CONCAT(COALESCE(staff.first_name,''),' ',COALESCE(staff.last_name,''))) like ?", ["%{$q}%"]);
            });
        }

        $deptId = $request->input('dept_id');
        if ($deptId) {
            $query->where('bed_allocations.hr_department_id', $deptId);
        }

        $total = (clone $query)->count('bed_allocations.id');
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = min($page, $lastPage);

        $admissions = $query->orderByDesc('bed_allocations.id')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        $patientIds = $admissions->pluck('patient_id')->filter()->unique()->values();

        $latestOpdByPatient = OpdPatient::query()
            ->whereIn('patient_id', $patientIds)
            ->where('hospital_id', $this->hospital_id)
            ->select('patient_id', 'visit_type', 'casualty')
            ->orderByDesc('id')
            ->get()
            ->unique('patient_id')
            ->keyBy('patient_id');

        $rows = $admissions->map(function ($row) use ($latestOpdByPatient) {
            $days = $row->admission_date ? Carbon::parse($row->admission_date)->diffInDays(now()) + 1 : '-';
            $latestOpd = $latestOpdByPatient->get($row->patient_id);
            $visitType = $latestOpd
                ? ($latestOpd->casualty === 'Yes' ? 'Emergency' : ($latestOpd->visit_type ?: 'OPD'))
                : ($row->admission_source === 'emergency' ? 'Emergency' : 'IPD');

            return [
                'id'           => $row->id,
                'patient_id'   => $row->patient_id,
                'admission_no' => $row->admission_no,
                'patient'      => $row->patient_name,
                'mrn'          => $row->mrn,
                'age_sex'      => ($row->age_years ?? '-') . '/' . ($row->gender ? strtoupper(substr($row->gender, 0, 1)) : '-'),
                'visit_type'   => $visitType,
                'bed'          => $row->bed_no,
                'ward'         => $row->ward_name ?: '-',
                'doctor'       => $row->doctor_name ?: '-',
                'dept'         => $row->dept_name ?: '-',
                'days'         => $days,
                'since'        => $row->admission_date ? Carbon::parse($row->admission_date)->format('d-m-Y') : '-',
            ];
        });

        return response()->json([
            'data'      => $rows,
            'total'     => $total,
            'page'      => $page,
            'per_page'  => $perPage,
            'last_page' => $lastPage,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // AJAX – SEARCH PATIENTS (autocomplete / MRN lookup)
    // ─────────────────────────────────────────────────────────────────────────

    public function searchPatients(Request $request)
    {
        $q = trim($request->input('q', ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $patients = Patient::query()
            ->where('hospital_id', $this->hospital_id)
            ->where(function ($sq) use ($q) {
                $sq->where('name', 'like', "%{$q}%")
                    ->orWhere('mrn', 'like', "%{$q}%")
                    ->orWhere('patient_id', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('aadhar_no', 'like', "%{$q}%");
            })
            ->select('id', 'mrn', 'patient_id', 'name', 'age_years', 'gender', 'phone', 'blood_group', 'date_of_birth')
            ->limit(10)
            ->get();

        return response()->json($patients->map(function ($p) {
            return [
                'id'         => $p->id,
                'mrn'        => $p->mrn ?: $p->patient_id,
                'name'       => $p->name,
                'phone'      => $p->phone,
                'age_sex'    => ($p->age_years ?? '-') . '/' . ($p->gender ? strtoupper(substr($p->gender, 0, 1)) : '-'),
                'blood_group' => $p->blood_group ?: '-',
                'dob'        => $p->date_of_birth ? Carbon::parse($p->date_of_birth)->format('d-m-Y') : null,
            ];
        }));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // AJAX – PATIENT 360 VIEW
    // ─────────────────────────────────────────────────────────────────────────

    public function patient360(Request $request)
    {
        $mrn = $request->input('mrn');
        $id  = $request->input('id');

        $patient = Patient::query()
            ->where('hospital_id', $this->hospital_id)
            ->when($mrn, fn ($q) => $q->where(function ($inner) use ($mrn) {
                $inner->where('mrn', $mrn)
                    ->orWhere('patient_id', $mrn);
            }))
            ->when($id && !$mrn, fn ($q) => $q->where('id', $id))
            ->first();

        if (!$patient) {
            return response()->json(['error' => 'Patient not found.'], 404);
        }

        // Last 5 OPD visits
        $opdVisits = OpdPatient::query()
            ->where('patient_id', $patient->id)
            ->leftJoin('staff', 'staff.id', '=', 'opd_patients.doctor_id')
            ->leftJoin('hr_departments', 'hr_departments.id', '=', 'opd_patients.hr_department_id')
            ->select(
                'opd_patients.case_no',
                'opd_patients.appointment_date',
                'opd_patients.status',
                'opd_patients.visit_type',
                'opd_patients.casualty',
                'opd_patients.applied_charge',
                'opd_patients.slot',
                'opd_patients.symptoms_description',
                DB::raw("TRIM(CONCAT(COALESCE(staff.first_name,''),' ',COALESCE(staff.last_name,''))) as doctor_name"),
                'hr_departments.name as dept_name'
            )
            ->orderByDesc('opd_patients.id')
            ->limit(5)
            ->get();

        // Active IPD
        $activeIpd = BedAllocation::query()
            ->where('patient_id', $patient->id)
            ->whereNull('discharge_date')
            ->leftJoin('beds', 'beds.id', '=', 'bed_allocations.bed_id')
            ->leftJoin('bed_types', 'bed_types.id', '=', 'beds.bed_type_id')
            ->select(
                'bed_allocations.admission_no',
                'bed_allocations.admission_date',
                'bed_allocations.admission_source',
                'beds.bed_code as bed_no',
                'bed_types.type_name as ward_name'
            )
            ->first();

        $chargeSummary = PatientCharge::query()
            ->where('patient_id', $patient->id)
            ->selectRaw('COALESCE(SUM(amount), 0) as total_billed')
            ->selectRaw('COALESCE(SUM(paid_amount), 0) as total_paid_against_charges')
            ->selectRaw('COALESCE(SUM(discount_amount), 0) as total_discount')
            ->first();

        $totalPaid = (float) PatientPayment::query()
            ->where('patient_id', $patient->id)
            ->sum('amount');

        $totalBilled = (float) ($chargeSummary->total_billed ?? 0);
        $paidAgainstCharges = (float) ($chargeSummary->total_paid_against_charges ?? 0);
        $totalDiscount = (float) ($chargeSummary->total_discount ?? 0);
        $totalDue = max(0, $totalBilled - $paidAgainstCharges);
        $advanceBalance = max(0, $totalPaid - $paidAgainstCharges);

        return response()->json([
            'patient' => $patient,
            'opd_visits' => $opdVisits,
            'active_ipd' => $activeIpd,
            'billing_summary' => [
                'total_billed' => round($totalBilled, 2),
                'total_paid' => round($totalPaid, 2),
                'total_paid_against_charges' => round($paidAgainstCharges, 2),
                'total_due' => round($totalDue, 2),
                'total_discount' => round($totalDiscount, 2),
                'advance_balance' => round($advanceBalance, 2),
            ],
        ]);
    }

    public function patientDetails(Request $request, ChargeLedgerService $chargeLedger)
    {
        $mrn = $request->input('mrn');
        $id = $request->input('id');

        $patient = Patient::query()
            ->where('hospital_id', $this->hospital_id)
            ->when($id, fn ($q) => $q->where('id', $id))
            ->when($mrn && !$id, fn ($q) => $q->where(function ($inner) use ($mrn) {
                $inner->where('mrn', $mrn)
                    ->orWhere('patient_id', $mrn);
            }))
            ->first();

        if (!$patient) {
            abort(404, 'Patient not found.');
        }

        $activeIpdAllocation = BedAllocation::query()
            ->where('hospital_id', $this->hospital_id)
            ->where('patient_id', $patient->id)
            ->whereNull('discharge_date')
            ->with([
                'bed:id,bed_code,bed_type_id,room_id',
                'bed.bedType:id,type_name',
                'bed.room:id,room_number,ward_id',
                'bed.room.ward:id,ward_name',
            ])
            ->orderByDesc('admission_date')
            ->orderByDesc('id')
            ->first();

        $patient360CanIpdDischarge = false;
        if ($activeIpdAllocation) {
            $activeIpdAllocation = $this->findIpdAllocationForPatient360($activeIpdAllocation->id);
            $activeIpdAllocation = $this->syncIpdBedChargeForPatient360($activeIpdAllocation, $chargeLedger);
            $episodeAllocations = $this->resolveIpdEpisodeAllocationsForPatient360($activeIpdAllocation);
            $episodeOutstanding = (float) $this->ipdEpisodeChargeQueryForPatient360($activeIpdAllocation, $episodeAllocations)
                ->get()
                ->sum(function (PatientCharge $charge) {
                    return max(0, (float) $charge->amount - (float) $charge->paid_amount);
                });
            $patient360CanIpdDischarge = $episodeOutstanding <= 0;
        }

        $latestOpdVisit = OpdPatient::query()
            ->where('hospital_id', $this->hospital_id)
            ->where('patient_id', $patient->id)
            ->with(['consultant:id,first_name,last_name'])
            ->orderByDesc('appointment_date')
            ->orderByDesc('id')
            ->first([
                'id',
                'patient_id',
                'case_no',
                'token_no',
                'appointment_date',
                'status',
                'systolic_bp',
                'diastolic_bp',
                'pluse',
                'spo2',
                'temperature',
                'diabetes',
                'height',
                'weight',
                'bmi',
                'visit_type',
            ]);

            $visits = OpdPatient::select(
                    'id',
                    'hospital_id',
                    'patient_id',
                    'case_no',
                    'doctor_id',
                    'hr_department_id',
                    'appointment_date',
                    'tpa_id',
                    'tpa_reference_no as reference',
                    'standard_charge',
                    'applied_charge',
                    'payment_mode',
                    'symptoms',
                    'symptoms_description as symptoms_name',
                    'height',
                    'weight',
                    'bp',
                    'pluse',
                    'temperature',
                    'respiration',
                    'systolic_bp',
                    'diastolic_bp',
                    'diabetes',
                    'bmi',
                    'slot'
                )
                ->where('patient_id', $patient->id)
                ->with([
                    'consultant:id,first_name,last_name',
                    'prescription:id,opd_patient_id',
                ])
                ->orderByDesc('appointment_date')
                ->orderByDesc('id')
                ->get();

            if ($visits->isEmpty() && isset($activeIpdAllocation)) {
                $ipdVisit = (object) [
                    'id' => (int) $activeIpdAllocation->id,
                    'is_ipd' => true,
                    'case_no' => $activeIpdAllocation->admission_no,
                    'appointment_date' => $activeIpdAllocation->admission_date,
                    'reference' => $activeIpdAllocation->tpa_reference_no,
                    'symptoms_name' => $activeIpdAllocation->admission_reason,
                    'slot' => 'IPD Admission',
                    'consultant' => (object) [
                        'full_name' => isset($activeIpdAllocation->consultantDoctor) ? trim(($activeIpdAllocation->consultantDoctor->first_name ?? '') . ' ' . ($activeIpdAllocation->consultantDoctor->last_name ?? '')) : '-',
                    ],
                    'systolic_bp' => $activeIpdAllocation->systolic_bp,
                    'diastolic_bp' => $activeIpdAllocation->diastolic_bp,
                    'pluse' => $activeIpdAllocation->pulse,
                    'temperature' => $activeIpdAllocation->temperature,
                    'respiration' => $activeIpdAllocation->respiration,
                    'diabetes' => $activeIpdAllocation->diabetes,
                    'height' => $activeIpdAllocation->height,
                    'weight' => $activeIpdAllocation->weight,
                    'bmi' => $activeIpdAllocation->bmi,
                ];
                $visits = collect([$ipdVisit]);
            }

        $visitContext = $activeIpdAllocation ? 'ipd' : 'opd';

        $timelineEntries = PatientTimeline::query()
            ->where('patient_id', $patient->id)
            ->orderByDesc('logged_at')
            ->orderByDesc('id')
            ->limit(200)
            ->get();

        $opdSoapNotes = OpdPatient::query()
            ->where('hospital_id', $this->hospital_id)
            ->where('patient_id', $patient->id)
            ->where(function ($query) {
                $query->whereNotNull('subjective_notes')
                    ->orWhereNotNull('objective_notes')
                    ->orWhereNotNull('assessment_notes')
                    ->orWhereNotNull('plan_notes');
            })
            ->with(['consultant:id,first_name,last_name'])
            ->orderByDesc('appointment_date')
            ->orderByDesc('id')
            ->get([
                'id',
                'case_no',
                'appointment_date',
                'doctor_id',
                'subjective_notes',
                'objective_notes',
                'assessment_notes',
                'plan_notes',
                'created_at',
                'updated_at',
            ]);

        $ipdProgressNotes = IpdProgressNote::query()
            ->where('patient_id', $patient->id)
            ->with([
                'allocation:id,admission_no',
                'creator:id,name',
            ])
            ->orderByDesc('noted_at')
            ->orderByDesc('id')
            ->get();

        $clinicalNotes = collect();

        foreach ($opdSoapNotes as $opdSoapNote) {
            $soapParts = [];
            if (filled($opdSoapNote->subjective_notes)) {
                $soapParts[] = 'S: ' . trim((string) $opdSoapNote->subjective_notes);
            }
            if (filled($opdSoapNote->objective_notes)) {
                $soapParts[] = 'O: ' . trim((string) $opdSoapNote->objective_notes);
            }
            if (filled($opdSoapNote->assessment_notes)) {
                $soapParts[] = 'A: ' . trim((string) $opdSoapNote->assessment_notes);
            }
            if (filled($opdSoapNote->plan_notes)) {
                $soapParts[] = 'P: ' . trim((string) $opdSoapNote->plan_notes);
            }

            $doctorName = trim(
                data_get($opdSoapNote, 'consultant.first_name', '') . ' ' . data_get($opdSoapNote, 'consultant.last_name', '')
            );

            $clinicalNotes->push([
                'title' => 'OPD Progress Note',
                'context' => 'Visit: OPD - Case ' . ($opdSoapNote->case_no ?: '-'),
                'author' => $doctorName !== '' ? $doctorName : '-',
                'author_role' => 'Doctor',
                'note_type' => 'opd',
                'note_badge' => 'badge-primary',
                'noted_at' => $opdSoapNote->updated_at ?? $opdSoapNote->appointment_date ?? $opdSoapNote->created_at,
                'body' => implode("\n", $soapParts),
            ]);
        }

        foreach ($ipdProgressNotes as $ipdProgressNote) {
            $noteType = strtolower((string) ($ipdProgressNote->note_type ?? 'progress'));
            $typeLabel = match ($noteType) {
                'doctor' => 'IPD Doctor Progress Note',
                'nursing' => 'IPD Nursing Note',
                'discharge_plan' => 'IPD Discharge Plan',
                default => 'IPD Progress Note',
            };
            $authorRole = match ($noteType) {
                'doctor' => 'Doctor',
                'nursing' => 'Nurse',
                default => 'Staff',
            };
            $noteBadge = match ($noteType) {
                'doctor' => 'badge-danger',
                'nursing' => 'badge-warning',
                'discharge_plan' => 'badge-success',
                default => 'badge-secondary',
            };

            $clinicalNotes->push([
                'title' => $typeLabel,
                'context' => 'IPD - Admission No: ' . (data_get($ipdProgressNote, 'allocation.admission_no') ?: '-'),
                'author' => data_get($ipdProgressNote, 'creator.name') ?: '-',
                'author_role' => $authorRole,
                'note_type' => 'ipd',
                'note_badge' => $noteBadge,
                'noted_at' => $ipdProgressNote->noted_at ?? $ipdProgressNote->created_at,
                'body' => trim((string) ($ipdProgressNote->note ?? '')),
            ]);
        }

        $clinicalNotes = $clinicalNotes
            ->sortByDesc(function ($row) {
                return filled($row['noted_at']) ? Carbon::parse($row['noted_at'])->timestamp : 0;
            })
            ->values();

        // OPD/IPD prescriptions for Patient 360 "Orders" tab (not a separate Prescriptions tab).
        $prescriptionVisits = OpdPrescription::query()
            ->where('patient_id', $patient->id)
            ->with([
                'opdPatient:id,case_no,appointment_date,doctor_id',
                'doctor:id,first_name,last_name',
                'items.medicine:id,name',
                'items.dosage:id,dosage',
                'items.route:id,route',
                'items.frequency:id,frequency',
                'items.instruction:id,instruction',
            ])
            ->latest('id')
            ->get();

        $ipdPrescriptionVisits = IpdPrescription::query()
            ->where('patient_id', $patient->id)
            ->with([
                'allocation:id,admission_no',
                'doctor:id,first_name,last_name',
                'items.medicine:id,name',
                'items.dosage:id,dosage',
                'items.route:id,route',
                'items.frequency:id,frequency',
                'items.instruction:id,instruction',
            ])
            ->latest('id')
            ->get();

        $todayDate = now()->startOfDay();
        $medicationRows = collect();

        foreach ($prescriptionVisits as $opdPrescription) {
            $doctorName = trim(
                data_get($opdPrescription, 'doctor.first_name', '') . ' ' . data_get($opdPrescription, 'doctor.last_name', '')
            ) ?: '-';
            $visitRef = 'OPD - ' . (data_get($opdPrescription, 'opdPatient.case_no') ?: '-');

            foreach (($opdPrescription->items ?? collect()) as $item) {
                $days = (int) ($item->no_of_day ?? 0);
                $startedAt = Carbon::parse($item->created_at ?? $opdPrescription->created_at)->startOfDay();
                $endsAt = $days > 0 ? $startedAt->copy()->addDays($days - 1) : null;
                $isCompleted = $endsAt ? $todayDate->gt($endsAt) : false;

                $medicationRows->push([
                    'drug' => data_get($item, 'medicine.name') ?: '-',
                    'dose' => data_get($item, 'dosage.dosage') ?: '-',
                    'route' => data_get($item, 'route.route') ?: '-',
                    'frequency' => data_get($item, 'frequency.frequency') ?: '-',
                    'duration' => $days > 0 ? ($days . ' day(s)') : '-',
                    'prescribed_by' => $doctorName,
                    'reference' => $visitRef,
                    'started_at' => $startedAt,
                    'status_label' => $isCompleted ? 'Completed' : 'Ongoing',
                    'status_class' => $isCompleted ? 'badge-gray' : 'badge-success',
                    'sort_ts' => Carbon::parse($item->created_at ?? $opdPrescription->created_at)->timestamp,
                ]);
            }
        }

        foreach ($ipdPrescriptionVisits as $ipdPrescription) {
            $doctorName = trim(
                data_get($ipdPrescription, 'doctor.first_name', '') . ' ' . data_get($ipdPrescription, 'doctor.last_name', '')
            ) ?: '-';
            $visitRef = 'IPD - ' . (data_get($ipdPrescription, 'allocation.admission_no') ?: '-');

            foreach (($ipdPrescription->items ?? collect()) as $item) {
                $days = (int) ($item->no_of_day ?? 0);
                $startedAt = Carbon::parse($item->created_at ?? $ipdPrescription->created_at)->startOfDay();
                $endsAt = $days > 0 ? $startedAt->copy()->addDays($days - 1) : null;
                $isCompleted = $endsAt ? $todayDate->gt($endsAt) : false;

                $medicationRows->push([
                    'drug' => data_get($item, 'medicine.name') ?: '-',
                    'dose' => data_get($item, 'dosage.dosage') ?: '-',
                    'route' => data_get($item, 'route.route') ?: '-',
                    'frequency' => data_get($item, 'frequency.frequency') ?: '-',
                    'duration' => $days > 0 ? ($days . ' day(s)') : '-',
                    'prescribed_by' => $doctorName,
                    'reference' => $visitRef,
                    'started_at' => $startedAt,
                    'status_label' => $isCompleted ? 'Completed' : 'Ongoing',
                    'status_class' => $isCompleted ? 'badge-gray' : 'badge-success',
                    'sort_ts' => Carbon::parse($item->created_at ?? $ipdPrescription->created_at)->timestamp,
                ]);
            }
        }

        $medicationRows = $medicationRows
            ->sortByDesc('sort_ts')
            ->values();

        $diagnosticItems = DiagnosticOrderItem::query()
            ->whereHas('order', function ($query) use ($patient) {
                $query->where('patient_id', $patient->id);
            })
            ->with([
                'order.visitable',
                'parameters' => function ($q) {
                    $q->orderBy('sort_order')->orderBy('id');
                },
            ])
            ->latest('id')
            ->get();

        $pathologyVisits = $diagnosticItems->where('department', 'pathology')->values();
        $radiologyVisits = $diagnosticItems->where('department', 'radiology')->values();

        $pathologyLabResultRows = collect();
        foreach ($pathologyVisits as $pathItem) {
            $order = $pathItem->order;
            $visitable = $order?->visitable;
            $visitLabel = '—';
            if ($order && (string) $order->visitable_type === OpdPatient::class && $visitable) {
                $visitLabel = 'OPD · ' . ($visitable->case_no ?? '—');
            } elseif ($order && (string) $order->visitable_type === BedAllocation::class && $visitable) {
                $visitLabel = 'IPD · ' . ($visitable->admission_no ?? '—');
            }

            $reportTs = $pathItem->reported_at ?? $pathItem->updated_at;
            $params = $pathItem->parameters;
            $hasParamResults = false;

            foreach ($params as $param) {
                if (! filled($param->result_value)) {
                    continue;
                }
                $hasParamResults = true;
                $unit = filled($param->unit_name) ? ' ' . $param->unit_name : '';
                $pathologyLabResultRows->push([
                    'test_label' => $param->parameter_name,
                    'context_line' => collect([$pathItem->test_name, $visitLabel !== '—' ? $visitLabel : null])
                        ->filter()
                        ->implode(' · ') ?: '—',
                    'result' => trim((string) $param->result_value . $unit),
                    'ref_range' => $param->normal_range ?: '—',
                    'result_flag' => $param->result_flag,
                    'dated_at' => $param->updated_at ?? $reportTs,
                    'item_id' => $pathItem->id,
                    'item_status' => $pathItem->status,
                ]);
            }

            if (! $hasParamResults && $pathItem->status === 'completed'
                && (filled($pathItem->report_summary) || filled($pathItem->report_text))) {
                $summary = $pathItem->report_summary ?: strip_tags((string) $pathItem->report_text);
                $pathologyLabResultRows->push([
                    'test_label' => $pathItem->test_name,
                    'context_line' => $visitLabel,
                    'result' => Str::limit(trim((string) $summary), 160),
                    'ref_range' => '—',
                    'result_flag' => null,
                    'dated_at' => $reportTs,
                    'item_id' => $pathItem->id,
                    'item_status' => $pathItem->status,
                ]);
            }
        }

        $pathologyLabResultRows = $pathologyLabResultRows
            ->sortByDesc(function ($row) {
                $d = $row['dated_at'] ?? null;

                return $d ? Carbon::parse($d)->timestamp : 0;
            })
            ->values()
            ->reject(fn ($row) => ($row['result_flag'] ?? null) === 'normal')
            ->values();

        $pathologyAbnormalCount = $pathologyLabResultRows->filter(function ($row) {
            return in_array($row['result_flag'] ?? null, ['low', 'high', 'critical_low', 'critical_high'], true);
        })->count();

        $diagnosticOpdVisitIds = $diagnosticItems
            ->pluck('order.visitable_id')
            ->filter()
            ->unique()
            ->values();

        $opdVisitsById = OpdPatient::query()
            ->whereIn('id', $diagnosticOpdVisitIds)
            ->with(['consultant:id,first_name,last_name'])
            ->get()
            ->keyBy('id');

        $patientCharges = PatientCharge::query()
            ->where('patient_id', $patient->id)
            ->orderByDesc('charged_at')
            ->orderByDesc('id')
            ->get();

        $totalCharges = (float) $patientCharges->sum('amount');
        $totalPaid = (float) $patientCharges->sum('paid_amount');
        $totalDue = (float) $patientCharges->sum(function (PatientCharge $charge) {
            return max(0, (float) $charge->amount - (float) $charge->paid_amount);
        });

        $latestOpdStatus = strtolower((string) optional($latestOpdVisit)->status);
        $latestOpdCompleted = $latestOpdVisit && $latestOpdStatus === 'completed';

        $lastBedAllocation = BedAllocation::query()
            ->where('hospital_id', $this->hospital_id)
            ->where('patient_id', $patient->id)
            ->orderByDesc('admission_date')
            ->orderByDesc('id')
            ->first(['id', 'discharge_date']);

        $lastIpdEpisodeDischarged = $lastBedAllocation && $lastBedAllocation->discharge_date !== null;

        if ($activeIpdAllocation) {
            $canPatient360NewOrder = true;
            $patient360NewOrderBlockedReason = null;
        } elseif ($latestOpdCompleted) {
            $canPatient360NewOrder = false;
            $patient360NewOrderBlockedReason = 'This OPD visit is completed. New orders cannot be placed from Patient 360.';
        } elseif ($lastIpdEpisodeDischarged && ! $latestOpdVisit) {
            $canPatient360NewOrder = false;
            $patient360NewOrderBlockedReason = 'Patient has been discharged from IPD. Register or open an OPD visit to place new orders.';
        } else {
            $canPatient360NewOrder = true;
            $patient360NewOrderBlockedReason = null;
        }

        return view('hospital.patient-management.patient-360', [
            'patient' => $patient,
            'activeIpdAllocation' => $activeIpdAllocation,
            'patient360CanIpdDischarge' => $patient360CanIpdDischarge,
            'latestOpdVisit' => $latestOpdVisit,
            'canPatient360NewOrder' => $canPatient360NewOrder,
            'patient360NewOrderBlockedReason' => $patient360NewOrderBlockedReason,
            'visits' => $visits,
            'visitContext' => $visitContext,
            'timelineEntries' => $timelineEntries,
            'clinicalNotes' => $clinicalNotes,
            'prescriptionVisits' => $prescriptionVisits,
            'ipdPrescriptionVisits' => $ipdPrescriptionVisits,
            'medicationRows' => $medicationRows,
            'pathologyVisits' => $pathologyVisits,
            'pathologyLabResultRows' => $pathologyLabResultRows,
            'pathologyAbnormalCount' => $pathologyAbnormalCount,
            'radiologyVisits' => $radiologyVisits,
            'opdVisitsById' => $opdVisitsById,
            'patientCharges' => $patientCharges,
            'totalCharges' => $totalCharges,
            'totalPaid' => $totalPaid,
            'totalDue' => $totalDue,
        ]);
    }
    public function mrnPreview()
    {
        return response()->json([
            'mrn' => $this->generateMrn(),
            'format' => $this->getMrnFormat(),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // AJAX – LOAD DOCTORS BY DEPARTMENT
    // ─────────────────────────────────────────────────────────────────────────

    public function loadDoctors(Request $request)
    {
        $deptId = $request->input('dept_id');

        $query = Staff::query()
            ->where('hospital_id', $this->hospital_id)
            ->doctor()
            ->active()
            ->select('id', 'first_name', 'last_name', 'slot_duration', 'work_timings');

        if ($deptId) {
            $query->where('hr_department_id', $deptId);
        }

        $doctors = $query->orderBy('first_name')->get()->map(function ($s) {
            return [
                'id'   => $s->id,
                'name' => trim($s->first_name . ' ' . $s->last_name),
            ];
        });

        return response()->json($doctors);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // AJAX – LOAD DOCTOR SLOTS (for date)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Returns available (not yet booked) time slots for a doctor on a given date.
     * Slots are generated from the doctor's work_timings + slot_duration.
     * Already-booked slots (opd_patients with matching doctor_id + date + slot) are excluded.
     * Manually blocked slots (doctor_slots table) are also excluded.
     */
    public function loadDoctorSlots(Request $request)
    {
        $doctorId  = $request->input('doctor_id');
        $dateInput = $request->input('date'); // Y-m-d or d-m-Y

        if (!$doctorId) {
            return response()->json([]);
        }

        // Parse date
        try {
            $date = Carbon::parse($dateInput)->startOfDay();
        } catch (\Throwable $e) {
            $date = now()->startOfDay();
        }

        $staff = Staff::where('id', $doctorId)
            ->where('hospital_id', $this->hospital_id)
            ->doctor()
            ->active()
            ->first();

        if (!$staff) {
            return response()->json([]);
        }

        $dayName   = strtolower($date->format('l')); // e.g. 'tuesday'
        $timings   = is_array($staff->work_timings) ? $staff->work_timings : json_decode($staff->work_timings ?? '[]', true);
        $dayTiming = null;

        foreach ((array) $timings as $day => $timing) {
            if (strtolower((string) $day) === $dayName) {
                $dayTiming = $timing;
                break;
            }
        }

        if (!$dayTiming || empty($dayTiming['start_time']) || empty($dayTiming['end_time'])) {
            return response()->json([]);
        }

        $slotDuration = max(5, (int) ($staff->slot_duration ?: 20));
        $startTime    = Carbon::createFromFormat('H:i', $dayTiming['start_time'])->setDateFrom($date);
        $endTime      = Carbon::createFromFormat('H:i', $dayTiming['end_time'])->setDateFrom($date);

        // Generate all slots
        $allSlots = [];
        $cursor   = $startTime->copy();
        while ($cursor->lt($endTime)) {
            $slotEnd    = $cursor->copy()->addMinutes($slotDuration);
            $slotLabel  = $cursor->format('h:i A') . ' - ' . ($slotEnd->gt($endTime) ? $endTime->format('h:i A') : $slotEnd->format('h:i A'));
            $allSlots[] = $slotLabel;
            $cursor->addMinutes($slotDuration);
        }

        // Booked slots for this doctor on this date
        $bookedSlots = OpdPatient::query()
            ->where('hospital_id', $this->hospital_id)
            ->where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date->toDateString())
            ->whereNotNull('slot')
            ->pluck('slot')
            ->map(fn ($s) => trim($s))
            ->unique()
            ->values()
            ->all();

        // Manually blocked slots
        $blockedSlots = DoctorSlot::query()
            ->where('hospital_id', $this->hospital_id)
            ->where('doctor_id', $doctorId)
            ->where('is_blocked', true)
            ->where(function ($q) use ($date, $dayName) {
                $q->whereDate('slot_date', $date->toDateString())
                    ->orWhere(function ($q2) use ($date, $dayName) {
                        $q2->whereNull('slot_date')
                            ->where('day_of_week', $date->dayOfWeek);
                    });
            })
            ->pluck('slot_label')
            ->map(fn ($s) => trim($s))
            ->unique()
            ->values()
            ->all();

        $unavailable = array_merge($bookedSlots, $blockedSlots);

        // If date is today, filter out past slots
        $now = now();
        $isToday = $date->isSameDay($now);

        $availableSlots = array_values(array_filter($allSlots, function ($slot) use ($unavailable, $isToday, $now, $date) {
            if (in_array($slot, $unavailable, true)) {
                return false;
            }
            if ($isToday) {
                // Parse slot start time and compare with now
                $parts     = explode(' - ', $slot);
                $slotStart = $parts[0] ?? '';
                try {
                    $slotTime = Carbon::createFromFormat('h:i A', trim($slotStart))->setDateFrom($date);
                    if ($slotTime->lte($now)) {
                        return false;
                    }
                } catch (\Throwable $e) {
                    // If parse fails, include the slot
                }
            }
            return true;
        }));

        return response()->json(array_map(fn ($s) => ['label' => $s], $availableSlots));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // AJAX – AVAILABLE BEDS (by ward/bed-type)
    // ─────────────────────────────────────────────────────────────────────────

    public function availableBeds(Request $request)
    {
        $bedTypeId = $request->input('bed_type_id');

        $query = Bed::query()
            ->where('beds.hospital_id', $this->hospital_id)
            ->where('beds.bed_status_id', BedStatus::AVAILABLE)
            ->whereDoesntHave('bedAllocations', function ($query) {
                $query->whereNull('discharge_date');
            })
            ->with([
                'bedType:id,type_name,base_charge',
                'room:id,room_number,ward_id',
                'room.ward:id,ward_name',
            ])
            ->select('beds.id', 'beds.bed_code as bed_no', 'beds.bed_type_id', 'beds.room_id')
            ->orderBy('beds.bed_number');

        if ($bedTypeId) {
            $query->where('beds.bed_type_id', $bedTypeId);
        }

        return response()->json($query->get()->map(function ($b) {
            return [
                'id'        => $b->id,
                'bed_no'    => $b->bed_no,
                'ward'      => $b->room?->ward?->ward_name ?: '-',
                'room_no'   => $b->room?->room_number ?: '-',
                'bed_type'  => $b->bedType?->type_name ?: '-',
                'rate'      => $b->bedType?->base_charge ?? 0,
            ];
        }));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // AJAX – LOAD DISTRICTS BY STATE
    // ─────────────────────────────────────────────────────────────────────────

    public function loadDistricts(Request $request)
    {
        $stateId = $request->input('state_id');

        $districts = IndianDistrict::query()
            ->where('state_id', $stateId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($districts);
    }

    public function getOpdCharge(Request $request)
    {
        $departmentId = (int) $request->input('hr_department_id');
        $doctorId = $request->filled('doctor_id') ? (int) $request->input('doctor_id') : null;
        $tpaId = $request->filled('tpa_id') ? (int) $request->input('tpa_id') : null;
        $visitType = (string) $request->input('visit_type', 'OPD');

        $charge = 0.0;
        $standardCharge = 0.0;
        $applyChargeType = 'department';

        $department = HrDepartment::query()
            ->where('id', $departmentId)
            ->where('hospital_id', $this->hospital_id)
            ->first();

        if ($department) {
            $charge = (float) ($department->charge ?? 0);
            $standardCharge = $charge;
        }

        if ($doctorId) {
            $doctorCharge = DoctorOpdCharge::query()
                ->where('doctor_id', $doctorId)
                ->first();

            if ($doctorCharge) {
                $isEmergencyVisit = strtolower($visitType) === 'emergency';
                $resolvedDoctorCharge = $isEmergencyVisit && $doctorCharge->emergency_charge !== null
                    ? (float) $doctorCharge->emergency_charge
                    : (float) $doctorCharge->charge;

                $charge = $resolvedDoctorCharge;
                $standardCharge = $resolvedDoctorCharge;
                $applyChargeType = $isEmergencyVisit && $doctorCharge->emergency_charge !== null ? 'doctor_emergency' : 'doctor';

                if ($tpaId) {
                    $tpaCharge = TpaOpdCharge::query()
                        ->where('doctor_opd_charge_id', $doctorCharge->id)
                        ->where('tpa_id', $tpaId)
                        ->first();

                    if ($tpaCharge) {
                        $charge = (float) $tpaCharge->charge;
                        $applyChargeType = 'tpa';
                    }
                }
            }
        }

        return response()->json([
            'status' => true,
            'charge' => $charge,
            'standard_charge' => $standardCharge,
            'apply_charge_type' => $applyChargeType,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PATIENT REGISTRATION – FULL (5-step form submit)
    // ─────────────────────────────────────────────────────────────────────────

    public function register(
        Request $request,
        ChargeLedgerService $chargeLedger,
        PatientTimelineService $timelineService,
        BedAllocationService $bedAllocationService
    ) {
        $validator = Validator::make($request->all(), [
            // Step 1 – Personal
            'name'                      => 'required|string|max:255',
            'title'                     => 'nullable|in:Mr.,Mrs.,Ms.,Dr.,Baby',
            'date_of_birth'             => 'nullable|date_format:Y-m-d',
            'age_years'                 => 'required|integer|min:0|max:150',
            'gender'                    => 'required|in:Male,Female,Other',
            'blood_group'               => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'marital_status'            => 'nullable|string|max:50',
            'aadhar_no'                 => 'nullable|string|max:20',
            'ayushman_bharat_id'        => 'nullable|string|max:100',
            'occupation'                => 'nullable|string|max:100',
            'category'                  => 'nullable|in:General,OBC,SC,ST,EWS',
            'patient_category_id'       => [
                'nullable',
                Rule::exists('patient_categories', 'id')->where(function ($query) {
                    $query->where('hospital_id', $this->hospital_id);
                }),
            ],
            'religion_id'               => 'nullable|exists:religions,id',
            // Step 2 – Contact
            'phone'                     => 'required|digits_between:7,15',
            'alternate_phone'           => 'nullable|digits_between:7,15',
            'email'                     => 'nullable|email|max:255',
            'address'                   => 'nullable|string|max:500',
            'pin_code'                  => 'nullable|string|max:10',
            'district'                  => 'nullable|string|max:100',
            'state'                     => 'nullable|string|max:100',
            'emergency_contact_name'    => 'nullable|string|max:150',
            'emergency_contact_relation' => 'nullable|string|max:50',
            'emergency_contact_phone'   => 'nullable|digits_between:7,15',
            // Step 3 – Medical history
            'known_allergies'           => 'nullable|string',
            'chronic_conditions'        => 'nullable|array',
            'chronic_conditions.*'      => 'nullable|string',
            'past_surgical_history'     => 'nullable|string',
            'current_medications'       => 'nullable|string',
            'family_history'            => 'nullable|string',
            'smoking_status'            => 'nullable|in:Never,Current,Past',
            'alcohol_status'            => 'nullable|in:Never,Occasional,Regular',
            'vaccination_status'        => 'nullable|in:Up to date,Partial,Unknown,None',
            // Step 4 – Visit type
            'visit_type'                => 'required|in:OPD,IPD,Emergency,Daycare',
            'hr_department_id'          => 'required|exists:hr_departments,id',
            'doctor_id'                 => [
                Rule::requiredIf(function () use ($request) {
                    $visitType = $request->input('visit_type');
                    if (in_array($visitType, ['OPD', 'Daycare'], true)) {
                        return true;
                    }

                    return $visitType === 'Emergency' && !$request->filled('bed_id');
                }),
                'nullable',
                'exists:staff,id',
            ],
            'chief_complaint'           => 'nullable|string|max:1000',
            'payment_mode'              => 'nullable|string|max:50',
            'applied_charge'            => 'nullable|numeric|min:0',
            'advance_deposit'           => 'nullable|numeric|min:0',
            'tpa_id'                    => 'nullable|exists:tpas,id',
            'tpa_reference_no'          => 'nullable|string|max:255',
            // OPD-specific
            'slot'                      => [
                Rule::requiredIf(function () use ($request) {
                    $visitType = $request->input('visit_type');
                    if (in_array($visitType, ['OPD', 'Daycare'], true)) {
                        return true;
                    }

                    return $visitType === 'Emergency' && !$request->filled('bed_id');
                }),
                'nullable',
                'string',
                'max:60',
            ],
            'appointment_date'          => 'nullable|date_format:Y-m-d',
            'appointment_time'          => 'nullable|date_format:H:i',
            'casualty'                  => 'nullable|in:Yes,No',
            // IPD-specific
            'bed_id'                    => 'nullable|exists:beds,id',
            'admission_reason'          => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        // Phone uniqueness: cannot belong to another hospital
        $existingPhone = Patient::query()
            ->where('phone', $request->phone)
            ->where('hospital_id', '!=', $this->hospital_id)
            ->exists();

        if ($existingPhone) {
            return response()->json([
                'errors' => [['code' => 'phone', 'message' => 'This phone number is already registered at another hospital.']],
            ], 422);
        }

        try {
            $result = DB::transaction(function () use ($request, $chargeLedger, $timelineService, $bedAllocationService) {
                $selectedPatientCategory = null;
                if ($request->filled('patient_category_id')) {
                    $selectedPatientCategory = PatientCategory::query()
                        ->where('hospital_id', $this->hospital_id)
                        ->find($request->patient_category_id);
                }

                $legacyCategory = $request->category;
                if (!$legacyCategory && $selectedPatientCategory) {
                    $normalizedCategory = strtoupper(trim((string) $selectedPatientCategory->name));
                    $legacyCategory = [
                        'GENERAL' => 'General',
                        'OBC' => 'OBC',
                        'SC' => 'SC',
                        'ST' => 'ST',
                        'EWS' => 'EWS',
                    ][$normalizedCategory] ?? 'General';
                }

                // ── 1. Upsert Patient ──────────────────────────────────────────
                $patient = Patient::query()
                    ->where('hospital_id', $this->hospital_id)
                    ->where('phone', $request->phone)
                    ->first();

                $isNew = !$patient;

                if ($isNew) {
                    $patient = new Patient();
                    $patient->hospital_id = $this->hospital_id;
                    $patient->patient_id  = $this->generatePatientCode();
                    $patient->mrn         = $this->generateMrn();
                }

                // Apply all fields from the form
                $patient->title                     = $request->title;
                $patient->name                      = $request->name;
                $patient->date_of_birth             = $request->date_of_birth ?: null;
                $patient->age_years                 = $request->age_years;
                $patient->age_months                = $request->input('age_months', 0);
                $patient->gender                    = $request->gender;
                $patient->blood_group               = $request->blood_group;
                $patient->marital_status            = $request->marital_status;
                $patient->aadhar_no                 = $request->aadhar_no;
                $patient->ayushman_bharat_id        = $request->ayushman_bharat_id;
                $patient->occupation                = $request->occupation;
                $patient->patient_category_id       = $selectedPatientCategory?->id;
                $patient->category                  = $legacyCategory ?: 'General';
                $patient->religion_id               = $request->religion_id;
                $patient->phone                     = $request->phone;
                $patient->alternate_phone           = $request->alternate_phone;
                $patient->country_code              = $request->input('country_code', '+91');
                $patient->email                     = $request->email;
                $patient->address                   = $request->address;
                $patient->pin_code                  = $request->pin_code;
                $patient->district                  = $request->district;
                $patient->state                     = $request->state;
                $patient->emergency_contact_name    = $request->emergency_contact_name;
                $patient->emergency_contact_relation = $request->emergency_contact_relation;
                $patient->emergency_contact_phone   = $request->emergency_contact_phone;
                $patient->known_allergies           = $request->known_allergies;
                $patient->chronic_conditions        = $request->input('chronic_conditions') ?: null;
                $patient->past_surgical_history     = $request->past_surgical_history;
                $patient->current_medications       = $request->current_medications;
                $patient->family_history            = $request->family_history;
                $patient->smoking_status            = $request->smoking_status ?: 'Never';
                $patient->alcohol_status            = $request->alcohol_status ?: 'Never';
                $patient->vaccination_status        = $request->vaccination_status ?: 'Unknown';
                $patient->is_staff                  = 'No';

                if ($request->hasFile('image')) {
                    if ($patient->image) {
                        Storage::disk('public')->delete($patient->image);
                    }
                    $patient->image = $request->file('image')->store('patients', 'public');
                }

                $patient->save();

                $visitType = $request->visit_type;
                $isEmergencyBedAdmission = $visitType === 'Emergency' && $request->filled('bed_id');
                $shouldCreateOpdVisit = in_array($visitType, ['OPD', 'Daycare'], true) || ($visitType === 'Emergency' && !$isEmergencyBedAdmission);
                $token     = null;
                $bookingNo = null;
                $caseNo    = null;
                $admissionNo = null;
                $bedNo     = null;

                // ── 2a. OPD Visit ──────────────────────────────────────────────
                if ($shouldCreateOpdVisit) {
                    // Duplicate active OPD check
                    $existingActive = $this->findBlockingOpdVisit($patient->id);

                    if ($existingActive) {
                        $tokenOrBooking = $existingActive->token_no
                            ? str_pad($existingActive->token_no, 3, '0', STR_PAD_LEFT)
                            : ($existingActive->booking_number ?: '-');

                        throw new \RuntimeException(
                            'DUPLICATE_OPD:' . $existingActive->case_no . ':' . $tokenOrBooking
                        );
                    }

                    // Build appointment datetime
                    $apptDate  = $request->appointment_date ?: now()->toDateString();
                    $apptTime  = $request->appointment_time ?: now()->format('H:i');
                    $appointmentDate = Carbon::createFromFormat('Y-m-d H:i', "$apptDate $apptTime");

                    $caseNo = $this->generateDailyCaseNo($appointmentDate);
                    $bookingNo = $this->generateDailyBookingNumber($appointmentDate);
                    $tokenNo = $this->shouldAssignTokenNow($appointmentDate, $request->slot)
                        ? $this->generateSlotWiseTokenNo($appointmentDate, $request->slot)
                        : null;

                    $opdPatient = OpdPatient::create([
                        'hospital_id'    => $this->hospital_id,
                        'patient_id'     => $patient->id,
                        'doctor_id'      => $request->doctor_id,
                        'hr_department_id' => $request->hr_department_id,
                        'appointment_date' => $appointmentDate->format('Y-m-d H:i:s'),
                        'case_no'        => $caseNo,
                        'booking_number' => $bookingNo,
                        'token_no'       => $tokenNo,
                        'casualty'       => ($visitType === 'Emergency') ? 'Yes' : ($request->casualty ?? 'No'),
                        'mlc_patient'    => $request->input('mlc_patient', 'No'),
                        'visit_type'     => $visitType,
                        'tpa_id'         => $request->tpa_id,
                        'tpa_reference_no' => $request->tpa_reference_no,
                        'slot'           => $request->slot,
                        'symptoms_description' => $request->chief_complaint,
                        'applied_charge' => (float) ($request->applied_charge ?? 0),
                        'standard_charge' => (float) ($request->applied_charge ?? 0),
                        'payment_mode'   => $this->normalizePaymentMode($request->payment_mode),
                        'live_consultation' => 'No',
                        'status'         => $tokenNo ? 'waiting' : 'booking',
                    ]);

                    // Charge ledger entry
                    if ((float) $opdPatient->applied_charge > 0) {
                        $charge = $chargeLedger->upsertCharge([
                            'hospital_id'       => $this->hospital_id,
                            'patient_id'        => $patient->id,
                            'visitable_type'    => OpdPatient::class,
                            'visitable_id'      => $opdPatient->id,
                            'source_type'       => OpdPatient::class,
                            'source_id'         => $opdPatient->id,
                            'module'            => 'opd',
                            'particular'        => 'OPD Consultation - ' . $caseNo,
                            'charge_category'   => 'opd_consultation',
                            'calculation_type'  => 'fixed',
                            'billing_frequency' => 'one_time',
                            'quantity'          => 1,
                            'unit_rate'         => $opdPatient->applied_charge,
                            'net_amount'        => $opdPatient->applied_charge,
                            'payer_type'        => $opdPatient->tpa_id ? 'tpa' : 'self',
                            'tpa_id'            => $opdPatient->tpa_id,
                            'charged_at'        => $opdPatient->appointment_date,
                        ]);

                        if ($this->normalizePaymentMode($request->payment_mode)) {
                            $chargeLedger->collectPayment($patient, [
                                'amount'       => (float) $opdPatient->applied_charge,
                                'payment_mode' => $this->normalizePaymentMode($request->payment_mode),
                            ], [$charge->id]);
                        }
                    }

                    $timelineService->logForOpdVisit($opdPatient, [
                        'event_key'   => 'opd.visit.created',
                        'title'       => 'OPD Visit Registered',
                        'description' => $tokenNo
                            ? "Case {$caseNo} - Token " . str_pad($tokenNo, 3, '0', STR_PAD_LEFT)
                            : "Case {$caseNo} - Booking {$bookingNo}",
                        'meta'        => [
                            'case_no' => $caseNo,
                            'booking_number' => $bookingNo,
                            'token_no' => $tokenNo,
                        ],
                        'logged_at'   => $appointmentDate,
                    ]);

                    $token = $tokenNo ? str_pad($tokenNo, 3, '0', STR_PAD_LEFT) : null;
                }

                // ── 2b. IPD Admission ──────────────────────────────────────────
                $shouldAdmit = $visitType === 'IPD' || $isEmergencyBedAdmission;
                if ($shouldAdmit) {
                    if ($visitType === 'IPD') {
                        $existingActive = $this->findBlockingOpdVisit($patient->id);
                        if ($existingActive) {
                            throw new \RuntimeException(
                                'ACTIVE_OPD_CONFLICT:' . $existingActive->case_no . ':' . str_pad($existingActive->token_no, 3, '0', STR_PAD_LEFT)
                            );
                        }
                    }

                    $bedId = $request->bed_id;

                    if (!$bedId) {
                        throw new \RuntimeException('Please select a bed for IPD admission.');
                    }

                    $bed = Bed::query()
                        ->where('id', $bedId)
                        ->where('hospital_id', $this->hospital_id)
                        ->first();

                    if (!$bed) {
                        throw new \RuntimeException('Invalid bed selected.');
                    }

                    // Check bed is still available
                    $alreadyOccupied = BedAllocation::query()
                        ->where('bed_id', $bedId)
                        ->whereNull('discharge_date')
                        ->exists();

                    if ($alreadyOccupied) {
                        throw new \RuntimeException('Selected bed is no longer available. Please choose another bed.');
                    }

                    $admissionNo = $this->generateAdmissionNo();
                    $admissionType = $visitType === 'Emergency' ? 'emergency' : 'planned';
                    $admissionSource = $visitType === 'Emergency' ? 'emergency' : 'reception';

                    $allocation = $bedAllocationService->allocateBed(
                        $this->hospital_id,
                        $patient->id,
                        $bedId,
                        null,
                        $admissionType,
                        $request->admission_reason ?: $request->chief_complaint,
                        [
                            'admission_no'         => $admissionNo,
                            'admission_date'       => now()->format('Y-m-d H:i:s'),
                            'consultant_doctor_id' => $request->doctor_id,
                            'hr_department_id'     => $request->hr_department_id,
                            'tpa_id'               => $request->tpa_id,
                            'tpa_reference_no'     => $request->tpa_reference_no,
                            'admission_reason'     => $request->admission_reason ?: $request->chief_complaint,
                            'admission_source'     => $admissionSource,
                        ]
                    );

                    // Initial deposit / charge
                    if ((float) ($request->advance_deposit ?? 0) > 0) {
                        $chargeLedger->upsertCharge([
                            'hospital_id'       => $this->hospital_id,
                            'patient_id'        => $patient->id,
                            'visitable_type'    => BedAllocation::class,
                            'visitable_id'      => $allocation->id,
                            'source_type'       => BedAllocation::class,
                            'source_id'         => $allocation->id,
                            'module'            => 'ipd',
                            'particular'        => 'IPD Admission Deposit - ' . $admissionNo,
                            'charge_category'   => 'ipd_admission',
                            'calculation_type'  => 'fixed',
                            'billing_frequency' => 'one_time',
                            'quantity'          => 1,
                            'unit_rate'         => (float) $request->advance_deposit,
                            'net_amount'        => (float) $request->advance_deposit,
                            'payer_type'        => $request->tpa_id ? 'tpa' : 'self',
                            'tpa_id'            => $request->tpa_id,
                            'charged_at'        => now()->format('Y-m-d H:i:s'),
                        ]);
                    }

                    $timelineService->logForIpdAdmission($allocation, [
                        'event_key'   => 'ipd.admission.created',
                        'title'       => 'IPD Admission',
                        'description' => "Admitted via new registration. Admission No: {$admissionNo}",
                        'meta'        => ['admission_no' => $admissionNo],
                        'logged_at'   => now()->format('Y-m-d H:i:s'),
                    ]);

                    $bedNo = $bed->bed_number;
                }

                return compact('patient', 'token', 'bookingNo', 'caseNo', 'admissionNo', 'bedNo', 'visitType', 'isNew');
            });

            return response()->json([
                'status'       => true,
                'message'      => 'Registration successful.',
                'mrn'          => $result['patient']->mrn,
                'patient_name' => $result['patient']->name,
                'visit_type'   => $result['visitType'],
                'token'        => $result['token'],
                'booking_number' => $result['bookingNo'],
                'case_no'      => $result['caseNo'],
                'admission_no' => $result['admissionNo'],
                'bed_no'       => $result['bedNo'],
                'is_new'       => $result['isNew'],
            ]);
        } catch (\Throwable $e) {
            if (str_starts_with($e->getMessage(), 'ACTIVE_OPD_CONFLICT:')) {
                return response()->json([
                    'errors' => [[
                        'code' => 'phone',
                        'message' => $this->activeOpdConflictMessage($e->getMessage()),
                    ]],
                ], 422);
            }

            if (str_starts_with($e->getMessage(), 'DUPLICATE_OPD:')) {
                $parts = explode(':', $e->getMessage());
                return response()->json([
                    'errors' => [['code' => 'phone', 'message' => "Patient already has an active OPD visit. Case: {$parts[1]}, Token/Booking: {$parts[2]}"]],
                ], 422);
            }

            return response()->json([
                'status'  => false,
                'message' => $e->getMessage() ?: 'Registration failed. Please try again.',
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // OPD TOKEN – Quick revisit (existing patient)
    // ─────────────────────────────────────────────────────────────────────────

    public function issueToken(
        Request $request,
        ChargeLedgerService $chargeLedger,
        PatientTimelineService $timelineService
    ) {
        $validator = Validator::make($request->all(), [
            'patient_id'      => 'required|exists:patients,id',
            'hr_department_id' => 'required|exists:hr_departments,id',
            'doctor_id'       => 'required|exists:staff,id',
            'slot'            => 'required|string|max:60',
            'visit_type'      => 'nullable|in:OPD,Emergency,Daycare',
            'appointment_date' => 'nullable|date_format:Y-m-d',
            'appointment_time' => 'nullable|date_format:H:i',
            'chief_complaint'  => 'nullable|string|max:1000',
            'applied_charge'   => 'nullable|numeric|min:0',
            'payment_mode'     => 'nullable|string|max:50',
            'tpa_id'           => 'nullable|exists:tpas,id',
            'priority'         => 'nullable|in:Normal,Urgent,STAT',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $patient = Patient::query()
            ->where('id', $request->patient_id)
            ->where('hospital_id', $this->hospital_id)
            ->first();

        if (!$patient) {
            return response()->json(['errors' => [['code' => 'patient_id', 'message' => 'Patient not found.']]], 422);
        }

        // Duplicate active check
        $existingActive = OpdPatient::query()
            ->where('hospital_id', $this->hospital_id)
            ->where('patient_id', $patient->id)
            ->whereIn('status', ['waiting', 'in_room'])
            ->first();

        if ($existingActive) {
            return response()->json([
                'errors' => [['code' => 'patient_id', 'message' => 'Patient already has an active OPD visit (Case: ' . $existingActive->case_no . '). Complete that first.']],
            ], 422);
        }

        try {
            $result = DB::transaction(function () use ($request, $patient, $chargeLedger, $timelineService) {
                $apptDate = $request->appointment_date ?: now()->toDateString();
                $apptTime = $request->appointment_time ?: now()->format('H:i');
                $appointmentDate = Carbon::createFromFormat('Y-m-d H:i', "$apptDate $apptTime");
                $visitType = in_array($request->visit_type, ['OPD', 'Emergency', 'Daycare'], true)
                    ? $request->visit_type
                    : 'OPD';

                $caseNo  = $this->generateDailyCaseNo($appointmentDate);
                $bookingNumber = $this->generateDailyBookingNumber($appointmentDate);
                $tokenNo = $this->shouldAssignTokenNow($appointmentDate, $request->slot)
                    ? $this->generateSlotWiseTokenNo($appointmentDate, $request->slot)
                    : null;

                $opdPatient = OpdPatient::create([
                    'hospital_id'       => $this->hospital_id,
                    'patient_id'        => $patient->id,
                    'doctor_id'         => $request->doctor_id,
                    'hr_department_id'  => $request->hr_department_id,
                    'appointment_date'  => $appointmentDate->format('Y-m-d H:i:s'),
                    'case_no'           => $caseNo,
                    'booking_number'    => $bookingNumber,
                    'token_no'          => $tokenNo,
                    'visit_type'        => $visitType,
                    'slot'              => $request->slot,
                    'casualty'          => $visitType === 'Emergency' ? 'Yes' : 'No',
                    'mlc_patient'       => 'No',
                    'tpa_id'            => $request->tpa_id,
                    'symptoms_description' => $request->chief_complaint,
                    'applied_charge'    => (float) ($request->applied_charge ?? 0),
                    'standard_charge'   => (float) ($request->applied_charge ?? 0),
                    'payment_mode'      => $this->normalizePaymentMode($request->payment_mode),
                    'live_consultation' => 'No',
                    'status'         => $tokenNo ? 'waiting' : 'booking',

                ]);

                if ((float) $opdPatient->applied_charge > 0) {
                    $charge = $chargeLedger->upsertCharge([
                        'hospital_id'       => $this->hospital_id,
                        'patient_id'        => $patient->id,
                        'visitable_type'    => OpdPatient::class,
                        'visitable_id'      => $opdPatient->id,
                        'source_type'       => OpdPatient::class,
                        'source_id'         => $opdPatient->id,
                        'module'            => 'opd',
                        'particular'        => 'OPD Revisit - ' . $caseNo,
                        'charge_category'   => 'opd_consultation',
                        'calculation_type'  => 'fixed',
                        'billing_frequency' => 'one_time',
                        'quantity'          => 1,
                        'unit_rate'         => $opdPatient->applied_charge,
                        'net_amount'        => $opdPatient->applied_charge,
                        'payer_type'        => $opdPatient->tpa_id ? 'tpa' : 'self',
                        'tpa_id'            => $opdPatient->tpa_id,
                        'charged_at'        => $opdPatient->appointment_date,
                    ]);

                    if ($this->normalizePaymentMode($request->payment_mode)) {
                        $chargeLedger->collectPayment($patient, [
                            'amount'       => (float) $opdPatient->applied_charge,
                            'payment_mode' => $this->normalizePaymentMode($request->payment_mode),
                        ], [$charge->id]);
                    }
                }

                $timelineService->logForOpdVisit($opdPatient, [
                    'event_key'   => 'opd.visit.created',
                    'title'       => 'OPD Booking Created',
                    'description' => $tokenNo
                        ? "Booking {$bookingNumber} - Case {$caseNo} - Token " . str_pad($tokenNo, 3, '0', STR_PAD_LEFT)
                        : "Booking {$bookingNumber} - Case {$caseNo}",
                    'meta'        => [
                        'case_no' => $caseNo,
                        'booking_number' => $bookingNumber,
                        'token_no' => $tokenNo,
                    ],
                    'logged_at'   => $appointmentDate,
                ]);

                return compact('opdPatient', 'caseNo', 'bookingNumber', 'tokenNo');
            });

            return response()->json([
                'status'  => true,
                'message' => $result['tokenNo']
                    ? 'OPD booking created successfully and token assigned.'
                    : 'OPD booking created successfully. Token will be assigned at check-in.',
                'booking_number' => $result['bookingNumber'],
                'token' => $result['tokenNo'] ? str_pad((string) $result['tokenNo'], 3, '0', STR_PAD_LEFT) : null,
                'case_no' => $result['caseNo'],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // IPD ADMIT – Quick admit existing patient
    // ─────────────────────────────────────────────────────────────────────────

    public function ipdAdmit(
        Request $request,
        BedAllocationService $bedAllocationService,
        ChargeLedgerService $chargeLedger,
        PatientTimelineService $timelineService
    ) {
        $validator = Validator::make($request->all(), [
            'patient_id'      => 'required|exists:patients,id',
            'hr_department_id' => 'required|exists:hr_departments,id',
            'bed_id'          => 'required|exists:beds,id',
            'doctor_id'       => 'nullable|exists:staff,id',
            'admission_reason' => 'nullable|string',
            'tpa_id'          => 'nullable|exists:tpas,id',
            'advance_deposit' => 'nullable|numeric|min:0',
            'payment_mode'    => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $patient = Patient::query()
            ->where('id', $request->patient_id)
            ->where('hospital_id', $this->hospital_id)
            ->first();

        if (!$patient) {
            return response()->json(['errors' => [['code' => 'patient_id', 'message' => 'Patient not found.']]], 422);
        }

        $activeOpdVisit = $this->findBlockingOpdVisit($patient->id);
        if ($activeOpdVisit) {
            return response()->json([
                'errors' => [[
                    'code' => 'patient_id',
                    'message' => $this->activeOpdConflictMessage($activeOpdVisit),
                ]],
            ], 422);
        }

        // Check bed belongs to this hospital
        $bed = Bed::query()->where('id', $request->bed_id)->where('hospital_id', $this->hospital_id)->first();
        if (!$bed) {
            return response()->json(['errors' => [['code' => 'bed_id', 'message' => 'Invalid bed.']]], 422);
        }

        $occupied = BedAllocation::query()->where('bed_id', $request->bed_id)->whereNull('discharge_date')->exists();
        if ($occupied) {
            return response()->json(['errors' => [['code' => 'bed_id', 'message' => 'Bed is already occupied.']]], 422);
        }

        try {
            $admissionNo = $this->generateAdmissionNo();

            $allocation = $bedAllocationService->allocateBed(
                $this->hospital_id,
                $patient->id,
                $request->bed_id,
                null,
                'direct',
                $request->admission_reason,
                [
                    'admission_no'         => $admissionNo,
                    'admission_date'       => now()->format('Y-m-d H:i:s'),
                    'consultant_doctor_id' => $request->doctor_id,
                    'hr_department_id'     => $request->hr_department_id,
                    'tpa_id'               => $request->tpa_id,
                    'tpa_reference_no'     => $request->tpa_reference_no,
                    'admission_reason'     => $request->admission_reason,
                    'admission_source'     => 'reception',
                ]
            );

            if ((float) ($request->advance_deposit ?? 0) > 0) {
                $chargeLedger->upsertCharge([
                    'hospital_id'       => $this->hospital_id,
                    'patient_id'        => $patient->id,
                    'visitable_type'    => BedAllocation::class,
                    'visitable_id'      => $allocation->id,
                    'source_type'       => BedAllocation::class,
                    'source_id'         => $allocation->id,
                    'module'            => 'ipd',
                    'particular'        => 'IPD Admission Deposit - ' . $admissionNo,
                    'charge_category'   => 'ipd_admission',
                    'calculation_type'  => 'fixed',
                    'billing_frequency' => 'one_time',
                    'quantity'          => 1,
                    'unit_rate'         => (float) $request->advance_deposit,
                    'net_amount'        => (float) $request->advance_deposit,
                    'payer_type'        => $request->tpa_id ? 'tpa' : 'self',
                    'tpa_id'            => $request->tpa_id,
                    'charged_at'        => now()->format('Y-m-d H:i:s'),
                ]);
            }

            $timelineService->logForIpdAdmission($allocation, [
                'event_key'   => 'ipd.admission.created',
                'title'       => 'IPD Admission',
                'description' => "Admitted. Bed: {$bed->bed_number}. Admission No: {$admissionNo}",
                'meta'        => ['admission_no' => $admissionNo, 'bed_no' => $bed->bed_number],
                'logged_at'   => now()->format('Y-m-d H:i:s'),
            ]);

            return response()->json([
                'status'       => true,
                'message'      => 'Patient admitted successfully.',
                'admission_no' => $admissionNo,
                'bed_no'       => $bed->bed_number,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    private function generateMrn(): string
    {
        $format = $this->getMrnFormat();
        $next = max(1, (int) Patient::query()->where('hospital_id', $this->hospital_id)->count() + 1);
        $attempts = 0;

        do {
            $mrn = $this->buildMrnFromFormat($format, $next);
            $next++;
            $attempts++;
        } while (Patient::query()->where('mrn', $mrn)->exists() && $attempts < 10000);

        if (Patient::query()->where('mrn', $mrn)->exists()) {
            $mrn = $this->buildMrnFromFormat($format, now()->timestamp);
        }

        return $mrn;
    }

    private function getMrnFormat(): string
    {
        $key = "hospital_{$this->hospital_id}_mrn_format";
        $value = BusinessSetting::where('key', $key)->value('value');
        return trim((string) ($value ?: 'MRN-{sequence:05}'));
    }

    private function buildMrnFromFormat(string $format, int $sequence): string
    {
        $replacements = [
            '{Y}' => now()->format('Y'),
            '{y}' => now()->format('y'),
            '{m}' => now()->format('m'),
            '{d}' => now()->format('d'),
        ];

        $resolved = strtr($format, $replacements);

        $resolved = preg_replace_callback('/\{hospital_id(?::(\d+))?\}/', function ($matches) {
            $pad = isset($matches[1]) ? (int) $matches[1] : 0;
            $hospitalId = (string) $this->hospital_id;
            return $pad > 0 ? str_pad($hospitalId, $pad, '0', STR_PAD_LEFT) : $hospitalId;
        }, $resolved);

        $resolved = preg_replace_callback('/\{sequence(?::(\d+))?\}/', function ($matches) use ($sequence) {
            $pad = isset($matches[1]) ? (int) $matches[1] : 0;
            $seq = (string) $sequence;
            return $pad > 0 ? str_pad($seq, $pad, '0', STR_PAD_LEFT) : $seq;
        }, $resolved);

        return $resolved;
    }

    private function findBlockingOpdVisit(int $patientId): ?OpdPatient
    {
        return OpdPatient::query()
            ->where('hospital_id', $this->hospital_id)
            ->where('patient_id', $patientId)
            ->whereIn('status', ['waiting', 'in_room'])
            ->orderByDesc('appointment_date')
            ->first();
    }

    private function activeOpdConflictMessage($visitOrEncodedMessage): string
    {
        if ($visitOrEncodedMessage instanceof OpdPatient) {
            $caseNo = $visitOrEncodedMessage->case_no ?: '—';
            $tokenNo = str_pad((string) ($visitOrEncodedMessage->token_no ?? 0), 3, '0', STR_PAD_LEFT);
            $slot = $visitOrEncodedMessage->slot ?: 'Booked';

            return "Patient ka OPD abhi active/booked hai (Case: {$caseNo}, Token: {$tokenNo}, Slot: {$slot}). OPD aur IPD dono ek sath allow nahi hain.";
        }

        [, $caseNo, $tokenNo] = array_pad(explode(':', (string) $visitOrEncodedMessage), 3, null);
        $caseNo = $caseNo ?: '—';
        $tokenNo = $tokenNo ?: '—';

        return "Patient ka OPD abhi active/booked hai (Case: {$caseNo}, Token: {$tokenNo}). OPD aur IPD dono ek sath allow nahi hain.";
    }

    /**
     * Patient code: P{hospital_id:04}{Ymd}{seq:04}
     */
    private function generatePatientCode(): string
    {
        $datePart = now()->format('Ymd');
        $prefix   = 'P' . str_pad((string) $this->hospital_id, 4, '0', STR_PAD_LEFT) . $datePart;

        $last = Patient::query()
            ->lockForUpdate()
            ->where('hospital_id', $this->hospital_id)
            ->where('patient_id', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('patient_id');

        $next = 1;
        if ($last && str_starts_with($last, $prefix)) {
            $next = ((int) substr($last, -4)) + 1;
        }

        do {
            $code = $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
            $next++;
        } while (Patient::query()->where('patient_id', $code)->exists());

        return $code;
    }

    /**
     * Daily OPD case number: OPD{Ymd}{seq:04}
     */
    private function generateDailyCaseNo(Carbon $date): string
    {
        $prefix = 'OPD' . $date->format('Ymd');

        $last = OpdPatient::query()
            ->lockForUpdate()
            ->where('hospital_id', $this->hospital_id)
            ->whereDate('appointment_date', $date->toDateString())
            ->where('case_no', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('case_no');

        $next = 1;
        if ($last && str_starts_with($last, $prefix)) {
            $next = ((int) substr($last, -4)) + 1;
        }

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Token number sequence by date per hospital.
     */
    private function generateSlotWiseTokenNo(Carbon $date, ?string $slot): int
    {
        $last = OpdPatient::query()
            ->lockForUpdate()
            ->where('hospital_id', $this->hospital_id)
            ->whereDate('appointment_date', $date->toDateString())
            ->whereNotNull('token_no')
            ->max('token_no');

        return ((int) ($last ?? 0)) + 1;
    }

    /**
     * Assign token only when patient reaches within configured minutes before slot start.
     */
    private function shouldAssignTokenNow(Carbon $appointmentDate, ?string $slot): bool
    {
        $beforeMinutes = max(0, (int) Helpers::getBeforeTime());
        $slotStart = $this->resolveSlotStartDateTime($appointmentDate, $slot);

        // Token starts from (slot start - beforeMinutes) onwards.
        return now()->greaterThanOrEqualTo($slotStart->copy()->subMinutes($beforeMinutes));
    }

    /**
     * Resolve slot start datetime from slot label like "09:40 AM - 10:00 AM".
     */
    private function resolveSlotStartDateTime(Carbon $appointmentDate, ?string $slot): Carbon
    {
        if (!empty($slot)) {
            $startPart = trim(explode('-', (string) $slot)[0] ?? '');

            if ($startPart !== '') {
                try {
                    return Carbon::createFromFormat('h:i A', $startPart)
                        ->setDate($appointmentDate->year, $appointmentDate->month, $appointmentDate->day);
                } catch (\Throwable $e) {
                    // Fallback to appointment datetime when slot format is unexpected.
                }
            }
        }

        return $appointmentDate->copy();
    }

    /**
     * Daily booking number: BK{hospital_id:04}{Ymd}{seq:05}
     * Used instead of token at registration; token assigned at check-in
     */
    private function generateDailyBookingNumber(Carbon $date): string
    {
        $prefix = 'BK' . str_pad((string) $this->hospital_id, 4, '0', STR_PAD_LEFT) . $date->format('Ymd');

        $last = OpdPatient::query()
            ->lockForUpdate()
            ->where('hospital_id', $this->hospital_id)
            ->whereDate('appointment_date', $date->toDateString())
            ->where('booking_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('booking_number');

        $next = 1;
        if ($last && str_starts_with($last, $prefix)) {
            $next = ((int) substr($last, -5)) + 1;
        }

        return $prefix . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    /**
     * IPD Admission number: IPD{hospital_id:04}{Ymd}{seq:04}
     */
    private function generateAdmissionNo(): string
    {
        $datePart = now()->format('Ymd');
        $prefix   = 'IPD' . str_pad((string) $this->hospital_id, 4, '0', STR_PAD_LEFT) . $datePart;

        $last = BedAllocation::query()
            ->lockForUpdate()
            ->where('hospital_id', $this->hospital_id)
            ->where('admission_no', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('admission_no');

        $next = 1;
        if ($last && str_starts_with((string) $last, $prefix)) {
            $next = ((int) substr((string) $last, -4)) + 1;
        }

        do {
            $no = $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
            $next++;
        } while (BedAllocation::query()->where('admission_no', $no)->exists());

        return $no;
    }

    /**
     * Normalizes payment mode to the old enum values (Cash / Card / Online).
     * The new UI uses more descriptive payment mode labels.
     */
    private function normalizePaymentMode(?string $mode): ?string
    {
        if (!$mode) {
            return null;
        }
        $mode = strtolower($mode);
        if (str_contains($mode, 'cash')) {
            return 'Cash';
        }
        if (str_contains($mode, 'card') || str_contains($mode, 'upi') || str_contains($mode, 'online') || str_contains($mode, 'neft')) {
            return 'Online';
        }
        if (str_contains($mode, 'pmjay') || str_contains($mode, 'ayushman') || str_contains($mode, 'cghs') || str_contains($mode, 'echs') || str_contains($mode, 'esi') || str_contains($mode, 'insurance') || str_contains($mode, 'scheme')) {
            return null; // These go through TPA, not direct payment
        }
        return null;
    }

    /**
     * Full allocation row for IPD actions (bed charge sync + episode billing).
     */
    private function findIpdAllocationForPatient360(int $allocationId): BedAllocation
    {
        return BedAllocation::query()
            ->where('hospital_id', $this->hospital_id)
            ->with([
                'patient',
                'bed.room.ward.floor',
                'bed.bedType.chargeMaster',
                'consultantDoctor',
                'department',
                'tpa',
                'sourceOpdPatient',
                'admittedBy',
                'dischargedBy',
                'charges',
            ])
            ->findOrFail($allocationId);
    }

    /**
     * Keeps bed charge line in sync (same rules as IpdPatientController::syncBedCharge).
     */
    private function syncIpdBedChargeForPatient360(BedAllocation $allocation, ChargeLedgerService $chargeLedger): BedAllocation
    {
        $allocation = $this->findIpdAllocationForPatient360($allocation->id);

        $bedType = $allocation->bed?->bedType;
        if (!$bedType) {
            return $allocation;
        }

        $admissionDate = Carbon::parse($allocation->admission_date);
        $endDate = $allocation->discharge_date ? Carbon::parse($allocation->discharge_date) : now();
        $quantity = max(1, $admissionDate->copy()->startOfDay()->diffInDays($endDate->copy()->startOfDay()) + 1);
        $chargeMaster = $bedType->chargeMaster;
        $billingFrequency = $chargeMaster?->billing_frequency ?? 'per_day';
        $calculationType = $chargeMaster?->calculation_type ?? 'fixed';
        $effectiveQuantity = $billingFrequency === 'one_time' ? 1 : $quantity;

        $chargeAttributes = [
            'hospital_id' => $allocation->hospital_id,
            'patient_id' => $allocation->patient_id,
            'visitable_type' => BedAllocation::class,
            'visitable_id' => $allocation->id,
            'source_type' => BedAllocation::class,
            'source_id' => $allocation->id,
            'module' => 'ipd',
            'particular' => 'Bed Charge - ' . ($allocation->bed?->getFullBedIdentifier() ?: 'IPD Bed'),
            'charge_master_id' => $chargeMaster?->id,
            'charge_category' => $chargeMaster?->category ?? 'bed_charge',
            'calculation_type' => $calculationType,
            'billing_frequency' => $billingFrequency,
            'quantity' => $effectiveQuantity,
            'payer_type' => $allocation->tpa_id ? 'tpa' : 'self',
            'tpa_id' => $allocation->tpa_id,
            'charged_at' => $allocation->admission_date,
        ];

        if (!$chargeMaster) {
            $baseAmount = (float) $bedType->base_charge * $effectiveQuantity;
            $chargeAttributes['unit_rate'] = (float) $bedType->base_charge;
            $chargeAttributes['amount'] = $baseAmount;
            $chargeAttributes['net_amount'] = $baseAmount;
        }

        $chargeLedger->upsertCharge($chargeAttributes);

        return $allocation->fresh([
            'patient',
            'bed.room.ward.floor',
            'bed.bedType.chargeMaster',
            'consultantDoctor',
            'department',
            'tpa',
            'sourceOpdPatient',
            'admittedBy',
            'dischargedBy',
            'charges',
        ]);
    }

    /**
     * @see \App\Http\Controllers\Hospital\IpdPatientController::resolveEpisodeAllocations
     */
    private function resolveIpdEpisodeAllocationsForPatient360(BedAllocation $allocation): Collection
    {
        $history = BedAllocation::query()
            ->where('hospital_id', $this->hospital_id)
            ->where('patient_id', $allocation->patient_id)
            ->orderByDesc('admission_date')
            ->orderByDesc('id')
            ->get();

        $index = $history->search(fn (BedAllocation $entry) => (int) $entry->id === (int) $allocation->id);
        if ($index === false) {
            return collect([$allocation]);
        }

        $episode = collect([$history[$index]]);
        for ($i = $index + 1; $i < $history->count(); $i++) {
            $candidate = $history[$i];
            if ((string) $candidate->discharge_status !== 'transferred') {
                break;
            }
            $episode->push($candidate);
        }

        return $episode->sortBy('admission_date')->values();
    }

    /**
     * @see \App\Http\Controllers\Hospital\IpdPatientController::episodeChargeQuery
     */
    private function ipdEpisodeChargeQueryForPatient360(BedAllocation $allocation, Collection $episodeAllocations)
    {
        $allocationIds = $episodeAllocations->pluck('id')->all();
        $diagnosticItemIds = DiagnosticOrderItem::query()
            ->whereHas('order', function ($query) use ($allocationIds) {
                $query->where('visitable_type', BedAllocation::class)
                    ->whereIn('visitable_id', $allocationIds);
            })
            ->pluck('id')
            ->all();

        return PatientCharge::query()
            ->where('patient_id', $allocation->patient_id)
            ->where(function ($query) use ($allocation, $allocationIds, $diagnosticItemIds) {
                $query->where(function ($bedQuery) use ($allocationIds) {
                    $bedQuery->where('visitable_type', BedAllocation::class)
                        ->whereIn('visitable_id', $allocationIds);
                });

                if ((int) $allocation->source_opd_patient_id > 0) {
                    $query->orWhere(function ($opdQuery) use ($allocation) {
                        $opdQuery->where('visitable_type', OpdPatient::class)
                            ->where('visitable_id', (int) $allocation->source_opd_patient_id);
                    });
                }

                if (!empty($diagnosticItemIds)) {
                    $query->orWhere(function ($diagQuery) use ($diagnosticItemIds) {
                        $diagQuery->where('source_type', DiagnosticOrderItem::class)
                            ->whereIn('source_id', $diagnosticItemIds);
                    });
                }
            });
    }
}
