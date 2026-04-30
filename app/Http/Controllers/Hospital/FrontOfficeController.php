<?php

namespace App\Http\Controllers\Hospital;

use App\CentralLogics\Helpers;
use App\Http\Controllers\BaseHospitalController;
use App\Models\Appointment;
use App\Models\AppointmentPriority;
use App\Models\OpdPatient;
use App\Models\Patient;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class FrontOfficeController extends BaseHospitalController
{
    public array $routes = [];

    public function __construct()
    {
        parent::__construct();

        $this->middleware('permission:create-appointments', ['only' => ['store']]);
        $this->middleware('permission:edit-appointments', ['only' => ['updateStatus', 'moveToOpd']]);
        $this->middleware('permission:delete-appointments', ['only' => ['destroy']]);

        $this->routes = [
            'store' => route('hospital.front-office.store'),
            'loadtable' => route('hospital.front-office.load'),
            'showform' => route('hospital.front-office.showform'),
            'destroy' => route('hospital.front-office.destroy', ['appointment' => '__APPOINTMENT__']),
            'update-status' => route('hospital.front-office.update-status', ['appointment' => '__APPOINTMENT__']),
            'move-to-opd' => route('hospital.front-office.move-to-opd', ['appointment' => '__APPOINTMENT__']),
            'load-doctor-slots' => route('hospital.load-doctor-slots'),
        ];
    }

    public function index()
    {
        return view('hospital.front-office.index', [
            'pathurl' => 'front-office',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = Appointment::query()
            ->leftJoin('staff', 'staff.id', '=', 'appointments.doctor_id')
            ->where('appointments.hospital_id', $this->hospital_id)
            ->select(
                'appointments.*',
                DB::raw("TRIM(CONCAT(COALESCE(staff.first_name, ''), ' ', COALESCE(staff.last_name, ''))) as doctor_name")
            )
            ->when($request->filled('from_date'), function ($query) use ($request) {
                $query->whereDate('appointments.appointment_date', '>=', Carbon::parse($request->from_date)->toDateString());
            })
            ->when($request->filled('to_date'), function ($query) use ($request) {
                $query->whereDate('appointments.appointment_date', '<=', Carbon::parse($request->to_date)->toDateString());
            })
            ->orderByDesc('appointments.id');

        return DataTables::of($data)
            ->addColumn('doctor', function ($row) {
                return $row->doctor_name ?: '-';
            })
            ->addColumn('source', function ($row) {
                return $row->source ?: 'Panel';
            })
            ->addColumn('priority', function ($row) {
                return $row->priority ?: '-';
            })
            ->addColumn('appointment_date', function ($row) {
                $date = $row->appointment_date ? Carbon::parse($row->appointment_date)->format('d-m-Y') : '-';
                return trim($date . ' ' . ($row->appointment_slot ?: ''));
            })
            ->addColumn('live_consultation', function ($row) {
                return $row->live_consultation ?: 'No';
            })
            ->addColumn('status_badge', function ($row) {
                $displayStatus = $this->toDisplayStatus($row->status);
                $badgeClass = match ($displayStatus) {
                    'Approved' => 'success',
                    'Cancelled' => 'danger',
                    default => 'warning text-dark',
                };

                return '<span class="badge bg-' . $badgeClass . '">' . e($displayStatus) . '</span>';
            })
            ->addColumn('actions', function ($row) {
                return view('hospital.front-office.partials.actions', [
                    'row' => $row,
                    'status' => $this->toDisplayStatus($row->status),
                ])->render();
            })
            ->rawColumns(['status_badge', 'actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = null;

        if (!empty($id)) {
            $data = Appointment::query()
                ->where('hospital_id', $this->hospital_id)
                ->where('id', $id)
                ->first();
        }

        $doctors = Staff::query()
            ->doctor()
            ->active()
            ->get(['id', 'first_name', 'last_name'])
            ->map(function ($row) {
                $row->full_name = $row->full_name;
                return $row;
            });

        $priorities = AppointmentPriority::query()->pluck('name')->toArray();

        return view('hospital.front-office.form', compact('data', 'id', 'doctors', 'priorities'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'nullable|integer',
            'name' => 'required|string|max:255',
            'patient_phone' => 'required|digits_between:7,15',
            'patient_email' => 'nullable|email|max:255',
            'appointment_date' => 'required|date',
            'appointment_slot' => 'nullable|string|max:50',
            'priority' => 'nullable|string|max:100',
            'gender' => 'required|in:Male,Female,Other',
            'age' => 'nullable|integer|min:0|max:120',
            'notes' => 'nullable|string',
            'doctor_id' => 'nullable|integer|exists:staff,id',
            'live_consultation' => 'nullable|in:Yes,No',
            'status' => 'nullable|in:Pending,Approved,Cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        try {
            $appointment = null;
            if (!empty($request->id)) {
                $appointment = Appointment::query()
                    ->where('hospital_id', $this->hospital_id)
                    ->where('id', $request->id)
                    ->firstOrFail();
            }

            $storageStatus = $this->toStorageStatus($request->status ?: 'Pending');
            if ($appointment && !$this->canTransition($appointment->status, $storageStatus)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid status transition.',
                ], 200);
            }

            $appointmentDateTime = Carbon::parse($request->appointment_date);

            $payload = [
                'hospital_id' => $this->hospital_id,
                'name' => $request->name,
                'patient_phone' => $request->patient_phone,
                'patient_email' => $request->patient_email,
                'appointment_date' => $appointmentDateTime->toDateString(),
                'appointment_slot' => $request->appointment_slot,
                'priority' => $request->priority,
                'gender' => $request->gender,
                'age' => $request->age,
                'notes' => $request->notes,
                'doctor_id' => $request->doctor_id,
                'status' => $storageStatus,
                'source' => $request->source ?: 'Panel',
                'live_consultation' => $request->live_consultation ?: 'No',
            ];

            if ($appointment) {
                $appointment->update($payload);
            } else {
                $payload['appointment_id'] = $this->generateAppointmentId();
                Appointment::create($payload);
            }

            return response()->json([
                'status' => true,
                'message' => $request->id ? 'Appointment updated successfully.' : 'Appointment created successfully.',
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => 'Unable to save appointment right now.',
            ], 500);
        }
    }

    public function destroy(Appointment $appointment)
    {
        if ($appointment->hospital_id !== $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $appointment->delete();

        return response()->json(['status' => true, 'message' => 'Appointment deleted successfully.']);
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        if ($appointment->hospital_id !== $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:Approved,Cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $nextStatus = $this->toStorageStatus($request->status);

        if (!$this->canTransition($appointment->status, $nextStatus)) {
            return response()->json([
                'status' => false,
                'message' => 'This status change is not allowed.',
            ], 422);
        }

        $appointment->update(['status' => $nextStatus]);

        return response()->json([
            'status' => true,
            'message' => 'Appointment status updated successfully.',
        ]);
    }

    public function moveToOpd(Appointment $appointment)
    {
        if ($appointment->hospital_id !== $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }

        if ($appointment->status !== 'Confirmed') {
            return response()->json([
                'status' => false,
                'message' => 'Only approved appointments can be moved to OPD.',
            ], 422);
        }

        if (!empty($appointment->opd_patient_id)) {
            return response()->json([
                'status' => true,
                'message' => 'Appointment already moved to OPD.',
            ]);
        }

        $doctor = Staff::query()->doctor()->active()->where('id', $appointment->doctor_id)->first();
        if (!$doctor) {
            return response()->json([
                'status' => false,
                'message' => 'Please select an active doctor before moving to OPD.',
            ], 422);
        }

        if (empty($doctor->hr_department_id)) {
            return response()->json([
                'status' => false,
                'message' => 'Selected doctor has no department assigned.',
            ], 422);
        }

        DB::beginTransaction();

        try {
            $patient = Patient::query()
                ->where('hospital_id', $this->hospital_id)
                ->where('phone', $appointment->patient_phone)
                ->first();

            if (!$patient) {
                $patient = Patient::create([
                    'hospital_id' => $this->hospital_id,
                    'patient_id' => $this->generateHospitalWisePatientId(),
                    'name' => $appointment->name,
                    'phone' => $appointment->patient_phone,
                    'email' => $appointment->patient_email,
                    'gender' => $appointment->gender,
                    'age_years' => $appointment->age,
                    'country_code' => '+91',
                ]);
            }

            $appointmentDateTime = $this->parseAppointmentDateTime($appointment);

            $opdPatient = OpdPatient::create([
                'hospital_id' => $this->hospital_id,
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'hr_department_id' => $doctor->hr_department_id,
                'appointment_date' => $appointmentDateTime->format('Y-m-d H:i:s'),
                'case_no' => $this->generateDailyCaseNo($appointmentDateTime),
                'token_no' => $this->generateDailyTokenNo($appointmentDateTime),
                'casualty' => 'No',
                'mlc_patient' => 'No',
                'tpa_id' => null,
                'slot' => $appointment->appointment_slot,
                'standard_charge' => 0,
                'applied_charge' => 0,
                'payment_mode' => 'Cash',
                'live_consultation' => $appointment->live_consultation ?: 'No',
                'status' => 'waiting',
            ]);

            $appointment->update([
                'opd_patient_id' => $opdPatient->id,
                'moved_to_opd_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Appointment moved to OPD successfully.',
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Unable to move appointment to OPD right now.',
            ], 500);
        }
    }

    public function visitorBook()
    {
        $pathurl = 'visitor-book';
        return view('hospital.front-office.visitor-book', compact('pathurl'));
    }

    private function toStorageStatus(?string $status): string
    {
        return match ($status) {
            'Approved' => 'Confirmed',
            default => $status ?: 'Pending',
        };
    }

    private function toDisplayStatus(?string $status): string
    {
        return match ($status) {
            'Confirmed' => 'Approved',
            default => $status ?: 'Pending',
        };
    }

    private function canTransition(?string $currentStatus, string $nextStatus): bool
    {
        $current = $currentStatus ?: 'Pending';

        if ($current === $nextStatus) {
            return true;
        }

        if ($current === 'Pending') {
            return in_array($nextStatus, ['Confirmed', 'Cancelled'], true);
        }

        if ($current === 'Confirmed') {
            return $nextStatus === 'Cancelled';
        }

        return false;
    }

    private function generateAppointmentId(): string
    {
        $datePart = now()->format('Ymd');
        $prefix = 'APP' . str_pad((string) $this->hospital_id, 4, '0', STR_PAD_LEFT) . $datePart;

        $lastAppointment = Appointment::query()
            ->where('hospital_id', $this->hospital_id)
            ->where('appointment_id', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        $nextNumber = 1;
        if ($lastAppointment && str_starts_with($lastAppointment->appointment_id, $prefix)) {
            $nextNumber = ((int) substr($lastAppointment->appointment_id, -4)) + 1;
        }

        do {
            $appointmentId = $prefix . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
            $nextNumber++;
        } while (Appointment::query()->where('appointment_id', $appointmentId)->exists());

        return $appointmentId;
    }

    private function parseAppointmentDateTime(Appointment $appointment): Carbon
    {
        $date = Carbon::parse($appointment->appointment_date)->startOfDay()->setTime(9, 0);

        if (!empty($appointment->appointment_slot) && str_contains($appointment->appointment_slot, '-')) {
            $startText = trim(explode('-', $appointment->appointment_slot)[0]);
            try {
                $slotStart = Carbon::createFromFormat('h:i A', $startText);
                $date->setTime($slotStart->hour, $slotStart->minute);
            } catch (\Exception $exception) {
                // Keep fallback date/time.
            }
        }

        if ($date->isToday() && $date->lt(now())) {
            return now();
        }

        return $date;
    }

    private function generateHospitalWisePatientId(): string
    {
        $datePart = now()->format('Ymd');
        $prefix = 'P' . str_pad((string) $this->hospital_id, 4, '0', STR_PAD_LEFT) . $datePart;

        $lastPatient = Patient::query()
            ->where('hospital_id', $this->hospital_id)
            ->where('patient_id', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        $nextNumber = 1;
        if ($lastPatient && str_starts_with($lastPatient->patient_id, $prefix)) {
            $nextNumber = ((int) substr($lastPatient->patient_id, -4)) + 1;
        }

        do {
            $patientCode = $prefix . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
            $nextNumber++;
        } while (Patient::query()->where('patient_id', $patientCode)->exists());

        return $patientCode;
    }

    private function generateDailyCaseNo(Carbon $appointmentDate): string
    {
        $datePart = $appointmentDate->format('Ymd');
        $prefix = 'OPD' . $datePart;

        $lastCase = OpdPatient::query()
            ->where('hospital_id', $this->hospital_id)
            ->whereDate('appointment_date', $appointmentDate->toDateString())
            ->where('case_no', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        $nextNumber = 1;
        if ($lastCase && str_starts_with($lastCase->case_no, $prefix)) {
            $nextNumber = ((int) substr($lastCase->case_no, -4)) + 1;
        }

        do {
            $caseNo = $prefix . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
            $nextNumber++;
        } while (OpdPatient::query()->where('case_no', $caseNo)->exists());

        return $caseNo;
    }

    private function generateDailyTokenNo(Carbon $appointmentDate): int
    {
        $nextToken = 1;

        $lastToken = OpdPatient::query()
            ->where('hospital_id', $this->hospital_id)
            ->whereDate('appointment_date', $appointmentDate->toDateString())
            ->whereNotNull('token_no')
            ->orderByDesc('id')
            ->value('token_no');

        if (!is_null($lastToken)) {
            $nextToken = ((int) $lastToken) + 1;
        }

        while (
            OpdPatient::query()
                ->where('hospital_id', $this->hospital_id)
                ->whereDate('appointment_date', $appointmentDate->toDateString())
                ->where('token_no', $nextToken)
                ->exists()
        ) {
            $nextToken++;
        }

        return $nextToken;
    }
}
