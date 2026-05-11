<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\HrAttendanceRecord;
use App\Models\HrDepartment;
use App\Models\HrLeaveRequest;
use App\Models\HrLeaveType;
use App\Models\HrStaffLeaveBalance;
use App\Models\HrPayrollRecord;
use App\Models\HrRecruitmentVacancy;
use App\Models\HrTrainingProgram;
use App\Models\HeaderFooter;
use App\Models\Hospital;
use App\Models\Role;
use App\Models\Staff;
use App\Services\HrLeaveAttendanceSyncService;
use App\Services\HrLeaveBalanceService;
use App\Services\PayrollProcessingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class HrDashboardController extends BaseHospitalController
{
    private array $allowedTabs = [
        'dashboard',
        'directory',
        'attendance',
        'payroll',
        'leave',
        'recruitment',
        'training',
        'reports',
    ];

    public function index()
    {
        $stats = $this->buildStats();

        $routes = [
            'tab' => route('hospital.hr.dashboard.tab', ['tab' => '__TAB__']),
            'showModal' => route('hospital.hr.dashboard.show-modal'),
            'storeStaff' => route('hospital.hr.dashboard.store-staff'),
            'storeLeave' => route('hospital.hr.dashboard.store-leave'),
        ];

        return view('hospital.hr.dashboard.index', [
            'pathurl' => 'hr-dashboard',
            'routes' => $routes,
            'stats' => $stats,
            'canViewAttendance' => $this->canViewAttendance(),
            'departments' => HrDepartment::query()->select('id', 'name')->orderBy('name')->get(),
            'leaveTypes' => HrLeaveType::query()->select('id', 'name')->orderBy('name')->get(),
            'staffOptions' => Staff::query()->select('id', 'first_name', 'last_name', 'staff_id')->orderBy('first_name')->limit(300)->get(),
        ]);
    }

    public function tab(string $tab): JsonResponse
    {
        if (!in_array($tab, $this->allowedTabs, true)) {
            abort(404);
        }

        if ($tab === 'attendance' && !$this->canViewAttendance()) {
            return response()->json(['html' => '<div class="hrx-loading">You do not have permission to view attendance.</div>'], 403);
        }

        $view = match ($tab) {
            'dashboard' => view('hospital.hr.dashboard.tabs.dashboard', $this->dashboardData()),
            'directory' => view('hospital.hr.dashboard.tabs.directory', $this->directoryData()),
            'attendance' => view('hospital.hr.dashboard.tabs.attendance', $this->attendanceData()),
            'payroll' => view('hospital.hr.dashboard.tabs.payroll', $this->payrollData()),
            'leave' => view('hospital.hr.dashboard.tabs.leave', $this->leaveData()),
            'recruitment' => view('hospital.hr.dashboard.tabs.recruitment', $this->recruitmentData()),
            'training' => view('hospital.hr.dashboard.tabs.training', $this->trainingData()),
            'reports' => view('hospital.hr.dashboard.tabs.reports', $this->reportsData()),
            default => abort(404),
        };

        return response()->json(['html' => $view->render()]);
    }

    public function showModal(Request $request): JsonResponse
    {
        $type = $request->string('type')->toString();

        $staffId = (int) $request->input('staff_id', 0);
        $attendanceDateInput = trim((string) $request->input('attendance_date', ''));
        try {
            $attendanceDate = $attendanceDateInput !== '' ? Carbon::parse($attendanceDateInput)->toDateString() : now()->toDateString();
        } catch (\Throwable $e) {
            $attendanceDate = now()->toDateString();
        }

        $existingAttendance = null;
        if ($type === 'mark-attendance' && $staffId > 0 && Schema::hasTable('hr_attendance_records')) {
            $existingAttendance = HrAttendanceRecord::query()
                ->where('staff_id', $staffId)
                ->whereDate('attendance_date', $attendanceDate)
                ->first();

            $isEditMode = (bool) $existingAttendance;
            if (($isEditMode && !$this->canEditAttendance()) || (!$isEditMode && !$this->canCreateAttendance())) {
                return response()->json(['status' => false, 'message' => 'You do not have permission for this attendance action.'], 403);
            }
        }

        $html = match ($type) {
            'add-staff' => view('hospital.hr.dashboard.modals.add-staff', [
                'departments' => HrDepartment::query()->select('id', 'name')->orderBy('name')->get(),
            ])->render(),
            'mark-attendance' => view('hospital.hr.dashboard.modals.mark-attendance', [
                'staffOptions' => Staff::query()->select('id', 'first_name', 'last_name', 'staff_id')->where('status', 'Active')->orderBy('first_name')->limit(500)->get(),
                'selectedStaffId' => $staffId > 0 ? $staffId : null,
                'attendanceDate' => $attendanceDate,
                'existingRecord' => $existingAttendance,
            ])->render(),
            'leave-request' => view('hospital.hr.dashboard.modals.leave-request', [
                'staffOptions' => Staff::query()->select('id', 'first_name', 'last_name', 'staff_id')->orderBy('first_name')->limit(300)->get(),
                'leaveTypes' => HrLeaveType::query()->select('id', 'name')->orderBy('name')->get(),
            ])->render(),
            'leave-request-ajax' => view('hospital.hr.dashboard.modals.leave-request-ajax', [
                'staffOptions' => Staff::query()->select('id', 'first_name', 'last_name', 'staff_id')->orderBy('first_name')->limit(300)->get(),
                'leaveTypes' => HrLeaveType::query()->select('id', 'name')->orderBy('name')->get(),
            ])->render(),
            default => ''
        };

        if ($html === '') {
            return response()->json(['status' => false, 'message' => 'Invalid modal type.'], 422);
        }

        return response()->json(['status' => true, 'html' => $html]);
    }

    public function directoryLoad(Request $request): JsonResponse
    {
        $page = max((int) $request->input('page', 1), 1);
        $perPage = 12;

        $search = trim((string) $request->input('search', ''));
        $department = trim((string) $request->input('department', ''));
        $status = trim((string) $request->input('status', ''));

        $query = $this->buildDirectoryQuery($search, $department, $status);
        $total = (clone $query)->count();

        $staff = $query
            ->latest('id')
            ->forPage($page, $perPage)
            ->get();

        $hasMore = ($page * $perPage) < $total;
        $shownCount = min($page * $perPage, $total);

        return response()->json([
            'status' => true,
            'cards_html' => view('hospital.hr.dashboard.tabs.partials.directory-cards', [
                'staff' => $staff,
                'showEmpty' => false,
            ])->render(),
            'rows_html' => view('hospital.hr.dashboard.tabs.partials.directory-rows', [
                'staff' => $staff,
                'showEmpty' => false,
            ])->render(),
            'has_more' => $hasMore,
            'next_page' => $hasMore ? ($page + 1) : null,
            'shown_count' => $shownCount,
            'total_count' => $total,
        ]);
    }

    public function directoryListData(Request $request): JsonResponse
    {
        $draw = (int) $request->input('draw', 1);
        $start = max((int) $request->input('start', 0), 0);
        $length = max((int) $request->input('length', 10), 1);

        $search = trim((string) $request->input('search_filter', ''));
        $department = trim((string) $request->input('department_filter', ''));
        $status = trim((string) $request->input('status_filter', ''));

        $totalRecords = (clone $this->buildDirectoryQuery())->count();

        $query = $this->buildDirectoryQuery($search, $department, $status);
        $filteredRecords = (clone $query)->count();

        $orderColumnIndex = (int) $request->input('order.0.column', 0);
        $orderDirection = strtolower((string) $request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $columnMap = [
            0 => 'staff.staff_id',
            1 => 'staff.first_name',
            2 => 'hr_departments.name',
            3 => 'staff.email',
            4 => 'staff.phone',
            5 => 'staff.date_of_joining',
            6 => 'staff.status',
        ];

        if (array_key_exists($orderColumnIndex, $columnMap)) {
            if ($columnMap[$orderColumnIndex] === 'hr_departments.name') {
                $query->leftJoin('hr_departments', 'hr_departments.id', '=', 'staff.hr_department_id')
                    ->orderBy('hr_departments.name', $orderDirection)
                    ->select('staff.*');
            } else {
                $query->orderBy($columnMap[$orderColumnIndex], $orderDirection);
            }
        } else {
            $query->latest('staff.id');
        }

        $rows = $query
            ->skip($start)
            ->take($length)
            ->get();

        $canViewStaff = auth()->user()?->can('view-staff');
        $canEditStaff = auth()->user()?->can('edit-staff');
        $canDeleteStaff = auth()->user()?->can('delete-staff');

        $data = $rows->map(function ($row) use ($canViewStaff, $canEditStaff, $canDeleteStaff) {
            $fullName = trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? ''));
            $departmentName = $row->department->name ?? 'Unassigned';
            $designationName = $row->designation->name ?? 'Unassigned';
            $staffId = $row->staff_id ?: 'N/A';
            $statusLabel = $row->status ?: 'Unknown';
            $statusClass = $statusLabel === 'Active' ? 'green' : 'red';

            $actions = [];
            if ($canViewStaff) {
                $actions[] = '<button type="button" class="hrx-btn-lite hrx-staff-view" data-id="' . e($row->id) . '" title="View"><i class="fa fa-eye"></i></button>';
            }
            if ($canEditStaff) {
                $actions[] = '<button type="button" class="hrx-btn-lite hrx-staff-edit" data-id="' . e($row->id) . '" title="Edit"><i class="fa fa-pen"></i></button>';
            }
            if ($canDeleteStaff) {
                $actions[] = '<button type="button" class="hrx-btn-lite hrx-staff-delete" data-id="' . e($row->id) . '" title="Delete"><i class="fa fa-trash"></i></button>';
            }

            return [
                'emp_id' => e($staffId),
                'name' => '<div>' . e($fullName !== '' ? $fullName : 'Unnamed Staff') . '</div><small style="color:#8fa4ba">' . e($designationName . ' - ' . $departmentName) . '</small>',
                'department' => e($departmentName),
                'email' => e($row->email ?: '-'),
                'phone' => e($row->phone ?: '-'),
                'join_date' => e(optional($row->date_of_joining)->format('d M Y') ?: '-'),
                'status' => '<span class="hrx-badge ' . $statusClass . '">' . e($statusLabel) . '</span>',
                'action' => '<div class="hrx-actions" style="gap:6px;">' . implode('', $actions) . '</div>',
            ];
        })->values();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }

    public function leaveRequestsData(Request $request): JsonResponse
    {
        if (!Schema::hasTable('hr_leave_requests')) {
            return response()->json([
                'draw' => (int) $request->input('draw', 0),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
            ]);
        }

        $draw = (int) $request->input('draw', 1);
        $start = max((int) $request->input('start', 0), 0);
        $length = min(max((int) $request->input('length', 10), 1), 100);

        $searchCustom = trim((string) $request->input('search_custom', ''));
        if ($searchCustom === '' && is_array($request->input('search'))) {
            $searchCustom = trim((string) data_get($request->input('search'), 'value', ''));
        }
        $statusFilter = strtolower(trim((string) $request->input('status_filter', '')));

        $totalRecords = HrLeaveRequest::query()->where('hospital_id', $this->hospital_id)->count();

        $query = HrLeaveRequest::query()
            ->leftJoin('staff', 'staff.id', '=', 'hr_leave_requests.staff_id')
            ->leftJoin('hr_leave_types', 'hr_leave_types.id', '=', 'hr_leave_requests.hr_leave_type_id')
            ->where('hr_leave_requests.hospital_id', $this->hospital_id)
            ->select(['hr_leave_requests.*']);

        if ($searchCustom !== '') {
            $like = '%' . addcslashes($searchCustom, '%_\\') . '%';
            $query->where(function ($q) use ($like) {
                $q->where('hr_leave_requests.request_no', 'like', $like)
                    ->orWhere('staff.first_name', 'like', $like)
                    ->orWhere('staff.last_name', 'like', $like)
                    ->orWhere('staff.staff_id', 'like', $like)
                    ->orWhere('hr_leave_types.name', 'like', $like);
            });
        }

        if ($statusFilter === 'pending') {
            $query->where('hr_leave_requests.status', 'Pending');
        } elseif ($statusFilter === 'approved') {
            $query->where('hr_leave_requests.status', 'Approved');
        } elseif ($statusFilter === 'rejected') {
            $query->where('hr_leave_requests.status', 'Rejected');
        }

        $filteredRecords = (clone $query)->count();

        $orderColumnIndex = (int) $request->input('order.0.column', 0);
        $orderDir = strtolower((string) $request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $columnMap = [
            0 => 'hr_leave_requests.id',
            1 => 'hr_leave_requests.request_no',
            2 => 'staff.first_name',
            3 => 'hr_leave_types.name',
            4 => 'hr_leave_requests.from_date',
            5 => 'hr_leave_requests.to_date',
            6 => 'hr_leave_requests.total_days',
            7 => 'hr_leave_requests.reason',
            8 => 'hr_leave_requests.status',
        ];

        if (array_key_exists($orderColumnIndex, $columnMap)) {
            $query->orderBy($columnMap[$orderColumnIndex], $orderDir);
        } else {
            $query->orderBy('hr_leave_requests.id', 'desc');
        }

        $rows = (clone $query)->skip($start)->take($length)->get();
        $rows->load(['staff:id,first_name,last_name,staff_id', 'leaveType:id,name']);

        $data = $rows->map(function ($row) {
            $fullName = trim(($row->staff->first_name ?? '') . ' ' . ($row->staff->last_name ?? ''));
            $typeName = $row->leaveType->name ?? 'General';
            $statusLabel = $row->status ?: 'Unknown';
            $statusClass = $statusLabel === 'Approved' ? 'green' : ($statusLabel === 'Rejected' ? 'red' : 'orange');
            $reqAttr = htmlspecialchars((string) $row->request_no, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $noteAttr = htmlspecialchars((string) ($row->status_note ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $reasonAttr = htmlspecialchars((string) ($row->reason ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $typeAttr = htmlspecialchars((string) $typeName, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $nameAttr = htmlspecialchars($fullName !== '' ? $fullName : '—', ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $statusLower = strtolower($statusLabel);

            $staffId = (int) ($row->staff_id ?? 0);
            $balanceCell = $staffId > 0
                ? '<button type="button" class="hrx-btn-lite hrx-staff-leave-balance-open" data-staff-id="' . $staffId . '" data-staff-name="' . $nameAttr . '" title="Yearly leave balance"><i class="fa fa-bar-chart"></i></button>'
                : '<span class="text-muted" style="font-size:12px">—</span>';

            if ($statusLabel === 'Pending') {
                $actions = '<div class="hrx-actions">'
                    . '<button type="button" class="hrx-btn-lite hrx-leave-approve hrx-leave-approve-btn" data-request="' . $reqAttr . '" title="Approve"><i class="fa fa-check"></i></button>'
                    . '<button type="button" class="hrx-btn-lite hrx-leave-reject hrx-leave-reject-btn" data-request="' . $reqAttr . '" title="Reject"><i class="fa fa-times"></i></button>'
                    . '</div>';
            } elseif ($statusLabel === 'Approved') {
                $actions = '<div class="hrx-actions">'
                    . '<button type="button" class="hrx-btn-lite hrx-leave-view hrx-leave-view-btn" data-request="' . $reqAttr . '" data-status="' . htmlspecialchars($statusLower, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '" data-note="' . $noteAttr . '" data-reason="' . $reasonAttr . '" data-type="' . $typeAttr . '" data-display-name="' . $nameAttr . '" title="View"><i class="fa fa-eye"></i></button>'
                    . '<button type="button" class="hrx-btn-lite hrx-leave-withdraw hrx-leave-withdraw-btn" data-request="' . $reqAttr . '" title="Withdraw approval (removes linked attendance)"><i class="fa fa-undo"></i></button>'
                    . '</div>';
            } else {
                $actions = '<div class="hrx-actions">'
                    . '<button type="button" class="hrx-btn-lite hrx-leave-view hrx-leave-view-btn" data-request="' . $reqAttr . '" data-status="' . htmlspecialchars($statusLower, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '" data-note="' . $noteAttr . '" data-reason="' . $reasonAttr . '" data-type="' . $typeAttr . '" data-display-name="' . $nameAttr . '" title="View"><i class="fa fa-eye"></i></button>'
                    . '</div>';
            }

            return [
                'id' => (int) $row->id,
                'request_no' => '<span style="font-family:monospace;font-size:12px">' . e($row->request_no) . '</span>',
                'staff_name' => '<span style="font-weight:700">' . e($fullName !== '' ? $fullName : '—') . '</span>',
                'type_name' => '<span class="hrx-badge blue">' . e($typeName) . '</span>',
                'from_date' => '<span style="color:#5a7894;font-size:12px">' . e(optional($row->from_date)->format('d/m') ?: '—') . '</span>',
                'to_date' => '<span style="color:#5a7894;font-size:12px">' . e(optional($row->to_date)->format('d/m') ?: '—') . '</span>',
                'total_days' => '<span style="font-weight:700">' . e(number_format((float) $row->total_days, 1)) . '</span>',
                'reason' => '<span style="font-size:12px;color:#5a7894;max-width:160px;display:inline-block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="' . $reasonAttr . '">' . ($row->reason ? e($row->reason) : '—') . '</span>',
                'status' => '<span class="hrx-badge ' . $statusClass . '">' . e($statusLabel) . '</span>',
                'balance' => $balanceCell,
                'action' => $actions,
            ];
        })->values();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }

    public function staffLeaveBalance(Request $request): JsonResponse
    {
        $staffId = (int) $request->input('staff_id', 0);
        $year = (int) $request->input('year', now()->year);
        if ($staffId <= 0) {
            return response()->json(['status' => false, 'message' => 'Staff is required.'], 422);
        }

        $staff = Staff::query()
            ->where('id', $staffId)
            ->where('hospital_id', $this->hospital_id)
            ->first(['id', 'first_name', 'last_name', 'staff_id']);

        if (!$staff) {
            return response()->json(['status' => false, 'message' => 'Staff not found.'], 404);
        }

        $rows = collect();
        if (Schema::hasTable('hr_staff_leave_balances')) {
            $rows = HrStaffLeaveBalance::query()
                ->where('staff_id', $staffId)
                ->where('year', $year)
                ->with('leaveType:id,name')
                ->orderBy('hr_leave_type_id')
                ->get();
        }

        $html = view('hospital.hr.dashboard.tabs.partials.staff-leave-balance-body', [
            'rows' => $rows,
            'year' => $year,
            'staff' => $staff,
        ])->render();

        return response()->json([
            'status' => true,
            'html' => $html,
            'staff_name' => trim(($staff->first_name ?? '') . ' ' . ($staff->last_name ?? '')),
            'year' => $year,
        ]);
    }

    public function attendanceRegisterData(Request $request): JsonResponse
    {
        if (!$this->canViewAttendance()) {
            return response()->json(['status' => false, 'message' => 'You do not have permission to view attendance.'], 403);
        }

        $page = max((int) $request->input('page', 1), 1);
        $perPage = max(min((int) $request->input('per_page', 20), 5000), 10);
        $department = trim((string) $request->input('department', ''));
        $weekStartInput = trim((string) $request->input('week_start', ''));

        try {
            $weekStart = $weekStartInput !== ''
                ? Carbon::parse($weekStartInput)->startOfWeek(Carbon::MONDAY)
                : now()->startOfWeek(Carbon::MONDAY);
        } catch (\Throwable $e) {
            $weekStart = now()->startOfWeek(Carbon::MONDAY);
        }
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        $staffQuery = Staff::query()
            ->with(['department:id,name'])
            ->select('id', 'first_name', 'last_name', 'staff_id', 'hr_department_id', 'status')
            ->where('status', 'Active')
            ->where('hospital_id', (int) $this->hospital_id);

        if ($department !== '') {
            if (is_numeric($department)) {
                $staffQuery->where('hr_department_id', (int) $department);
            } else {
                $staffQuery->whereHas('department', function ($subQuery) use ($department) {
                    $subQuery->whereRaw('LOWER(name) = ?', [strtolower($department)]);
                });
            }
        }

        $total = (clone $staffQuery)->count();
        $lastPage = max((int) ceil(($total ?: 1) / $perPage), 1);
        $page = min($page, $lastPage);

        $staffRows = (clone $staffQuery)
            ->orderBy('first_name')
            ->forPage($page, $perPage)
            ->get();

        $days = [];
        for ($d = $weekStart->copy(); $d->lte($weekEnd); $d->addDay()) {
            $days[] = $d->copy();
        }

        $recordsByStaff = collect();
        if (Schema::hasTable('hr_attendance_records') && $staffRows->isNotEmpty()) {
            $recordsByStaff = HrAttendanceRecord::query()
                ->where('hospital_id', (int) $this->hospital_id)
                ->whereIn('staff_id', $staffRows->pluck('id'))
                ->whereBetween('attendance_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
                ->get()
                ->groupBy('staff_id');
        }

        $leaveCoverage = $this->approvedLeaveCoverageByStaff(
            (int) $this->hospital_id,
            $staffRows->pluck('id')->all(),
            $weekStart->copy()->startOfDay(),
            $weekEnd->copy()->startOfDay()
        );

        $rows = $staffRows->map(function ($staff) use ($days, $recordsByStaff, $leaveCoverage) {
            $name = trim(($staff->first_name ?? '') . ' ' . ($staff->last_name ?? ''));
            $departmentName = $staff->department->name ?? 'Unassigned';
            $staffRecs = $recordsByStaff[$staff->id] ?? collect();
            $recByDay = $staffRecs->keyBy(function ($rec) {
                return Carbon::parse($rec->attendance_date)->toDateString();
            });

            $cells = collect($days)->map(function ($day) use ($recByDay, $staff, $leaveCoverage) {
                $dayKey = $day->copy()->startOfDay()->toDateString();
                $rec = $recByDay[$dayKey] ?? null;
                $status = $rec?->status ?? 'Absent';
                $onApprovedLeave = !empty($leaveCoverage[(int) $staff->id][$dayKey]);

                $code = 'A';
                $title = '';
                if ($status == 'Present') {
                    $code = 'P';
                } elseif ($status == 'Holiday') {
                    $code = 'H';
                } elseif ($status == 'Leave' || ($onApprovedLeave && $status !== 'Present')) {
                    $code = 'L';
                    $title = 'On leave (approved)';
                }

                return [
                    'date' => $dayKey,
                    'code' => $code,
                    'class' => strtolower($code),
                    'title' => $title,
                ];
            })->values();

            return [
                'staff_id' => $staff->id,
                'name' => $name !== '' ? $name : 'Unknown',
                'department' => $departmentName,
                'cells' => $cells,
            ];
        })->values();

        return response()->json([
            'status' => true,
            'week_start' => $weekStart->toDateString(),
            'week_end' => $weekEnd->toDateString(),
            'week_label' => $weekStart->format('d M Y') . ' - ' . $weekEnd->format('d M Y'),
            'days' => collect($days)->map(function ($d) {
                return [
                    'date' => $d->toDateString(),
                    'label' => $d->format('M j'),
                ];
            })->values(),
            'rows' => $rows,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => $lastPage,
            ],
        ]);
    }

    public function storeStaff(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'email' => 'required|email|unique:staff,email',
            'phone' => 'nullable|string|max:20',
            'hr_department_id' => 'nullable|exists:hr_departments,id',
            'hr_designation' => 'nullable|string|max:120',
            'date_of_joining' => 'nullable|date',
            'status' => 'required|in:Active,Inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $staff = new Staff();
        $staff->hospital_id = $this->hospital_id;
        $staff->staff_id = $this->generateStaffCode();
        $staff->first_name = $request->string('first_name')->toString();
        $staff->last_name = $request->string('last_name')->toString();
        $staff->email = $request->string('email')->toString();
        $staff->phone = $this->sanitizePhone($request->input('phone'));
        $staff->hr_department_id = $request->input('hr_department_id');
        $staff->note = $request->filled('hr_designation') ? ('Designation: ' . $request->string('hr_designation')) : null;
        $staff->date_of_joining = $request->filled('date_of_joining') ? Carbon::parse($request->date_of_joining)->format('Y-m-d') : null;
        $staff->status = $request->string('status')->toString();
        $staff->save();

        return response()->json([
            'status' => true,
            'message' => 'Staff created successfully in HR directory.',
        ]);
    }

    public function attendanceDailyData(Request $request): JsonResponse
    {
        if (!$this->canViewAttendance()) {
            return response()->json(['status' => false, 'message' => 'You do not have permission to view attendance.'], 403);
        }

        $draw = (int) $request->input('draw', 1);
        $start = max((int) $request->input('start', 0), 0);
        $length = max((int) $request->input('length', 10), 1);
        $search = trim((string) data_get($request->input('search'), 'value', ''));
        $department = trim((string) $request->input('department', ''));
        $attendanceDateInput = trim((string) $request->input('attendance_date', ''));

        try {
            $attendanceDate = $attendanceDateInput !== ''
                ? Carbon::parse($attendanceDateInput)->toDateString()
                : now()->toDateString();
        } catch (\Throwable $e) {
            $attendanceDate = now()->toDateString();
        }

        if ($attendanceDate > now()->toDateString()) {
            $attendanceDate = now()->toDateString();
        }

        $query = Staff::query()
            ->with(['designation:id,name'])
            ->select('id', 'first_name', 'last_name', 'staff_id', 'hr_department_id', 'hr_designation_id', 'note', 'status')
            ->where('status', 'Active');

        if ($department !== '') {
            if (is_numeric($department)) {
                $query->where('hr_department_id', (int) $department);
            } else {
                $query->whereHas('department', function ($subQuery) use ($department) {
                    $subQuery->whereRaw('LOWER(name) = ?', [strtolower($department)]);
                });
            }
        }

        $totalRecords = (clone $query)->count();

        if ($search !== '') {
            $searchLike = '%' . $search . '%';
            $query->where(function ($subQuery) use ($searchLike) {
                $subQuery->whereRaw("CONCAT(COALESCE(first_name,''), ' ', COALESCE(last_name,'')) LIKE ?", [$searchLike])
                    ->orWhere('staff_id', 'like', $searchLike)
                    ->orWhereHas('designation', function ($designationQuery) use ($searchLike) {
                        $designationQuery->where('name', 'like', $searchLike);
                    })
                    ->orWhere('note', 'like', $searchLike);
            });
        }

        $filteredRecords = (clone $query)->count();

        $orderColumnIndex = (int) $request->input('order.0.column', 0);
        $orderDirection = strtolower((string) $request->input('order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $columnMap = [
            0 => 'first_name',
            1 => 'first_name',
            2 => 'first_name',
            3 => 'first_name',
            4 => 'first_name',
            5 => 'first_name',
            6 => 'first_name',
            7 => 'first_name',
        ];
        $query->orderBy($columnMap[$orderColumnIndex] ?? 'first_name', $orderDirection);

        $staffRows = $query
            ->skip($start)
            ->take($length)
            ->get();

        $recordsByStaff = collect();
        if (Schema::hasTable('hr_attendance_records') && $staffRows->isNotEmpty()) {
            $recordsByStaff = HrAttendanceRecord::query()
                ->whereIn('staff_id', $staffRows->pluck('id'))
                ->whereDate('attendance_date', $attendanceDate)
                ->get()
                ->keyBy('staff_id');
        }

        $dayCarbon = Carbon::parse($attendanceDate)->startOfDay();
        $leaveCoverage = $this->approvedLeaveCoverageByStaff(
            (int) $this->hospital_id,
            $staffRows->pluck('id')->all(),
            $dayCarbon,
            $dayCarbon->copy()
        );

        $canEditAttendance = $this->canEditAttendance();

        $data = $staffRows->map(function ($staff) use ($recordsByStaff, $attendanceDate, $canEditAttendance, $leaveCoverage) {
            $name = trim(($staff->first_name ?? '') . ' ' . ($staff->last_name ?? ''));
            $record = $recordsByStaff[$staff->id] ?? null;

            $designation = $this->resolveStaffDesignation($staff);
            $shift = $record?->shift_name ?? 'General';
            $inTime = $record && $record->in_time ? Carbon::parse($record->in_time)->format('H:i') : '—';
            $outTime = $record && $record->out_time ? Carbon::parse($record->out_time)->format('H:i') : '—';
            $statusLabel = $record ? $record->combined_status_label : 'Absent';
            $onApprovedLeave = !empty($leaveCoverage[(int) $staff->id][$attendanceDate]);
            if ($onApprovedLeave && (!$record || (string) $record->status === 'Absent')) {
                $statusLabel = 'OnLeave';
            }
            $statusClass = $this->attendanceStatusBadgeClass($statusLabel);

            $overtimeHours = (float) ($record?->overtime_hours ?? 0);
            $otDisplay = $overtimeHours > 0
                ? rtrim(rtrim((string) number_format($overtimeHours, 2, '.', ''), '0'), '.') . 'h'
                : '—';

            return [
                'name' => '<span class="fw-700">' . e($name !== '' ? $name : 'Unknown') . '</span>',
                'designation' => '<span class="text-sm text-muted">' . e($designation) . '</span>',
                'shift' => '<span class="badge badge-gray">' . e($shift) . '</span>',
                'in_time' => '<span class="fw-700">' . e($inTime) . '</span>',
                'out_time' => '<span class="fw-700">' . e($outTime) . '</span>',
                'status' => '<span class="badge ' . e($statusClass) . '">' . e($statusLabel) . '</span>',
                'ot_hrs' => '<span class="fw-700">' . e($otDisplay) . '</span>',
                'action' => $canEditAttendance
                    ? '<button type="button" class="btn btn-sm btn-outline hrx-daily-att-edit" data-staff-id="' . e((string) $staff->id) . '" data-attendance-date="' . e($attendanceDate) . '"><i class="fas fa-edit"></i></button>'
                    : '<span class="text-muted">-</span>',
            ];
        })->values();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }

    public function payrollListData(Request $request): JsonResponse
    {
        if (!$this->canViewPayroll()) {
            return response()->json(['status' => false, 'message' => 'You do not have permission to view payroll.'], 403);
        }

        if (!Schema::hasTable('hr_payroll_records')) {
            return response()->json([
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
            ]);
        }

        $draw = (int) $request->input('draw', 1);
        $start = max((int) $request->input('start', 0), 0);
        $length = max((int) $request->input('length', 10), 1);
        $search = trim((string) data_get($request->input('search'), 'value', ''));
        $status = strtolower(trim((string) $request->input('status', '')));
        $monthInput = trim((string) $request->input('month', now()->subMonth()->format('Y-m')));

        try {
            $payrollMonth = Carbon::createFromFormat('Y-m', $monthInput)->startOfMonth();
        } catch (\Throwable $e) {
            $payrollMonth = now()->subMonth()->startOfMonth();
        }

        $baseStaffQuery = Staff::query()
            ->where('hospital_id', $this->hospital_id)
            ->where('status', 'Active');

        $totalRecords = (clone $baseStaffQuery)->count();

        $staffQuery = Staff::query()
            ->with(['designation:id,name', 'department:id,name', 'user:id,email'])
            ->select('id', 'first_name', 'last_name', 'staff_id', 'hr_designation_id', 'hr_department_id', 'date_of_joining', 'email', 'user_id')
            ->where('hospital_id', $this->hospital_id)
            ->where('status', 'Active');

        if ($search !== '') {
            $searchLike = '%' . $search . '%';
            $staffQuery->where(function ($q) use ($searchLike) {
                $q->whereRaw("CONCAT(COALESCE(first_name,''), ' ', COALESCE(last_name,'')) LIKE ?", [$searchLike])
                    ->orWhere('staff_id', 'like', $searchLike)
                    ->orWhereHas('designation', function ($designationQuery) use ($searchLike) {
                        $designationQuery->where('name', 'like', $searchLike);
                    });
            });
        }

        if ($status === 'pending') {
            $staffQuery->where(function ($q) use ($payrollMonth) {
                $q->whereDoesntHave('payrollRecords', function ($payrollQuery) use ($payrollMonth) {
                    $payrollQuery->whereMonth('payroll_month', $payrollMonth->month)
                        ->whereYear('payroll_month', $payrollMonth->year);
                })->orWhereHas('payrollRecords', function ($payrollQuery) use ($payrollMonth) {
                    $payrollQuery->whereMonth('payroll_month', $payrollMonth->month)
                        ->whereYear('payroll_month', $payrollMonth->year)
                        ->where('status', 'Pending');
                });
            });
        } elseif (in_array($status, ['generated', 'paid'], true)) {
            $targetStatus = ucfirst($status);
            $staffQuery->whereHas('payrollRecords', function ($payrollQuery) use ($payrollMonth, $targetStatus) {
                $payrollQuery->whereMonth('payroll_month', $payrollMonth->month)
                    ->whereYear('payroll_month', $payrollMonth->year)
                    ->where('status', $targetStatus);
            });
        }

        $filteredRecords = (clone $staffQuery)->count();

        $staffRows = $staffQuery
            ->orderByDesc('id')
            ->skip($start)
            ->take($length)
            ->get();

        $recordsByStaff = HrPayrollRecord::query()
            ->whereMonth('payroll_month', $payrollMonth->month)
            ->whereYear('payroll_month', $payrollMonth->year)
            ->whereIn('staff_id', $staffRows->pluck('id'))
            ->get()
            ->keyBy('staff_id');

        $hospital = Hospital::query()->select('id', 'name', 'email', 'phone', 'address', 'city', 'pincode')->find($this->hospital_id);
        $hospitalAddress = trim(implode(', ', array_filter([
            $hospital?->address,
            $hospital?->city,
            $hospital?->pincode,
        ])));

        $data = $staffRows->map(function ($staff) use ($recordsByStaff, $hospital, $hospitalAddress, $payrollMonth) {
            $record = $recordsByStaff->get($staff->id);
            $fullName = trim(($staff->first_name ?? '') . ' ' . ($staff->last_name ?? ''));
            $designation = $staff->designation->name ?? 'N/A';
            $department = $staff->department->name ?? 'N/A';
            $staffCode = $staff->staff_id ?? '-';
            $joinDate = $staff->date_of_joining ? $staff->date_of_joining->format('d M Y') : 'N/A';

            $basicPay = (float) ($record->basic_pay ?? 0);
            $allowances = (float) ($record->allowances ?? 0);
            $deductions = (float) ($record->deductions ?? 0);
            $netPay = (float) ($record->net_pay ?? 0);
            $statusText = $record ? (string) $record->status : 'Pending';
            $statusClass = $statusText === 'Paid' ? 'green' : ($statusText === 'Generated' ? 'blue' : 'orange');

            $viewButton = '<button type="button" class="hrx-icon-btn hrx-payroll-slip" disabled title="Process payroll first"><i class="fas fa-file-invoice"></i></button>';
            $emailButton = '<button type="button" class="hrx-icon-btn primary hrx-payroll-send" disabled title="Payslip not generated"><i class="fas fa-paper-plane"></i></button>';
            $paidButton = '';

            if ($record) {
                $viewButton = '<button type="button" class="hrx-icon-btn hrx-payroll-slip"'
                    . ' data-record-id="' . e((string) $record->id) . '"'
                    . ' data-name="' . e($fullName) . '"'
                    . ' data-designation="' . e($designation) . '"'
                    . ' data-department="' . e($department) . '"'
                    . ' data-employee-id="' . e($staffCode) . '"'
                    . ' data-join-date="' . e($joinDate) . '"'
                    . ' data-basic="' . e(number_format($basicPay, 2, '.', '')) . '"'
                    . ' data-allowances="' . e(number_format($allowances, 2, '.', '')) . '"'
                    . ' data-deductions="' . e(number_format($deductions, 2, '.', '')) . '"'
                    . ' data-net-pay="' . e(number_format($netPay, 2, '.', '')) . '"'
                    . ' data-slip-no="' . e((string) ($record->slip_no ?? '')) . '"'
                    . ' data-payroll-month="' . e(optional($record->payroll_month)->format('F Y') ?: $payrollMonth->format('F Y')) . '"'
                    . ' data-hospital-name="' . e($hospital?->name ?? 'Hospital') . '"'
                    . ' data-hospital-email="' . e($hospital?->email ?? 'N/A') . '"'
                    . ' data-hospital-phone="' . e($hospital?->phone ?? 'N/A') . '"'
                    . ' data-hospital-address="' . e($hospitalAddress !== '' ? $hospitalAddress : 'N/A') . '"'
                    . ' title="View payslip"><i class="fas fa-file-invoice"></i></button>';

                $emailButton = '<button type="button" class="hrx-icon-btn primary hrx-payroll-send" data-record-id="' . e((string) $record->id) . '" data-name="' . e($fullName) . '" title="Send payslip"><i class="fas fa-paper-plane"></i></button>';

                if ($statusText === 'Generated') {
                    $paidButton = '<button type="button" class="hrx-icon-btn success hrx-payroll-mark-paid" data-record-id="' . e((string) $record->id) . '" title="Mark as Paid"><i class="fas fa-check"></i></button>';
                } elseif ($statusText === 'Paid') {
                    $paidButton = '<button type="button" class="hrx-icon-btn success" disabled title="Already paid"><i class="fas fa-check-double"></i></button>';
                }
            }

            return [
                'select' => $record
                    ? '<input type="checkbox" class="hrx-payroll-select" data-record-id="' . e((string) $record->id) . '">'
                    : '<input type="checkbox" disabled title="Payroll not generated">',
                'emp_id' => '<span class="hrx-emp-code">' . e($staffCode) . '</span>',
                'slip_no' => '<span class="hrx-emp-code">' . e((string) ($record?->slip_no ?? '-')) . '</span>',
                'name' => '<span class="hrx-name">' . e($fullName !== '' ? $fullName : 'Unknown') . '</span>',
                'designation' => '<span class="hrx-desig">' . e($designation) . '</span>',
                'basic' => 'INR ' . number_format($basicPay, 2),
                'allowances' => '<span class="hrx-allow">INR ' . number_format($allowances, 2) . '</span>',
                'deductions' => '<span class="hrx-deduct">INR ' . number_format($deductions, 2) . '</span>',
                'net_pay' => '<span class="hrx-net">INR ' . number_format($netPay, 2) . '</span>',
                'status' => '<span class="hrx-badge ' . e($statusClass) . '">' . e($statusText) . '</span>',
                'action' => '<div class="hrx-row-actions">' . $viewButton . $emailButton . $paidButton . '</div>',
            ];
        })->values();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }

    public function processPayroll(Request $request, PayrollProcessingService $payrollProcessingService): JsonResponse
    {
        if (!$this->canViewPayroll()) {
            return response()->json(['status' => false, 'message' => 'You do not have permission to process payroll.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'month' => 'required|date_format:Y-m',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Invalid payroll month.'], 422);
        }

        $month = Carbon::createFromFormat('Y-m', (string) $request->input('month'))->startOfMonth();

        if ($month->greaterThanOrEqualTo(Carbon::now()->startOfMonth())) {
            return response()->json(['status' => false, 'message' => 'Payroll cannot be processed for a future month. Please select a previous month.'], 422);
        }

        $alreadyProcessed = HrPayrollRecord::query()
            ->whereMonth('payroll_month', $month->month)
            ->whereYear('payroll_month', $month->year)
            ->exists();

        if ($alreadyProcessed) {
            return response()->json([
                'status' => false,
                'message' => 'Payroll for this month has already been processed. Reprocessing is not allowed.',
            ], 422);
        }

        $result = $payrollProcessingService->processMonth($this->hospital_id, $month);

        return response()->json([
            'status' => true,
            'message' => 'Payroll processed for ' . $result['month_label'] . '. Created: ' . $result['created'] . ', Updated: ' . $result['updated'] . '.',
            'meta' => $result,
        ]);
    }

    public function payrollPayslipCard(Request $request): JsonResponse
    {
        if (!$this->canViewPayroll()) {
            return response()->json(['status' => false, 'message' => 'You do not have permission to view payroll.'], 403);
        }

        if (!Schema::hasTable('hr_payroll_records')) {
            return response()->json(['status' => false, 'message' => 'Payroll data table not found.'], 422);
        }

        $recordId = (int) $request->input('payroll_record_id', 0);
        if ($recordId <= 0) {
            return response()->json(['status' => false, 'message' => 'Invalid payroll record selected.'], 422);
        }

        $record = HrPayrollRecord::query()
            ->with(['staff:id,first_name,last_name,staff_id,hr_designation_id,hr_department_id,date_of_joining', 'staff.designation:id,name', 'staff.department:id,name', 'items'])
            ->where('id', $recordId)
            ->first();

        if (!$record) {
            return response()->json(['status' => false, 'message' => 'Payroll record not found.'], 404);
        }

        $hospital = Hospital::query()->select('id', 'name', 'email', 'phone', 'address', 'city', 'pincode')->find($this->hospital_id);

        $fullName = trim(($record->staff->first_name ?? '') . ' ' . ($record->staff->last_name ?? ''));
        $designation = $record->staff->designation->name ?? 'N/A';
        $department = $record->staff->department->name ?? 'N/A';
        $staffCode = $record->staff->staff_id ?? '-';
        $joinDate = $record->staff->date_of_joining ? $record->staff->date_of_joining->format('d M Y') : 'N/A';
        $hospitalAddress = trim(implode(', ', array_filter([
            $hospital?->address,
            $hospital?->city,
            $hospital?->pincode,
        ])));

        $hospitalName = $hospital?->name ?? 'Hospital';
        $hospitalLogoUrl = $hospital?->image ? asset('public/storage/' . $hospital->image) : null;
        $hospitalInitials = collect(preg_split('/\s+/', trim($hospitalName)) ?: [])
            ->filter()
            ->take(2)
            ->map(function ($part) {
                return strtoupper(substr($part, 0, 1));
            })
            ->implode('');

        $html = view('hospital.hr.dashboard.tabs.partials.payroll-payslip-card', [
            'slipMonthLabel' => optional($record->payroll_month)->format('F Y') ?: now()->format('F Y'),
            'hospitalName' => $hospitalName,
            'hospitalPhone' => $hospital?->phone ?? 'N/A',
            'hospitalEmail' => $hospital?->email ?? 'N/A',
            'hospitalAddress' => $hospitalAddress !== '' ? $hospitalAddress : 'N/A',
            'hospitalLogoUrl' => $hospitalLogoUrl,
            'hospitalInitials' => $hospitalInitials !== '' ? $hospitalInitials : 'H',
            'slipName' => $fullName !== '' ? $fullName : 'Unknown',
            'slipDesig' => $designation,
            'slipDepartment' => $department,
            'slipEmployeeId' => $staffCode,
            'slipJoinDate' => $joinDate,
            'slipBasic' => (float) $record->basic_pay,
            'slipAllowances' => (float) $record->allowances,
            'slipDeductions' => (float) $record->deductions,
            'slipNet' => (float) $record->net_pay,
            'allowanceItems' => $record->items->where('item_type', 'Allowance')->values(),
            'deductionItems' => $record->items->where('item_type', 'Deduction')->values(),
        ])->render();

        return response()->json([
            'status' => true,
            'html' => $html,
        ]);
    }

    public function payrollPayslipPdf(HrPayrollRecord $record)
    {
        if (!$this->canViewPayroll()) {
            abort(403, 'You do not have permission to view payroll.');
        }

        if ((int) $record->hospital_id !== (int) $this->hospital_id) {
            abort(403);
        }

        $pdfDocument = $this->buildPayrollPdfDocument($record);

        if (!$pdfDocument['pdf']) {
            return response($pdfDocument['html'], 200, ['Content-Type' => 'text/html; charset=UTF-8']);
        }

        return response($pdfDocument['pdf'], 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $pdfDocument['filename'] . '"',
        ]);
    }

    public function markPayrollPaid(HrPayrollRecord $record): JsonResponse
    {
        if (!$this->canViewPayroll()) {
            return response()->json(['status' => false, 'message' => 'You do not have permission to update payroll status.'], 403);
        }

        if ((int) $record->hospital_id !== (int) $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Invalid payroll record.'], 403);
        }

        if ((string) $record->status === 'Pending') {
            return response()->json(['status' => false, 'message' => 'A pending payroll record cannot be marked as paid directly. Please process payroll first.'], 422);
        }

        if ((string) $record->status !== 'Paid') {
            $record->status = 'Paid';
            $record->paid_at = now();
            $record->save();
        }

        return response()->json(['status' => true, 'message' => 'Payroll marked as Paid successfully.']);
    }

    public function payrollExportCsv(Request $request)
    {
        if (!$this->canViewPayroll()) {
            abort(403, 'You do not have permission to export payroll.');
        }

        $monthInput = trim((string) $request->input('month', now()->subMonth()->format('Y-m')));
        try {
            $payrollMonth = Carbon::createFromFormat('Y-m', $monthInput)->startOfMonth();
        } catch (\Throwable $e) {
            $payrollMonth = now()->subMonth()->startOfMonth();
        }

        $rows = $this->buildPayrollRowsForMonth($payrollMonth);
        $filename = 'Payroll-' . $payrollMonth->format('Y-m') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Slip No', 'Month', 'Emp ID', 'Name', 'Department', 'Designation', 'Status', 'Basic', 'Allowances', 'Deductions', 'Net Pay', 'Email']);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row['slip_no'],
                    $row['month'],
                    $row['staff_code'],
                    $row['name'],
                    $row['department'],
                    $row['designation'],
                    $row['status'],
                    number_format((float) $row['basic_pay'], 2, '.', ''),
                    number_format((float) $row['allowances'], 2, '.', ''),
                    number_format((float) $row['deductions'], 2, '.', ''),
                    number_format((float) $row['net_pay'], 2, '.', ''),
                    $row['email'],
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function sendPayrollSlip(Request $request): JsonResponse
    {
        if (!$this->canViewPayroll()) {
            return response()->json(['status' => false, 'message' => 'You do not have permission to send payslip.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'payroll_record_id' => 'required|integer|exists:hr_payroll_records,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Invalid payroll record selected.'], 422);
        }

        $record = HrPayrollRecord::query()
            ->with(['staff:id,first_name,last_name,email,user_id', 'staff.user:id,email'])
            ->find((int) $request->input('payroll_record_id'));

        if (!$record || (int) $record->hospital_id !== (int) $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Payroll record not found.'], 404);
        }

        $recipient = $this->resolvePayrollRecipientEmail($record->staff);
        if (!$recipient) {
            return response()->json(['status' => false, 'message' => 'Staff email not found.'], 422);
        }

        $pdfDocument = $this->buildPayrollPdfDocument($record);
        $staffName = trim((string) ($record->staff->first_name ?? '') . ' ' . (string) ($record->staff->last_name ?? ''));

        Mail::send([], [], function ($message) use ($recipient, $staffName, $record, $pdfDocument) {
            $monthLabel = optional($record->payroll_month)->format('F Y') ?: now()->format('F Y');
            $message->to($recipient)
                ->subject('Salary Slip - ' . $monthLabel)
                ->html('<p>Dear ' . e($staffName !== '' ? $staffName : 'Team Member') . ',</p><p>Please find attached your salary slip for ' . e($monthLabel) . '.</p><p>Regards,<br>HR Payroll Team</p>');

            if ($pdfDocument['pdf']) {
                $message->attachData($pdfDocument['pdf'], $pdfDocument['filename'], ['mime' => 'application/pdf']);
            }
        });

        return response()->json(['status' => true, 'message' => 'Payslip emailed successfully.']);
    }

    public function sendPayrollBulk(Request $request): JsonResponse
    {
        if (!$this->canViewPayroll()) {
            return response()->json(['status' => false, 'message' => 'You do not have permission to send bulk payslips.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'month' => 'required|date_format:Y-m',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Invalid month selected.'], 422);
        }

        $payrollMonth = Carbon::createFromFormat('Y-m', (string) $request->input('month'))->startOfMonth();

        $records = HrPayrollRecord::query()
            ->with(['staff:id,first_name,last_name,email,user_id', 'staff.user:id,email'])
            ->whereMonth('payroll_month', $payrollMonth->month)
            ->whereYear('payroll_month', $payrollMonth->year)
            ->whereIn('status', ['Generated', 'Paid'])
            ->get();

        $sent = 0;
        $skipped = 0;

        foreach ($records as $record) {
            $recipient = $this->resolvePayrollRecipientEmail($record->staff);
            if (!$recipient) {
                $skipped++;
                continue;
            }

            $pdfDocument = $this->buildPayrollPdfDocument($record);
            $staffName = trim((string) ($record->staff->first_name ?? '') . ' ' . (string) ($record->staff->last_name ?? ''));

            Mail::send([], [], function ($message) use ($recipient, $staffName, $record, $pdfDocument) {
                $monthLabel = optional($record->payroll_month)->format('F Y') ?: now()->format('F Y');
                $message->to($recipient)
                    ->subject('Salary Slip - ' . $monthLabel)
                    ->html('<p>Dear ' . e($staffName !== '' ? $staffName : 'Team Member') . ',</p><p>Please find attached your salary slip for ' . e($monthLabel) . '.</p><p>Regards,<br>HR Payroll Team</p>');

                if ($pdfDocument['pdf']) {
                    $message->attachData($pdfDocument['pdf'], $pdfDocument['filename'], ['mime' => 'application/pdf']);
                }
            });

            $sent++;
        }

        return response()->json([
            'status' => true,
            'message' => 'Bulk payslip email completed. Sent: ' . $sent . ', Skipped: ' . $skipped . '.',
            'meta' => ['sent' => $sent, 'skipped' => $skipped],
        ]);
    }

    public function sendPayrollSelected(Request $request): JsonResponse
    {
        if (!$this->canViewPayroll()) {
            return response()->json(['status' => false, 'message' => 'You do not have permission to send selected payslips.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'record_ids' => 'required|array|min:1',
            'record_ids.*' => 'integer|distinct|exists:hr_payroll_records,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Please select valid payroll rows.'], 422);
        }

        $recordIds = array_values(array_unique(array_map('intval', (array) $request->input('record_ids', []))));

        $records = HrPayrollRecord::query()
            ->with(['staff:id,first_name,last_name,email,user_id', 'staff.user:id,email'])
            ->whereIn('id', $recordIds)
            ->whereIn('status', ['Generated', 'Paid'])
            ->get();

        $sent = 0;
        $skipped = 0;

        foreach ($records as $record) {
            if ((int) $record->hospital_id !== (int) $this->hospital_id) {
                $skipped++;
                continue;
            }

            $recipient = $this->resolvePayrollRecipientEmail($record->staff);
            if (!$recipient) {
                $skipped++;
                continue;
            }

            $pdfDocument = $this->buildPayrollPdfDocument($record);
            $staffName = trim((string) ($record->staff->first_name ?? '') . ' ' . (string) ($record->staff->last_name ?? ''));

            Mail::send([], [], function ($message) use ($recipient, $staffName, $record, $pdfDocument) {
                $monthLabel = optional($record->payroll_month)->format('F Y') ?: now()->format('F Y');
                $message->to($recipient)
                    ->subject('Salary Slip - ' . $monthLabel)
                    ->html('<p>Dear ' . e($staffName !== '' ? $staffName : 'Team Member') . ',</p><p>Please find attached your salary slip for ' . e($monthLabel) . '.</p><p>Regards,<br>HR Payroll Team</p>');

                if ($pdfDocument['pdf']) {
                    $message->attachData($pdfDocument['pdf'], $pdfDocument['filename'], ['mime' => 'application/pdf']);
                }
            });

            $sent++;
        }

        return response()->json([
            'status' => true,
            'message' => 'Selected payslip email completed. Sent: ' . $sent . ', Skipped: ' . $skipped . '.',
            'meta' => ['sent' => $sent, 'skipped' => $skipped],
        ]);
    }

    public function markPayrollPaidBulk(Request $request): JsonResponse
    {
        if (!$this->canViewPayroll()) {
            return response()->json(['status' => false, 'message' => 'You do not have permission to update payroll status.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'record_ids' => 'required|array|min:1',
            'record_ids.*' => 'integer|distinct|exists:hr_payroll_records,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Please select valid payroll rows.'], 422);
        }

        $recordIds = array_values(array_unique(array_map('intval', (array) $request->input('record_ids', []))));

        $records = HrPayrollRecord::query()
            ->whereIn('id', $recordIds)
            ->get();

        $updated = 0;
        $skipped = 0;

        foreach ($records as $record) {
            if ((int) $record->hospital_id !== (int) $this->hospital_id) {
                $skipped++;
                continue;
            }

            if ((string) $record->status === 'Generated') {
                $record->status = 'Paid';
                $record->paid_at = now();
                $record->save();
                $updated++;
            } else {
                $skipped++;
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Selected records update completed. Paid marked: ' . $updated . ', Skipped: ' . $skipped . '.',
            'meta' => ['updated' => $updated, 'skipped' => $skipped],
        ]);
    }

    private function buildPayrollRowsForMonth(Carbon $payrollMonth)
    {
        $staffRows = Staff::query()
            ->with(['designation:id,name', 'department:id,name', 'user:id,email'])
            ->select('id', 'first_name', 'last_name', 'staff_id', 'hr_designation_id', 'hr_department_id', 'email', 'user_id')
            ->where('hospital_id', $this->hospital_id)
            ->where('status', 'Active')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $recordsByStaff = HrPayrollRecord::query()
            ->whereMonth('payroll_month', $payrollMonth->month)
            ->whereYear('payroll_month', $payrollMonth->year)
            ->whereIn('staff_id', $staffRows->pluck('id'))
            ->get()
            ->keyBy('staff_id');

        return $staffRows->map(function ($staff) use ($recordsByStaff, $payrollMonth) {
            $record = $recordsByStaff->get($staff->id);
            $fullName = trim((string) ($staff->first_name ?? '') . ' ' . (string) ($staff->last_name ?? ''));
            $email = $this->resolvePayrollRecipientEmail($staff) ?? '';

            return [
                'slip_no' => $record?->slip_no ?? '',
                'month' => $payrollMonth->format('Y-m'),
                'staff_code' => $staff->staff_id ?? '-',
                'name' => $fullName !== '' ? $fullName : 'Unknown',
                'department' => $staff->department->name ?? 'N/A',
                'designation' => $staff->designation->name ?? 'N/A',
                'status' => $record ? (string) $record->status : 'Pending',
                'basic_pay' => (float) ($record->basic_pay ?? 0),
                'allowances' => (float) ($record->allowances ?? 0),
                'deductions' => (float) ($record->deductions ?? 0),
                'net_pay' => (float) ($record->net_pay ?? 0),
                'email' => $email,
            ];
        });
    }

    private function resolvePayrollRecipientEmail(?Staff $staff): ?string
    {
        if (!$staff) {
            return null;
        }

        $email = trim((string) ($staff->email ?? ''));
        if ($email !== '') {
            return $email;
        }

        $userEmail = trim((string) ($staff->user->email ?? ''));
        return $userEmail !== '' ? $userEmail : null;
    }

    private function buildPayrollPdfDocument(HrPayrollRecord $record): array
    {
        $record->load([
            'staff:id,first_name,last_name,staff_id,hr_designation_id,hr_department_id,date_of_joining',
            'staff.designation:id,name',
            'staff.department:id,name',
            'items',
        ]);

        $hospital = Hospital::query()->select('id', 'name', 'email', 'phone', 'address', 'city', 'pincode', 'image')->find($this->hospital_id);
        $printTemplate = HeaderFooter::query()->where('type', 'radiology')->first();

        $fullName = trim(($record->staff->first_name ?? '') . ' ' . ($record->staff->last_name ?? ''));
        $designation = $record->staff->designation->name ?? 'N/A';
        $department = $record->staff->department->name ?? 'N/A';
        $staffCode = $record->staff->staff_id ?? '-';
        $joinDate = $record->staff->date_of_joining ? $record->staff->date_of_joining->format('d M Y') : 'N/A';

        $html = view('hospital.hr.dashboard.tabs.pdf.payroll-payslip', [
            'record' => $record,
            'hospital' => $hospital,
            'printTemplate' => $printTemplate,
            'slipMonthLabel' => optional($record->payroll_month)->format('F Y') ?: now()->format('F Y'),
            'slipName' => $fullName !== '' ? $fullName : 'Unknown',
            'slipDesig' => $designation,
            'slipDepartment' => $department,
            'slipEmployeeId' => $staffCode,
            'slipJoinDate' => $joinDate,
            'slipBasic' => (float) $record->basic_pay,
            'slipAllowances' => (float) $record->allowances,
            'slipDeductions' => (float) $record->deductions,
            'slipNet' => (float) $record->net_pay,
            'allowanceItems' => $record->items->where('item_type', 'Allowance')->values(),
            'deductionItems' => $record->items->where('item_type', 'Deduction')->values(),
        ])->render();

        $filename = 'Payroll-Slip-' . preg_replace('/[^A-Za-z0-9_-]+/', '_', (string) ($record->slip_no ?: ($staffCode ?: $record->id))) . '.pdf';

        if (!class_exists(\Dompdf\Dompdf::class)) {
            return ['html' => $html, 'pdf' => null, 'filename' => $filename];
        }

        try {
            $options = new \Dompdf\Options();
            $options->set('isRemoteEnabled', false);
            $options->set('defaultFont', 'DejaVu Sans');
            $chroot = realpath(public_path());
            if ($chroot) {
                $options->setChroot($chroot);
            }

            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            return ['html' => $html, 'pdf' => $dompdf->output(), 'filename' => $filename];
        } catch (\Throwable $e) {
            return ['html' => $html, 'pdf' => null, 'filename' => $filename];
        }
    }

    public function storeAttendance(Request $request): JsonResponse
    {
        $attendanceDate = Carbon::parse((string) $request->input('attendance_date'))->toDateString();
        $existingRecord = HrAttendanceRecord::query()
            ->where('hospital_id', $this->hospital_id)
            ->where('staff_id', (int) $request->input('staff_id'))
            ->whereDate('attendance_date', $attendanceDate)
            ->first();

        if ($existingRecord && !$this->canEditAttendance()) {
            return response()->json(['status' => false, 'message' => 'You do not have permission to edit attendance.'], 403);
        }

        if (!$existingRecord && !$this->canCreateAttendance()) {
            return response()->json(['status' => false, 'message' => 'You do not have permission to add attendance.'], 403);
        }

        if (!Schema::hasTable('hr_attendance_records')) {
            return response()->json([
                'status' => false,
                'message' => 'hr_attendance_records table not found. Run migrations first.',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'staff_id' => 'required|exists:staff,id',
            'attendance_date' => 'required|date|before_or_equal:today',
            'in_time' => 'nullable|date_format:H:i',
            'out_time' => 'nullable|date_format:H:i',
            'status' => 'required|in:Present,Absent,Leave,Holiday',
            'day_type' => 'nullable|in:Full Day,Half Day',
            'late_count' => 'nullable|integer|min:0|max:255',
            'is_miss_punch' => 'nullable|boolean',
            'is_overtime' => 'nullable|boolean',
            'overtime_hours' => 'nullable|numeric|min:0|max:24',
            'notes' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $status = $request->string('status')->toString();
        $lateCount = (int) $request->input('late_count', 0);
        $isMissPunch = $request->boolean('is_miss_punch');
        $isOvertime = $request->boolean('is_overtime');
        $overtimeHours = (float) $request->input('overtime_hours', 0);

        if (!$isOvertime) {
            $overtimeHours = 0;
        }

        $combinedStatus = $this->buildAttendanceCombinedStatus(
            $status,
            (string) $request->input('day_type', ''),
            $lateCount,
            $isMissPunch,
            $isOvertime,
            $overtimeHours
        );

        $payload = [
            'in_time' => $request->filled('in_time') ? $request->string('in_time')->toString() : null,
            'out_time' => $request->filled('out_time') ? $request->string('out_time')->toString() : null,
            'status' => $status,
            'late_count' => $lateCount,
            'day_type' => $request->filled('day_type') ? $request->string('day_type')->toString() : null,
            'is_miss_punch' => $isMissPunch,
            'is_overtime' => $isOvertime,
            'combined_status' => $combinedStatus,
            'overtime_hours' => $overtimeHours,
            'notes' => $request->filled('notes') ? $request->string('notes')->toString() : null,
        ];

        $oldAuditData = null;
        if ($existingRecord) {
            $oldAuditData = [
                'status' => (string) ($existingRecord->status ?? ''),
                'day_type' => (string) ($existingRecord->day_type ?? ''),
                'late_count' => (int) ($existingRecord->late_count ?? 0),
                'is_miss_punch' => (bool) ($existingRecord->is_miss_punch ?? false),
                'is_overtime' => (bool) ($existingRecord->is_overtime ?? false),
                'overtime_hours' => (float) ($existingRecord->overtime_hours ?? 0),
                'in_time' => $existingRecord->in_time ? Carbon::parse($existingRecord->in_time)->format('H:i') : null,
                'out_time' => $existingRecord->out_time ? Carbon::parse($existingRecord->out_time)->format('H:i') : null,
                'notes' => (string) ($existingRecord->notes ?? ''),
                'combined_status' => (string) ($existingRecord->combined_status ?? ''),
            ];
        }

        $attendanceRecord = HrAttendanceRecord::updateOrCreate(
            [
                'hospital_id' => $this->hospital_id,
                'staff_id' => (int) $request->input('staff_id'),
                'attendance_date' => $attendanceDate,
            ],
            $payload
        );

        if ($existingRecord && Schema::hasTable('hr_attendance_edit_logs')) {
            $newAuditData = [
                'status' => (string) ($payload['status'] ?? ''),
                'day_type' => (string) ($payload['day_type'] ?? ''),
                'late_count' => (int) ($payload['late_count'] ?? 0),
                'is_miss_punch' => (bool) ($payload['is_miss_punch'] ?? false),
                'is_overtime' => (bool) ($payload['is_overtime'] ?? false),
                'overtime_hours' => (float) ($payload['overtime_hours'] ?? 0),
                'in_time' => $payload['in_time'] ? (string) $payload['in_time'] : null,
                'out_time' => $payload['out_time'] ? (string) $payload['out_time'] : null,
                'notes' => (string) ($payload['notes'] ?? ''),
                'combined_status' => (string) ($payload['combined_status'] ?? ''),
            ];

            $audit = $this->buildAttendanceChangeSummary($oldAuditData ?: [], $newAuditData);

            if (!empty($audit['changed_fields'])) {
                DB::table('hr_attendance_edit_logs')->insert([
                    'hospital_id' => $this->hospital_id,
                    'attendance_record_id' => $attendanceRecord->id,
                    'staff_id' => (int) $request->input('staff_id'),
                    'attendance_date' => $attendanceDate,
                    'edited_by_user_id' => auth()->id(),
                    'change_summary' => $audit['summary'],
                    'old_data' => json_encode($oldAuditData, JSON_UNESCAPED_UNICODE),
                    'new_data' => json_encode($newAuditData, JSON_UNESCAPED_UNICODE),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Attendance saved successfully.',
        ]);
    }

    public function storeLeave(Request $request): JsonResponse
    {
        if (!Schema::hasTable('hr_leave_requests')) {
            return response()->json([
                'status' => false,
                'message' => 'hr_leave_requests table not found. Run migrations first.',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'staff_id' => 'required|exists:staff,id',
            'hr_leave_type_id' => 'nullable|exists:hr_leave_types,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'reason' => 'required|string|max:500',
            'contact_during_leave' => 'nullable|string|max:20',
            'covering_staff' => 'nullable|string|max:150',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $fromDate = Carbon::parse($request->from_date)->startOfDay();
        $toDate = Carbon::parse($request->to_date)->startOfDay();

        $leave = new HrLeaveRequest();
        $leave->hospital_id = $this->hospital_id;
        $leave->staff_id = (int) $request->staff_id;
        $leave->request_no = $this->generateLeaveRequestNo();
        $leave->hr_leave_type_id = $request->input('hr_leave_type_id');
        $leave->from_date = $fromDate->format('Y-m-d');
        $leave->to_date = $toDate->format('Y-m-d');
        $leave->total_days = (float) $fromDate->diffInDays($toDate) + 1;
        $leave->reason = $request->string('reason')->toString();
        $leave->contact_during_leave = $request->filled('contact_during_leave') ? $request->string('contact_during_leave')->toString() : null;
        $leave->covering_staff = $request->filled('covering_staff') ? $request->string('covering_staff')->toString() : null;
        $leave->status = 'Pending';
        $leave->save();

        return response()->json([
            'status' => true,
            'message' => 'Leave request submitted successfully.',
        ]);
    }

    public function updateLeaveStatus(Request $request): JsonResponse
    {
        if (!Schema::hasTable('hr_leave_requests')) {
            return response()->json([
                'status' => false,
                'message' => 'hr_leave_requests table not found. Run migrations first.',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'request_no' => 'required|string|exists:hr_leave_requests,request_no',
            'status' => 'required|in:Approved,Rejected',
            'note' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $leave = HrLeaveRequest::query()->where('request_no', $request->string('request_no')->toString())->first();
        if (!$leave) {
            return response()->json(['status' => false, 'message' => 'Leave request not found.'], 404);
        }

        $newStatus = $request->string('status')->toString();
        $wasApproved = $leave->status === 'Approved';
        $canUpdate = $leave->status === 'Pending'
            || ($wasApproved && $newStatus === 'Rejected');

        if (!$canUpdate) {
            return response()->json(['status' => false, 'message' => 'Only pending requests can be approved or rejected, or approved leave can be withdrawn.'], 422);
        }

        $leave->status = $newStatus;
        $leave->status_note = $request->string('note')->toString();
        $leave->approved_at = now();
        $leave->approved_by = auth()->id();
        $leave->save();

        if (Schema::hasTable('hr_staff_leave_balances')) {
            app(HrLeaveBalanceService::class)->syncUsedDaysForStaff((int) $leave->staff_id, (int) $leave->hospital_id);
        }

        $attendanceSync = app(HrLeaveAttendanceSyncService::class);
        if ($leave->status === 'Approved') {
            $attendanceSync->syncApprovedLeaveToAttendance($leave);
        } elseif ($leave->status === 'Rejected') {
            $attendanceSync->removeLinkedLeaveAttendance($leave);
        }

        $message = 'Leave request ' . $leave->request_no . ' ' . strtolower($leave->status) . ' successfully.';
        if ($wasApproved && $leave->status === 'Rejected') {
            $message = 'Approved leave withdrawn for ' . $leave->request_no . '. Linked attendance rows were removed.';
        }

        return response()->json([
            'status' => true,
            'message' => $message,
        ]);
    }

    private function buildStats(): array
    {
        $totalStaff = Staff::query()->count();

        $presentToday = 0;
        if (Schema::hasTable('hr_attendance_records')) {
            $presentToday = HrAttendanceRecord::query()
                ->whereDate('attendance_date', now()->toDateString())
                ->where('status', 'Present')
                ->count();
        }

        $onLeave = 0;
        if (Schema::hasTable('hr_leave_requests')) {
            $today = now()->toDateString();
            $onLeave = HrLeaveRequest::query()
                ->where('status', 'Approved')
                ->whereDate('from_date', '<=', $today)
                ->whereDate('to_date', '>=', $today)
                ->count();
        }

        $monthlyPayroll = 0;
        if (Schema::hasTable('hr_payroll_records')) {
            $monthlyPayroll = HrPayrollRecord::query()
                ->whereMonth('payroll_month', now()->month)
                ->whereYear('payroll_month', now()->year)
                ->sum('net_pay');
        }

        $vacancies = 0;
        if (Schema::hasTable('hr_recruitment_vacancies')) {
            $vacancies = HrRecruitmentVacancy::query()->sum('required_positions');
        }

        return [
            'totalStaff' => $totalStaff,
            'presentToday' => $presentToday,
            'onLeave' => $onLeave,
            'monthlyPayroll' => $monthlyPayroll,
            'vacancies' => $vacancies,
        ];
    }

    private function dashboardData(): array
    {
        $roleNames = Role::hospitalRole()
            ->orderBy('name')
            ->pluck('name')
            ->push('Unassigned')
            ->unique()
            ->values();

        $departmentSummary = HrDepartment::query()
            ->withCount('staff')
            ->orderByDesc('staff_count')
            ->limit(8)
            ->get();

        $today = now()->toDateString();

        $presentByDepartment = collect();
        $leaveByDepartment = collect();

        if (Schema::hasTable('hr_attendance_records')) {
            $presentByDepartment = HrAttendanceRecord::query()
                ->join('staff', 'staff.id', '=', 'hr_attendance_records.staff_id')
                ->selectRaw('staff.hr_department_id as department_id')
                ->selectRaw("SUM(CASE WHEN hr_attendance_records.status = 'Present' THEN 1 ELSE 0 END) as present_count")
                ->selectRaw("SUM(CASE WHEN hr_attendance_records.status = 'Leave' THEN 1 ELSE 0 END) as leave_count")
                ->whereDate('hr_attendance_records.attendance_date', $today)
                ->groupBy('staff.hr_department_id')
                ->get()
                ->keyBy('department_id');
        }

        if (Schema::hasTable('hr_leave_requests')) {
            $leaveByDepartment = HrLeaveRequest::query()
                ->join('staff', 'staff.id', '=', 'hr_leave_requests.staff_id')
                ->selectRaw('staff.hr_department_id as department_id')
                ->selectRaw('COUNT(hr_leave_requests.id) as active_leave_count')
                ->where('hr_leave_requests.status', 'Approved')
                ->whereDate('hr_leave_requests.from_date', '<=', $today)
                ->whereDate('hr_leave_requests.to_date', '>=', $today)
                ->groupBy('staff.hr_department_id')
                ->get()
                ->keyBy('department_id');
        }

        $departmentAttendance = $departmentSummary
            ->map(function ($department) use ($presentByDepartment, $leaveByDepartment) {
                $deptId = $department->id;
                $total = (int) $department->staff_count;
                $present = (int) ($presentByDepartment->get($deptId)->present_count ?? 0);

                $leaveFromAttendance = (int) ($presentByDepartment->get($deptId)->leave_count ?? 0);
                $leaveFromRequest = (int) ($leaveByDepartment->get($deptId)->active_leave_count ?? 0);
                $onLeave = max($leaveFromAttendance, $leaveFromRequest);

                $absent = max($total - $present - $onLeave, 0);
                $rate = $total > 0 ? round(($present / $total) * 100, 1) : 0;

                return [
                    'department' => $department->name,
                    'total' => $total,
                    'present' => $present,
                    'absent' => $absent,
                    'on_leave' => $onLeave,
                    'rate' => $rate,
                ];
            })
            ->values();

        $categoryMix = Staff::query()
            ->leftJoin('roles', 'roles.id', '=', 'staff.role_id')
            ->selectRaw("COALESCE(roles.name, 'Unassigned') as role_name")
            ->selectRaw('COUNT(staff.id) as total')
            ->groupBy('role_name')
            ->orderByDesc('total')
            ->get();

        $normalizedCategoryMix = $roleNames->map(function ($roleName) use ($categoryMix) {
            $match = $categoryMix->firstWhere('role_name', $roleName);

            return [
                'role_name' => $roleName,
                'total' => (int) ($match->total ?? 0),
            ];
        })->values();

        $categoryByDepartment = Staff::query()
            ->leftJoin('roles', 'roles.id', '=', 'staff.role_id')
            ->leftJoin('hr_departments', 'hr_departments.id', '=', 'staff.hr_department_id')
            ->selectRaw("COALESCE(hr_departments.name, 'Unassigned') as department_name")
            ->selectRaw("COALESCE(roles.name, 'Unassigned') as role_name")
            ->selectRaw('COUNT(staff.id) as total')
            ->groupBy('department_name', 'role_name')
            ->orderBy('department_name')
            ->orderByDesc('total')
            ->get()
            ->groupBy('department_name')
            ->map(function ($rows) use ($roleNames) {
                return $roleNames->map(function ($roleName) use ($rows) {
                    $match = $rows->firstWhere('role_name', $roleName);

                    return [
                        'role_name' => $roleName,
                        'total' => (int) ($match->total ?? 0),
                    ];
                })->values();
            });

        return [
            'departmentSummary' => $departmentAttendance,
            'attendanceSummary' => $departmentAttendance,
            'categoryMix' => $normalizedCategoryMix,
            'categoryByDepartment' => $categoryByDepartment,
            'alerts' => $this->buildAlerts(),
        ];
    }

    private function directoryData(): array
    {
        $perPage = 12;
        $query = $this->buildDirectoryQuery();
        $total = (clone $query)->count();

        $staff = $query
            ->latest('id')
            ->limit($perPage)
            ->get();

        $shownCount = min($perPage, $total);

        return [
            'staff' => $staff,
            'departments' => HrDepartment::query()->select('id', 'name')->orderBy('name')->get(),
            'hasMore' => $total > $perPage,
            'nextPage' => $total > $perPage ? 2 : null,
            'shownCount' => $shownCount,
            'totalCount' => $total,
        ];
    }

    private function buildDirectoryQuery(string $search = '', string $department = '', string $status = '')
    {
        $query = Staff::query()
            ->with(['department:id,name', 'designation:id,name'])
            ->select([
                'id',
                'staff_id',
                'first_name',
                'last_name',
                'email',
                'phone',
                'hr_department_id',
                'hr_designation_id',
                'status',
                'date_of_joining',
            ]);

        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('staff_id', 'like', '%' . $search . '%');
            });
        }

        if ($department !== '') {
            $query->whereHas('department', function ($subQuery) use ($department) {
                $subQuery->whereRaw('LOWER(name) = ?', [strtolower($department)]);
            });
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        return $query;
    }



    private function attendanceData(): array
    {
        return [
            'departments' => HrDepartment::query()->select('id', 'name')->orderBy('name')->get(),
            'weekStart' => now()->startOfWeek(Carbon::MONDAY)->toDateString(),
            'canViewAttendance' => $this->canViewAttendance(),
            'canCreateAttendance' => $this->canCreateAttendance(),
            'canEditAttendance' => $this->canEditAttendance(),
        ];
    }

    private function payrollData(): array
    {
        $hospital = Hospital::query()->select('id', 'name', 'email', 'phone', 'address', 'city', 'pincode')->find($this->hospital_id);

        $lastMonth = Carbon::now()->subMonth();

        if (!Schema::hasTable('hr_payroll_records')) {
            return [
                'payrollRows' => collect(),
                'payrollMonthLabel' => $lastMonth->format('F Y'),
                'payrollDefaultMonth' => $lastMonth->format('Y-m'),
                'payrollHospital' => $hospital,
            ];
        }

        $payrollRows = HrPayrollRecord::query()
            ->with(['staff:id,first_name,last_name,staff_id,hr_designation_id,hr_department_id,date_of_joining', 'staff.designation:id,name', 'staff.department:id,name'])
            ->whereMonth('payroll_month', $lastMonth->month)
            ->whereYear('payroll_month', $lastMonth->year)
            ->latest('id')
            ->limit(100)
            ->get();

        return [
            'payrollRows' => $payrollRows,
            'payrollMonthLabel' => $lastMonth->format('F Y'),
            'payrollDefaultMonth' => $lastMonth->format('Y-m'),
            'canViewPayroll' => $this->canViewPayroll(),
            'payrollHospital' => $hospital,
        ];
    }

    private function leaveData(): array
    {
        if (!Schema::hasTable('hr_leave_requests')) {
            return [
                'leaveByStatus' => collect(),
                'leaveCalendar' => collect(),
                'leaveBalance' => collect(),
            ];
        }

        $leaveByStatus = HrLeaveRequest::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get();

        $leaveCalendar = HrLeaveRequest::query()
            ->with(['staff:id,first_name,last_name,staff_id', 'leaveType:id,name'])
            ->where('status', '!=', 'Rejected')
            ->whereNotNull('from_date')
            ->latest('id')
            ->limit(40)
            ->get()
            ->map(fn ($r) => [
                'name' => trim(($r->staff->first_name ?? '') . ' ' . ($r->staff->last_name ?? '')),
                'type' => $r->leaveType->name ?? 'Leave',
                'from' => optional($r->from_date)->format('d M'),
                'to' => optional($r->to_date)->format('d M'),
                'status' => $r->status,
            ])
            ->values();

        $leaveBalance = collect();
        $year = (int) now()->year;
        if (Schema::hasTable('hr_staff_leave_balances')) {
            $leaveBalance = HrStaffLeaveBalance::query()
                ->where('hr_staff_leave_balances.year', $year)
                ->join('hr_leave_types', 'hr_leave_types.id', '=', 'hr_staff_leave_balances.hr_leave_type_id')
                ->selectRaw('hr_leave_types.name as type_name')
                ->selectRaw('SUM(hr_staff_leave_balances.entitled_days) as entitled_sum')
                ->selectRaw('SUM(hr_staff_leave_balances.used_days) as used_sum')
                ->groupBy('hr_leave_types.id', 'hr_leave_types.name')
                ->orderBy('hr_leave_types.name')
                ->get()
                ->map(fn ($r) => [
                    'name' => $r->type_name,
                    'used' => (float) $r->used_sum,
                    'available' => max(0, (float) $r->entitled_sum - (float) $r->used_sum),
                ])
                ->values();
        } elseif (Schema::hasTable('hr_leave_types')) {
            $leaveBalance = HrLeaveType::query()
                ->select('hr_leave_types.id', 'hr_leave_types.name')
                ->selectRaw('COALESCE(SUM(CASE WHEN r.status = "Approved" THEN r.total_days ELSE 0 END), 0) as used_days')
                ->selectRaw('COALESCE(SUM(CASE WHEN r.status != "Rejected" THEN r.total_days ELSE 0 END), 0) - COALESCE(SUM(CASE WHEN r.status = "Approved" THEN r.total_days ELSE 0 END), 0) as available_days')
                ->leftJoin('hr_leave_requests as r', 'r.hr_leave_type_id', '=', 'hr_leave_types.id')
                ->groupBy('hr_leave_types.id', 'hr_leave_types.name')
                ->orderBy('hr_leave_types.name')
                ->get()
                ->map(fn ($t) => [
                    'name' => $t->name,
                    'used' => (float) $t->used_days,
                    'available' => max(0, (float) $t->available_days),
                ]);
        }

        return [
            'leaveByStatus' => $leaveByStatus,
            'leaveCalendar' => $leaveCalendar,
            'leaveBalance' => $leaveBalance,
        ];
    }

    private function recruitmentData(): array
    {
        if (!Schema::hasTable('hr_recruitment_vacancies')) {
            return ['vacancies' => collect()];
        }

        $vacancies = HrRecruitmentVacancy::query()
            ->with(['department:id,name'])
            ->latest('id')
            ->limit(100)
            ->get();

        return ['vacancies' => $vacancies];
    }

    private function trainingData(): array
    {
        if (!Schema::hasTable('hr_training_programs')) {
            return ['programs' => collect()];
        }

        $programs = HrTrainingProgram::query()
            ->orderByDesc('schedule_date')
            ->limit(100)
            ->get();

        return ['programs' => $programs];
    }

    private function reportsData(): array
    {
        $monthlyStaff = Staff::query()
            ->selectRaw("DATE_FORMAT(date_of_joining, '%Y-%m') as month_key")
            ->selectRaw('COUNT(*) as total')
            ->whereNotNull('date_of_joining')
            ->whereDate('date_of_joining', '>=', now()->subMonths(5)->startOfMonth()->toDateString())
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->get();

        $payrollTrend = collect();
        if (Schema::hasTable('hr_payroll_records')) {
            $payrollTrend = HrPayrollRecord::query()
                ->selectRaw("DATE_FORMAT(payroll_month, '%Y-%m') as month_key")
                ->selectRaw('SUM(net_pay) as total_net')
                ->whereDate('payroll_month', '>=', now()->subMonths(5)->startOfMonth()->toDateString())
                ->groupBy('month_key')
                ->orderBy('month_key')
                ->get();
        }

        return [
            'monthlyStaff' => $monthlyStaff,
            'payrollTrend' => $payrollTrend,
            'departmentBreakup' => HrDepartment::query()->withCount('staff')->orderByDesc('staff_count')->limit(10)->get(),
        ];
    }

    private function buildAlerts(): array
    {
        $alerts = [];

        if (Schema::hasTable('hr_leave_requests')) {
            $pendingLeaves = HrLeaveRequest::query()->where('status', 'Pending')->count();
            $alerts[] = [
                'type' => 'warning',
                'text' => $pendingLeaves . ' leave applications pending approval.',
            ];
        }

        if (Schema::hasTable('hr_recruitment_vacancies')) {
            $openVacancies = HrRecruitmentVacancy::query()->where('status', 'Open')->count();
            $alerts[] = [
                'type' => 'info',
                'text' => $openVacancies . ' active recruitment vacancies are open.',
            ];
        }

        if (Schema::hasTable('hr_training_programs')) {
            $upcomingTraining = HrTrainingProgram::query()
                ->where('status', 'Scheduled')
                ->whereDate('schedule_date', '>=', now()->toDateString())
                ->whereDate('schedule_date', '<=', now()->addDays(14)->toDateString())
                ->count();

            $alerts[] = [
                'type' => 'success',
                'text' => $upcomingTraining . ' training sessions scheduled in next 14 days.',
            ];
        }

        if (empty($alerts)) {
            $alerts[] = [
                'type' => 'info',
                'text' => 'No active HR alerts right now.',
            ];
        }

        return $alerts;
    }

    private function generateStaffCode(): string
    {
        $prefix = 'HR-' . now()->format('Y') . '-';
        $last = Staff::query()->where('staff_id', 'like', $prefix . '%')->orderByDesc('id')->first();
        $next = 1;

        if ($last && preg_match('/(\d+)$/', (string) $last->staff_id, $matches)) {
            $next = ((int) $matches[1]) + 1;
        }

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    private function generateLeaveRequestNo(): string
    {
        $prefix = 'LR-' . now()->format('Y') . '-';
        $last = HrLeaveRequest::query()->where('request_no', 'like', $prefix . '%')->orderByDesc('id')->first();
        $next = 1;

        if ($last && preg_match('/(\d+)$/', (string) $last->request_no, $matches)) {
            $next = ((int) $matches[1]) + 1;
        }

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Dates (Y-m-d) per staff that fall inside an approved leave overlapping the given inclusive range.
     *
     * @param  array<int>  $staffIds
     * @return array<int, array<string, bool>>
     */
    private function approvedLeaveCoverageByStaff(int $hospitalId, array $staffIds, Carbon $rangeStart, Carbon $rangeEnd): array
    {
        $map = [];
        if ($staffIds === [] || !Schema::hasTable('hr_leave_requests')) {
            return $map;
        }

        $rangeStart = $rangeStart->copy()->startOfDay();
        $rangeEnd = $rangeEnd->copy()->startOfDay();

        $leaves = HrLeaveRequest::withoutGlobalScopes()
            ->where('hospital_id', $hospitalId)
            ->where('status', 'Approved')
            ->whereIn('staff_id', $staffIds)
            ->whereDate('from_date', '<=', $rangeEnd->toDateString())
            ->whereDate('to_date', '>=', $rangeStart->toDateString())
            ->get(['staff_id', 'from_date', 'to_date']);

        foreach ($leaves as $lr) {
            $sid = (int) $lr->staff_id;
            if ($sid <= 0) {
                continue;
            }
            $from = Carbon::parse($lr->from_date)->startOfDay();
            $to = Carbon::parse($lr->to_date)->startOfDay();
            $clipStart = $from->greaterThan($rangeStart) ? $from : $rangeStart->copy();
            $clipEnd = $to->lessThan($rangeEnd) ? $to : $rangeEnd->copy();
            for ($x = $clipStart->copy(); $x->lte($clipEnd); $x->addDay()) {
                $map[$sid][$x->toDateString()] = true;
            }
        }

        return $map;
    }

    public function attendanceExport(Request $request)
    {
        if (!$this->canViewAttendance()) {
            return response()->json(['status' => false, 'message' => 'You do not have permission to export attendance.'], 403);
        }

        $weekStart = $request->string('week_start')->default(now()->toDateString())->toString();
        $department = $request->string('department')->default('')->toString();

        try {
            $weekStartDate = Carbon::parse($weekStart)->startOfWeek(Carbon::MONDAY)->startOfDay();
        } catch (\Throwable $e) {
            $weekStartDate = now()->startOfWeek(Carbon::MONDAY)->startOfDay();
        }
        $weekEndDate = $weekStartDate->copy()->addDays(6)->endOfDay();

        $staffQuery = Staff::query()
            ->where('hospital_id', $this->hospital_id)
            ->where('status', 'Active');

        if ($department !== '') {
            $staffQuery->where('hr_department_id', (int) $department);
        }

        $staffRows = $staffQuery->orderBy('first_name')->get();

        $days = [];
        for ($d = $weekStartDate->copy(); $d->lte($weekEndDate); $d->addDay()) {
            $days[] = $d->copy();
        }

        $recordsByStaff = collect();
        if (Schema::hasTable('hr_attendance_records') && $staffRows->isNotEmpty()) {
            $recordsByStaff = HrAttendanceRecord::withoutGlobalScopes()
                ->where('hospital_id', (int) $this->hospital_id)
                ->whereIn('staff_id', $staffRows->pluck('id'))
                ->whereBetween('attendance_date', [$weekStartDate->toDateString(), $weekEndDate->toDateString()])
                ->get()
                ->groupBy('staff_id');
        }

        $leaveCoverage = $this->approvedLeaveCoverageByStaff(
            (int) $this->hospital_id,
            $staffRows->pluck('id')->all(),
            $weekStartDate->copy()->startOfDay(),
            $weekStartDate->copy()->addDays(6)->startOfDay()
        );

        // CSV Header
        $csv = "Staff ID,Name,Department," . implode(',', array_map(function ($d) {
            return $d->format('M j');
        }, $days)) . "\n";

        // CSV Rows
        foreach ($staffRows as $staff) {
            $name = trim(($staff->first_name ?? '') . ' ' . ($staff->last_name ?? ''));
            $departmentName = $staff->department->name ?? 'Unassigned';
            $staffRecs = $recordsByStaff[$staff->id] ?? collect();
            $recByDay = $staffRecs->keyBy(function ($rec) {
                return Carbon::parse($rec->attendance_date)->toDateString();
            });

            $row = [
                $staff->staff_id ?? $staff->id,
                $name !== '' ? $name : 'Unknown',
                $departmentName,
            ];

            foreach ($days as $day) {
                $dayKey = $day->copy()->startOfDay()->toDateString();
                $rec = $recByDay[$dayKey] ?? null;
                $status = $rec?->status ?? 'Absent';
                $onApprovedLeave = !empty($leaveCoverage[(int) $staff->id][$dayKey]);

                $code = 'A';
                if ($status === 'Present') {
                    $code = 'P';
                } elseif ($status === 'Holiday') {
                    $code = 'H';
                } elseif ($status === 'Leave' || ($onApprovedLeave && $status !== 'Present')) {
                    $code = 'L';
                }

                $row[] = $code;
            }

            $csv .= '"' . implode('","', array_map(function ($val) {
                return str_replace('"', '""', (string) $val);
            }, $row)) . "\"\n";
        }

        $fileName = 'Attendance_' . $weekStartDate->format('Y-m-d') . '_to_' . $weekEndDate->format('Y-m-d') . '.csv';

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }

    private function resolveStaffDesignation(Staff $staff): string
    {
        $designation = trim((string) ($staff->designation->name ?? ''));
        if ($designation !== '') {
            return $designation;
        }

        $note = (string) ($staff->note ?? '');
        if (stripos($note, 'Designation:') === 0) {
            $parsed = trim(substr($note, strlen('Designation:')));
            if ($parsed !== '') {
                return $parsed;
            }
        }

        return 'N/A';
    }

    private function attendanceStatusBadgeClass(string $statusLabel): string
    {
        $normalized = strtoupper(trim($statusLabel));

        if ($normalized === 'ABSENT' || $normalized === 'A') {
            return 'badge-red';
        }

        if ($normalized === 'LEAVE' || $normalized === 'ONLEAVE' || $normalized === 'HOLIDAY' || $normalized === 'H') {
            return 'badge-blue';
        }

        if (str_contains($normalized, 'OT')) {
            return 'badge-blue';
        }

        if (str_contains($normalized, 'L') && !str_contains($normalized, 'LEAVE')) {
            return 'badge-orange';
        }

        return 'badge-green';
    }

    private function buildAttendanceCombinedStatus(
        string $status,
        string $dayType,
        int $lateCount,
        bool $isMissPunch,
        bool $isOvertime,
        float $overtimeHours
    ): string {
        if ($status === 'Absent') {
            return 'A';
        }

        if ($status === 'Leave') {
            return 'OnLeave';
        }

        if ($status === 'Holiday') {
            return 'H';
        }

        $parts = ['P'];

        if ($lateCount > 0) {
            $parts[] = 'L';
        }

        if ($dayType === 'Half Day') {
            $parts[] = 'HD';
        } elseif ($dayType === 'Full Day') {
            $parts[] = 'FD';
        }

        if ($isMissPunch) {
            $parts[] = 'MP';
        }

        if ($isOvertime && $overtimeHours > 0) {
            $parts[] = 'OT';
        }

        return implode('/', $parts);
    }

    private function buildAttendanceChangeSummary(array $oldData, array $newData): array
    {
        $changedFields = [];
        $chunks = [];

        foreach ($newData as $key => $newValue) {
            $oldValue = $oldData[$key] ?? null;

            $oldNormalized = is_bool($oldValue) ? (int) $oldValue : (string) ($oldValue ?? '');
            $newNormalized = is_bool($newValue) ? (int) $newValue : (string) ($newValue ?? '');

            if ((string) $oldNormalized !== (string) $newNormalized) {
                $changedFields[] = $key;
                $chunks[] = $key . ': ' . ($oldNormalized === '' ? '-' : $oldNormalized) . ' -> ' . ($newNormalized === '' ? '-' : $newNormalized);
            }
        }

        return [
            'changed_fields' => $changedFields,
            'summary' => implode('; ', $chunks),
        ];
    }

    private function canViewAttendance(): bool
    {
        $user = auth()->user();

        return (bool) ($user?->can('view-attendance') || $user?->can('view-staff'));
    }

    private function canCreateAttendance(): bool
    {
        $user = auth()->user();

        return (bool) ($user?->can('create-attendance') || $user?->can('create-staff'));
    }

    private function canEditAttendance(): bool
    {
        $user = auth()->user();

        return (bool) ($user?->can('edit-attendance') || $user?->can('edit-staff'));
    }

    private function canViewPayroll(): bool
    {
        $user = auth()->user();

        return (bool) ($user?->can('view-payroll') || $user?->can('view-staff'));
    }

    private function sanitizePhone(?string $value): ?int
    {
        if (!$value) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $value) ?: '';

        if ($digits === '' || (int) $digits > 2147483647) {
            return null;
        }

        return (int) $digits;
    }
}
