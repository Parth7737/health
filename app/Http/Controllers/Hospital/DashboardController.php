<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Http\Controllers\Controller;
use App\Models\BedAllocation;
use App\Models\DiagnosticOrderItem;
use App\Models\IpdPrescription;
use App\Models\IpdProgressNote;
use App\Models\Notifications;
use App\Models\OpdPatient;
use App\Models\OpdPrescription;
use App\Models\PatientTimeline;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;


class DashboardController extends BaseHospitalController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if(Auth::user() && !Auth::user()->hasRole('Admininstrator')){
            return view('hospital.dashboard');
        }
        if(Auth::user()->hasRole('Admin') && auth()->user()->is_complete_registration == 0){
            return redirect(route('hospital.empanelmentRegistration.create'));
        }else{
            if(Auth::user()->hasRole('Doctor')){
                return redirect(route('hospital.doctor-dashboard'));
            }
            return view('hospital.dashboard');
        }
    }

    public function doctorDashboard()
    {
        $doctor = Staff::query()
            ->with(['department:id,name', 'specialist:id,name'])
            ->where('user_id', auth()->id())
            ->first();

        return view('hospital.doctor-dashboard', [
            'dashboardSnapshot' => $this->buildDoctorDashboardSnapshot($doctor),
        ]);
    }

    private function buildDoctorDashboardSnapshot(?Staff $doctor): array
    {
        $today = Carbon::today();
        $doctorStaffId = $doctor?->id;

        $opdTodayQuery = OpdPatient::query()
            ->with(['patient:id,name,age_years,gender', 'department:id,name'])
            ->where('hospital_id', $this->hospital_id)
            ->whereDate('appointment_date', $today)
            ->when($doctorStaffId, function ($query) use ($doctorStaffId) {
                $query->where('doctor_id', $doctorStaffId);
            });

        $totalOpdToday = (clone $opdTodayQuery)->count();
        $waitingCount = (clone $opdTodayQuery)->where('status', 'waiting')->count();
        $inConsultCount = (clone $opdTodayQuery)->where('status', 'in_room')->count();
        $completedCount = (clone $opdTodayQuery)->where('status', 'completed')->count();
        $overdueQueueCount = (clone $opdTodayQuery)
            ->where('status', 'waiting')
            ->where('appointment_date', '<=', now()->subMinutes(15))
            ->count();

        $activeIpdQuery = BedAllocation::query()
            ->with(['patient:id,name,age_years,gender', 'bed.room.ward:id,ward_name', 'department:id,name'])
            ->where('hospital_id', $this->hospital_id)
            ->whereNull('discharge_date')
            ->when($doctorStaffId, function ($query) use ($doctorStaffId) {
                $query->where('consultant_doctor_id', $doctorStaffId);
            });

        $activeIpdCount = (clone $activeIpdQuery)->count();
        $ipdPatients = (clone $activeIpdQuery)
            ->orderByDesc('admission_date')
            ->limit(6)
            ->get()
            ->map(function (BedAllocation $allocation) {
                $patient = $allocation->patient;
                $bedLabel = strtolower(trim($allocation->getWardIdentifier() . ' ' . $allocation->getRoomIdentifier()));
                $days = $allocation->admission_date ? $allocation->getLengthOfStay() + 1 : null;

                $status = 'stable';
                if (str_contains($bedLabel, 'icu') || str_contains($bedLabel, 'hdu')) {
                    $status = 'critical';
                } elseif (($days ?? 0) >= 4) {
                    $status = 'improving';
                }

                $statusColor = match ($status) {
                    'critical' => '#c62828',
                    'improving' => '#0288d1',
                    default => '#2e7d32',
                };

                $statusBadgeClass = match ($status) {
                    'critical' => 'badge-red',
                    'improving' => 'badge-blue',
                    default => 'badge-green',
                };

                return [
                    'name' => $patient?->name ?: '-',
                    'age_gender' => $this->formatAgeGender($patient?->age_years, $patient?->gender),
                    'bed' => trim($allocation->getWardIdentifier() . ' / ' . $allocation->getRoomIdentifier(), ' /'),
                    'department' => $allocation->department?->name ?: 'IPD Care',
                    'days' => $days,
                    'status' => $status,
                    'status_color' => $statusColor,
                    'status_badge_class' => $statusBadgeClass,
                    'profile_url' => route('hospital.ipd-patient.profile', ['allocation' => $allocation->id]),
                ];
            })
            ->values()
            ->all();

        $opdPrescriptions = OpdPrescription::query()
            ->with([
                'patient:id,name',
                'items.medicine:id,name',
                'items.dosage:id,dosage',
                'items.frequency:id,frequency',
                'saleBill:id,opd_prescription_id',
            ])
            ->where('hospital_id', $this->hospital_id)
            ->when($doctorStaffId, function ($query) use ($doctorStaffId) {
                $query->where('doctor_id', $doctorStaffId);
            })
            ->whereDate('created_at', $today)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function (OpdPrescription $prescription) {
                $firstItem = $prescription->items->first();
                $extraItems = max($prescription->items->count() - 1, 0);

                return [
                    'context' => 'OPD',
                    'patient' => $prescription->patient?->name ?: '-',
                    'drug' => $this->formatPrescriptionDrugLabel($firstItem?->medicine?->name, $extraItems),
                    'dose' => $firstItem?->dosage?->dosage ?: '-',
                    'frequency' => $firstItem?->frequency?->frequency ?: '-',
                    'days' => $firstItem?->no_of_day ?: '-',
                    'status' => $prescription->saleBill ? 'dispensed' : 'sent',
                    'time' => optional($prescription->created_at)->format('H:i'),
                    'action_url' => route('hospital.opd-patient.prescription.print', ['opdPatient' => $prescription->opd_patient_id]),
                    'action_label' => 'Print',
                ];
            });

        $ipdPrescriptions = IpdPrescription::query()
            ->with([
                'patient:id,name',
                'items.medicine:id,name',
                'items.dosage:id,dosage',
                'items.frequency:id,frequency',
                'saleBill:id,ipd_prescription_id',
            ])
            ->where('hospital_id', $this->hospital_id)
            ->when($doctorStaffId, function ($query) use ($doctorStaffId) {
                $query->where('doctor_id', $doctorStaffId);
            })
            ->whereDate('created_at', $today)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function (IpdPrescription $prescription) {
                $firstItem = $prescription->items->first();
                $extraItems = max($prescription->items->count() - 1, 0);

                return [
                    'context' => 'IPD',
                    'patient' => $prescription->patient?->name ?: '-',
                    'drug' => $this->formatPrescriptionDrugLabel($firstItem?->medicine?->name, $extraItems),
                    'dose' => $firstItem?->dosage?->dosage ?: '-',
                    'frequency' => $firstItem?->frequency?->frequency ?: '-',
                    'days' => $firstItem?->no_of_day ?: '-',
                    'status' => $prescription->saleBill ? 'dispensed' : 'sent',
                    'time' => optional($prescription->created_at)->format('H:i'),
                    'action_url' => route('hospital.ipd-patient.prescription.print', ['allocation' => $prescription->bed_allocation_id, 'prescription' => $prescription->id]),
                    'action_label' => 'Print',
                ];
            });

        $prescriptions = $opdPrescriptions
            ->concat($ipdPrescriptions)
            ->sortByDesc('time')
            ->take(8)
            ->values()
            ->all();

        $pathologyItems = DiagnosticOrderItem::query()
            ->with(['order.patient:id,name', 'pathologyStatus:id,name'])
            ->whereHas('order', function ($query) {
                $query->where('ordered_by', auth()->id())
                    ->where('order_type', 'pathology');
            })
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        $radiologyItems = DiagnosticOrderItem::query()
            ->with(['order.patient:id,name'])
            ->whereHas('order', function ($query) {
                $query->where('ordered_by', auth()->id())
                    ->where('order_type', 'radiology');
            })
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        $pendingDiagnosticsCount = $pathologyItems->whereNotIn('status', ['reported', 'completed'])->count()
            + $radiologyItems->whereNotIn('status', ['reported', 'completed'])->count();

        $clinicalNotes = PatientTimeline::query()
            ->with('patient:id,name')
            ->where('created_by', auth()->id())
            ->orderByDesc('logged_at')
            ->orderByDesc('id')
            ->limit(6)
            ->get()
            ->map(function (PatientTimeline $entry) {
                return [
                    'patient' => $entry->patient?->name ?: 'Patient timeline',
                    'title' => $entry->title ?: Str::headline(str_replace('.', ' ', (string) $entry->event_key)),
                    'note' => $entry->description ?: 'Clinical update recorded.',
                    'time' => optional($entry->logged_at)->diffForHumans(),
                    'author' => $entry->creator?->name ?: 'System',
                ];
            })
            ->values()
            ->all();

        $progressHighlights = IpdProgressNote::query()
            ->with(['patient:id,name', 'creator:id,name', 'allocation'])
            ->whereHas('allocation', function ($query) use ($doctorStaffId) {
                if ($doctorStaffId) {
                    $query->where('consultant_doctor_id', $doctorStaffId);
                }
                $query->whereNull('discharge_date');
            })
            ->orderByDesc('noted_at')
            ->limit(4)
            ->get()
            ->map(function (IpdProgressNote $note) {
                return [
                    'patient' => $note->patient?->name ?: 'IPD Patient',
                    'title' => strtoupper((string) $note->note_type) . ' note',
                    'note' => $note->note ?: 'Progress note updated.',
                    'time' => optional($note->noted_at)->diffForHumans(),
                    'author' => $note->creator?->name ?: 'Care team',
                ];
            });

        $clinicalFeed = collect($clinicalNotes)
            ->concat($progressHighlights)
            ->take(6)
            ->values()
            ->all();

        $unreadNotifications = 0;
        if (Schema::hasTable('notifications')) {
            $unreadNotifications = Notifications::query()
                ->where('hospital_id', $this->hospital_id)
                ->where('user_id', auth()->id())
                ->where('is_read', 0)
                ->count();
        }

        return [
            'doctor' => [
                'name' => $doctor?->full_name ?: auth()->user()->name,
                'department' => $doctor?->department?->name ?: 'Doctor Workspace',
                'speciality' => $doctor?->specialist?->name ?: 'Clinical Operations',
            ],
            'stats' => [
                'total_opd_today' => $totalOpdToday,
                'waiting_count' => $waitingCount,
                'in_consult_count' => $inConsultCount,
                'completed_count' => $completedCount,
                'active_ipd_count' => $activeIpdCount,
                'pending_diagnostics_count' => $pendingDiagnosticsCount,
                'rx_today_count' => count($prescriptions),
                'overdue_queue_count' => $overdueQueueCount,
                'unread_notifications_count' => $unreadNotifications,
            ],
            'ipdPatients' => $ipdPatients,
            'prescriptions' => $prescriptions,
            'labOrders' => $pathologyItems->map(function (DiagnosticOrderItem $item) {
                return [
                    'patient' => $item->order?->patient?->name ?: '-',
                    'test' => $item->test_name ?: '-',
                    'status' => $this->formatStatusLabel($item->status),
                    'result' => $item->pathologyStatus?->name ?: ($item->reported_at ? 'Reported' : 'Pending'),
                    'time' => optional($item->created_at)->format('H:i'),
                ];
            })->values()->all(),
            'radiologyOrders' => $radiologyItems->map(function (DiagnosticOrderItem $item) {
                return [
                    'patient' => $item->order?->patient?->name ?: '-',
                    'test' => $item->test_name ?: '-',
                    'modality' => strtoupper($item->department ?: 'RAD'),
                    'status' => $this->formatStatusLabel($item->status),
                    'time' => optional($item->created_at)->format('H:i'),
                ];
            })->values()->all(),
            'clinicalFeed' => $clinicalFeed,
            'workboard' => [
                [
                    'slot' => 'LIVE',
                    'type' => 'OPD',
                    'label' => $totalOpdToday > 0 ? $totalOpdToday . ' OPD visits assigned today' : 'No OPD visits assigned today',
                    'color' => '#1565c0',
                ],
                [
                    'slot' => 'NOW',
                    'type' => 'Queue',
                    'label' => $waitingCount > 0 ? $waitingCount . ' patients are waiting in queue' : 'Queue is clear right now',
                    'color' => '#00897b',
                ],
                [
                    'slot' => 'IPD',
                    'type' => 'Rounds',
                    'label' => $activeIpdCount > 0 ? $activeIpdCount . ' active IPD patients under your care' : 'No active IPD round assigned',
                    'color' => '#6d4c41',
                ],
                [
                    'slot' => 'LAB',
                    'type' => 'Review',
                    'label' => $pendingDiagnosticsCount > 0 ? $pendingDiagnosticsCount . ' diagnostics awaiting review' : 'Diagnostics review queue is clear',
                    'color' => '#ef6c00',
                ],
            ],
            'alerts' => $this->buildDoctorAlerts($waitingCount, $overdueQueueCount, $pathologyItems, $radiologyItems, $activeIpdCount, $unreadNotifications),
            'flowChart' => $this->buildFlowChartData($opdTodayQuery),
        ];
    }

    private function buildDoctorAlerts(int $waitingCount, int $overdueQueueCount, $pathologyItems, $radiologyItems, int $activeIpdCount, int $unreadNotifications): array
    {
        $alerts = collect();

        if ($overdueQueueCount > 0) {
            $alerts->push([
                'type' => 'critical',
                'icon' => 'OPD',
                'message' => $overdueQueueCount . ' OPD patients waiting more than 15 minutes.',
                'sub' => 'Queue attention required.',
                'time' => 'Live',
            ]);
        }

        if ($waitingCount > 0) {
            $alerts->push([
                'type' => 'warn',
                'icon' => 'Q',
                'message' => $waitingCount . ' patients currently in doctor queue.',
                'sub' => 'Use Call Next or see queue actions below.',
                'time' => 'Now',
            ]);
        }

        $pendingPathology = $pathologyItems->whereNotIn('status', ['reported', 'completed'])->count();
        if ($pendingPathology > 0) {
            $alerts->push([
                'type' => 'warn',
                'icon' => 'LAB',
                'message' => $pendingPathology . ' pathology orders are still pending.',
                'sub' => 'Review latest diagnostics in the orders tab.',
                'time' => 'Today',
            ]);
        }

        $pendingRadiology = $radiologyItems->whereNotIn('status', ['reported', 'completed'])->count();
        if ($pendingRadiology > 0) {
            $alerts->push([
                'type' => 'info',
                'icon' => 'RAD',
                'message' => $pendingRadiology . ' radiology orders are awaiting follow-up.',
                'sub' => 'Coordinate with radiology worklist if needed.',
                'time' => 'Today',
            ]);
        }

        if ($activeIpdCount > 0) {
            $alerts->push([
                'type' => 'info',
                'icon' => 'IPD',
                'message' => $activeIpdCount . ' active IPD patients under your supervision.',
                'sub' => 'Keep bedside notes and prescriptions updated.',
                'time' => 'Live',
            ]);
        }

        if ($unreadNotifications > 0) {
            $alerts->push([
                'type' => 'info',
                'icon' => 'MSG',
                'message' => $unreadNotifications . ' unread system notifications available.',
                'sub' => 'Please review pending operational updates.',
                'time' => 'Inbox',
            ]);
        }

        if ($alerts->isEmpty()) {
            $alerts->push([
                'type' => 'info',
                'icon' => 'OK',
                'message' => 'No high-priority clinical alerts at the moment.',
                'sub' => 'Queue, diagnostics and IPD care are currently stable.',
                'time' => 'Live',
            ]);
        }

        return $alerts->take(5)->values()->all();
    }

    private function buildFlowChartData($opdTodayQuery): array
    {
        $hours = collect(range(9, 16));
        $byHour = (clone $opdTodayQuery)
            ->get(['appointment_date'])
            ->filter(fn (OpdPatient $visit) => $visit->appointment_date !== null)
            ->groupBy(fn (OpdPatient $visit) => (int) $visit->appointment_date->format('G'));

        return [
            'labels' => $hours->map(fn (int $hour) => Carbon::createFromTime($hour)->format('ga'))->values()->all(),
            'data' => $hours->map(fn (int $hour) => $byHour->get($hour, collect())->count())->values()->all(),
        ];
    }

    private function formatAgeGender($age, ?string $gender): string
    {
        $parts = [];

        if ($age !== null && $age !== '') {
            $parts[] = (int) $age . 'Y';
        }

        if (!empty($gender)) {
            $parts[] = strtoupper((string) $gender);
        }

        return !empty($parts) ? implode(' / ', $parts) : '-';
    }

    private function formatPrescriptionDrugLabel(?string $medicineName, int $extraItems): string
    {
        $label = $medicineName ?: 'Prescription prepared';

        if ($extraItems > 0) {
            $label .= ' +' . $extraItems . ' more';
        }

        return $label;
    }

    private function formatStatusLabel(?string $status): string
    {
        return $status ? Str::headline(str_replace('_', ' ', $status)) : 'Pending';
    }
}
