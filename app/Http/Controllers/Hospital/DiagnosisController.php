<?php

namespace App\Http\Controllers\Hospital;

use App\CentralLogics\Helpers;
use App\Http\Controllers\BaseHospitalController;
use App\Models\Diagnosis;
use App\Models\OpdPatient;
use App\Models\Patient;
use App\Services\PatientTimelineService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class DiagnosisController extends BaseHospitalController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:view-diagnosis', ['only' => ['loaddata', 'showform']]);
        $this->middleware('permission:create-diagnosis|edit-diagnosis', ['only' => ['store']]);
        $this->middleware('permission:delete-diagnosis', ['only' => ['destroy']]);
    }

    public function loaddata(Request $request, Patient $patient)
    {
        $this->ensureAuthorizedPatient($patient);

        $data = Diagnosis::query()
            ->where('patient_id', $patient->id)
            ->orderByDesc('report_date')
            ->orderByDesc('id');

        return DataTables::of($data)
            ->editColumn('report_date', function ($row) {
                return $row->report_date ? Carbon::parse($row->report_date)->format('d-m-Y') : '-';
            })
            ->addColumn('description', function ($row) {
                $description = e($row->description ?? '-');

                if (!$row->report_file) {
                    return $description;
                }

                $fileUrl = asset('public/storage/' . ltrim($row->report_file, '/'));
                return $description . '<br><a href="' . $fileUrl . '" target="_blank">View Report</a>';
            })
            ->addColumn('actions', function ($row) use ($patient) {
                return view('hospital.opd-patient.diagnosis.partials.actions', compact('row', 'patient'))->render();
            })
            ->rawColumns(['description', 'actions'])
            ->make(true);
    }

    public function showform(Request $request, Patient $patient)
    {
        $this->ensureAuthorizedPatient($patient);

        $id = $request->id;
        $data = null;

        if ($id) {
            $data = Diagnosis::where('patient_id', $patient->id)->findOrFail($id);
        }

        return view('hospital.opd-patient.diagnosis.form', compact('data', 'id', 'patient'));
    }

    public function store(Request $request, Patient $patient, PatientTimelineService $timelineService)
    {
        $this->ensureAuthorizedPatient($patient);

        if ($request->filled('id') && !$request->user()->can('edit-diagnosis')) {
            abort(403, 'Unauthorized action.');
        }

        if (!$request->filled('id') && !$request->user()->can('create-diagnosis')) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'report_type' => 'required|string|max:255',
            'report_date' => 'required|date_format:d-m-Y',
            'report_file' => 'nullable|file|mimes:pdf,jpeg,png,jpg,gif,svg|max:10480',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $diagnosis = null;
        if ($request->filled('id')) {
            $diagnosis = Diagnosis::where('patient_id', $patient->id)->findOrFail($request->id);
        } else {
            $diagnosis = new Diagnosis();
            $diagnosis->hospital_id = $this->hospital_id;
            $diagnosis->patient_id = $patient->id;
        }

        if ($request->hasFile('report_file')) {
            if ($diagnosis->report_file) {
                Storage::disk('public')->delete($diagnosis->report_file);
            }

            $diagnosis->report_file = Storage::disk('public')->put('diagnosis_reports', $request->file('report_file'));
        }

        $diagnosis->report_type = $request->report_type;
        $diagnosis->report_date = Carbon::createFromFormat('d-m-Y', $request->report_date)->format('Y-m-d');
        $diagnosis->description = $request->description;
        $diagnosis->save();

        $latestOpdVisit = OpdPatient::query()
            ->where('patient_id', $patient->id)
            ->latest('appointment_date')
            ->latest('id')
            ->first();

        $timelinePayload = [
            'event_key' => 'patient.diagnosis.saved',
            'title' => $request->filled('id') ? 'Diagnosis Updated' : 'Diagnosis Added',
            'description' => 'Diagnosis type ' . $diagnosis->report_type . ' dated ' . ($diagnosis->report_date ? Carbon::parse($diagnosis->report_date)->format('d-m-Y') : '-') . ' has been saved.',
            'meta' => [
                'diagnosis_id' => $diagnosis->id,
                'report_type' => $diagnosis->report_type,
                'report_date' => (string) $diagnosis->report_date,
                'is_update' => $request->filled('id'),
            ],
        ];

        if ($latestOpdVisit) {
            $timelineService->logForOpdVisit($latestOpdVisit, $timelinePayload);
        } else {
            $timelineService->log($patient, $timelinePayload);
        }

        $message = $request->filled('id')
            ? 'Diagnosis updated successfully.'
            : 'Diagnosis created successfully.';

        return response()->json(['status' => true, 'message' => $message]);
    }

    public function destroy(Patient $patient, Diagnosis $diagnosis, PatientTimelineService $timelineService)
    {
        $this->ensureAuthorizedPatient($patient);

        if ((int) $diagnosis->patient_id !== (int) $patient->id) {
            abort(404);
        }

        $deletedDiagnosisId = $diagnosis->id;
        $deletedReportType = $diagnosis->report_type;

        if ($diagnosis->report_file) {
            Storage::disk('public')->delete($diagnosis->report_file);
        }

        $diagnosis->delete();

        $latestOpdVisit = OpdPatient::query()
            ->where('patient_id', $patient->id)
            ->latest('appointment_date')
            ->latest('id')
            ->first();

        $timelinePayload = [
            'event_key' => 'patient.diagnosis.deleted',
            'title' => 'Diagnosis Deleted',
            'description' => 'Diagnosis record ' . ($deletedReportType ?: '-') . ' has been deleted.',
            'meta' => [
                'diagnosis_id' => $deletedDiagnosisId,
                'report_type' => $deletedReportType,
            ],
        ];

        if ($latestOpdVisit) {
            $timelineService->logForOpdVisit($latestOpdVisit, $timelinePayload);
        } else {
            $timelineService->log($patient, $timelinePayload);
        }

        return response()->json(['status' => true, 'message' => 'Diagnosis deleted successfully.']);
    }

    private function ensureAuthorizedPatient(Patient $patient): void
    {
        if ((int) $patient->hospital_id !== (int) $this->hospital_id) {
            abort(403, 'Unauthorized patient record.');
        }
    }
}
