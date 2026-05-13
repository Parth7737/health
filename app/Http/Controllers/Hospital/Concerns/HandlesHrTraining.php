<?php

namespace App\Http\Controllers\Hospital\Concerns;

use App\Models\Hospital;
use App\Models\HrTrainingCategory;
use App\Models\HrTrainingParticipant;
use App\Models\HrTrainingProgram;
use App\Models\HrTrainingProgramLog;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

trait HandlesHrTraining
{
    protected function userCanEditTraining(): bool
    {
        return auth()->user()->can('edit-staff');
    }

    protected function syncTrainingParticipantCount(HrTrainingProgram $program): void
    {
        if (!Schema::hasTable('hr_training_participants')) {
            return;
        }
        $count = $program->trainingParticipants()->count();
        $program->participants = $count;
        $program->saveQuietly();
    }

    protected function appendTrainingProgramLog(
        HrTrainingProgram $program,
        string $eventType,
        string $message,
        ?string $note = null
    ): void {
        if (!Schema::hasTable('hr_training_program_logs')) {
            return;
        }

        HrTrainingProgramLog::query()->create([
            'hospital_id' => (int) $program->hospital_id,
            'hr_training_program_id' => (int) $program->id,
            'event_type' => $eventType,
            'message' => $message,
            'note' => $note,
            'created_by' => auth()->id(),
            'created_at' => now(),
        ]);
    }

    protected function trainingProgramCategoryLabel(HrTrainingProgram $program): ?string
    {
        $program->loadMissing('trainingCategory');
        if ($program->trainingCategory) {
            return (string) $program->trainingCategory->name;
        }

        return $program->category ? (string) $program->category : null;
    }

    protected function generateCertificateNumber(HrTrainingProgram $program, HrTrainingParticipant $participant): string
    {
        return 'TRN-' . now()->format('Y') . '-' . str_pad((string) $program->hospital_id, 4, '0', STR_PAD_LEFT)
            . '-' . str_pad((string) $program->id, 5, '0', STR_PAD_LEFT)
            . '-' . str_pad((string) $participant->id, 5, '0', STR_PAD_LEFT);
    }

    protected function renderTrainingCertificatePdf(HrTrainingProgram $program, HrTrainingParticipant $participant, Hospital $hospital): ?string
    {
        $staff = $participant->staff;
        $participantName = $staff ? (string) $staff->full_name : 'Participant';

        $html = view('hospital.hr.dashboard.tabs.pdf.training-certificate', [
            'hospitalName' => $hospital->name ?? 'Hospital',
            'programTitle' => $program->title,
            'category' => $this->trainingProgramCategoryLabel($program),
            'scheduleDate' => optional($program->schedule_date)->format('d F Y'),
            'venue' => $program->venue,
            'durationHours' => $program->duration_hours,
            'participantName' => $participantName,
            'certificateNumber' => $participant->certificate_number,
            'issuedDate' => now()->format('d F Y'),
        ])->render();

        if (!class_exists(\Dompdf\Dompdf::class)) {
            return null;
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
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();

            return $dompdf->output();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * @return array{ok: bool, skipped: bool, message: string}
     */
    protected function generateAndStoreParticipantCertificate(
        HrTrainingProgram $program,
        HrTrainingParticipant $participant,
        bool $forceRegenerate = false
    ): array {
        if (!Schema::hasTable('hr_training_participants')) {
            return ['ok' => false, 'skipped' => false, 'message' => 'Participants table missing.'];
        }

        $hospital = Hospital::query()->find((int) $program->hospital_id);
        if (!$hospital) {
            return ['ok' => false, 'skipped' => false, 'message' => 'Hospital not found.'];
        }

        $participant->loadMissing('staff');
        $program->loadMissing('trainingCategory');

        if (!$forceRegenerate && $participant->certificate_path && Storage::disk('local')->exists($participant->certificate_path)) {
            return ['ok' => true, 'skipped' => true, 'message' => 'Certificate already exists.'];
        }

        if ($forceRegenerate && $participant->certificate_path && Storage::disk('local')->exists($participant->certificate_path)) {
            Storage::disk('local')->delete($participant->certificate_path);
        }

        $participant->certificate_number = $this->generateCertificateNumber($program, $participant);
        $pdf = $this->renderTrainingCertificatePdf($program, $participant, $hospital);
        if ($pdf === null) {
            $participant->certificate_number = null;
            $participant->certificate_path = null;
            $participant->certificate_issued_at = null;
            $participant->saveQuietly();

            return ['ok' => false, 'skipped' => false, 'message' => 'PDF engine could not render the certificate.'];
        }

        $relative = 'hr-training-certificates/' . (int) $program->hospital_id . '/' . (int) $program->id . '/participant-' . (int) $participant->id . '.pdf';
        Storage::disk('local')->put($relative, $pdf);

        $participant->certificate_path = $relative;
        $participant->certificate_issued_at = now();
        $participant->save();

        return ['ok' => true, 'skipped' => false, 'message' => 'Certificate saved.'];
    }

    public function trainingProgramsData(Request $request): JsonResponse
    {
        $draw = (int) $request->input('draw', 1);
        $start = max((int) $request->input('start', 0), 0);
        $length = max((int) $request->input('length', 10), 1);
        $search = trim((string) $request->input('search_custom', ''));
        $status = trim((string) $request->input('status_filter', ''));

        if (!Schema::hasTable('hr_training_programs')) {
            return response()->json([
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
            ]);
        }

        $baseQuery = HrTrainingProgram::query()->select('hr_training_programs.*');

        if (Schema::hasTable('hr_training_categories') && Schema::hasColumn('hr_training_programs', 'hr_training_category_id')) {
            $baseQuery->leftJoin('hr_training_categories as htc', 'htc.id', '=', 'hr_training_programs.hr_training_category_id')
                ->addSelect('htc.name as category_joined');
        }

        $totalRecords = (clone $baseQuery)->count('hr_training_programs.id');

        if ($search !== '') {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $search) . '%';
            $baseQuery->where(function ($q) use ($like) {
                $q->where('hr_training_programs.title', 'like', $like)
                    ->orWhere('hr_training_programs.category', 'like', $like)
                    ->orWhere('hr_training_programs.trainer_name', 'like', $like);
                if (Schema::hasTable('hr_training_categories')) {
                    $q->orWhere('htc.name', 'like', $like);
                }
            });
        }

        if ($status !== '') {
            $baseQuery->whereRaw('LOWER(hr_training_programs.status) = ?', [strtolower($status)]);
        }

        $filteredRecords = (clone $baseQuery)->count('hr_training_programs.id');

        $rows = $baseQuery
            ->orderByDesc('hr_training_programs.schedule_date')
            ->orderByDesc('hr_training_programs.id')
            ->skip($start)
            ->take($length)
            ->get();

        $canEdit = $this->userCanEditTraining();

        $data = $rows->map(function ($row) use ($canEdit) {
            $st = strtolower((string) $row->status);
            $badgeClass = $st === 'completed' ? 'green' : ($st === 'cancelled' ? 'red' : 'blue');
            $dateLabel = optional($row->schedule_date)->format('d M Y') ?: '—';

            $catLabel = $row->category_joined ?? $row->category;

            $editBtn = $canEdit && $st === 'scheduled'
                ? '<button type="button" class="hrx-btn-lite hrx-training-edit-btn" data-training-id="' . (int) $row->id . '" title="Edit"><i class="fa fa-edit"></i></button>'
                : '';

            return [
                'id' => (int) $row->id,
                'title' => '<span class="fw-700">' . e($row->title) . '</span>',
                'category' => '<span class="hrx-badge recruitment-dept">' . e($catLabel ?: '—') . '</span>',
                'date' => e($dateLabel),
                'trainer' => e($row->trainer_name ?: '—'),
                'participants' => '<span class="fw-700">' . e((string) ($row->participants ?? 0)) . '</span>',
                'status' => '<span class="hrx-badge ' . e($badgeClass) . '">' . e($row->status ?: 'Scheduled') . '</span>',
                'action' => '<div class="hrx-actions hrx-recruitment-table-actions">'
                    . '<button type="button" class="hrx-btn-lite hrx-recruitment-view-btn hrx-training-view" data-training-id="' . (int) $row->id . '" title="View"><i class="fa fa-eye"></i></button>'
                    . $editBtn
                    . '</div>',
            ];
        })->values();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }

    public function storeTrainingProgram(Request $request): JsonResponse
    {
        if (!$this->userCanEditTraining()) {
            return response()->json(['status' => false, 'message' => 'You do not have permission to manage training.'], 403);
        }

        if (!Schema::hasTable('hr_training_programs')) {
            return response()->json(['status' => false, 'message' => 'Training table not found. Run migrations first.'], 422);
        }

        $rules = [
            'id' => 'nullable|integer|exists:hr_training_programs,id',
            'title' => 'required|string|max:200',
            'schedule_date' => 'required|date',
            'trainer_name' => 'nullable|string|max:150',
            'description' => 'nullable|string|max:5000',
            'venue' => 'nullable|string|max:191',
            'duration_hours' => 'nullable|integer|min:1|max:500',
        ];

        if (Schema::hasTable('hr_training_categories') && Schema::hasColumn('hr_training_programs', 'hr_training_category_id')) {
            $rules['hr_training_category_id'] = 'nullable|integer|exists:hr_training_categories,id';
        } else {
            $rules['category'] = 'nullable|string|max:120';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $programId = (int) $request->input('id', 0);
        $program = $programId > 0 ? HrTrainingProgram::query()->find($programId) : new HrTrainingProgram;

        if (!$program) {
            return response()->json(['status' => false, 'message' => 'Program not found.'], 404);
        }

        if ($program->exists && strtolower((string) $program->status) !== 'scheduled') {
            return response()->json(['status' => false, 'message' => 'Only scheduled programmes can be edited.'], 422);
        }

        if (!$program->exists) {
            $program->hospital_id = (int) $this->hospital_id;
            $program->status = 'Scheduled';
            $program->participants = 0;
        }

        $program->title = $request->string('title')->toString();
        $program->schedule_date = Carbon::parse((string) $request->input('schedule_date'))->toDateString();
        $program->trainer_name = $request->filled('trainer_name') ? $request->string('trainer_name')->toString() : null;

        if (Schema::hasTable('hr_training_categories') && Schema::hasColumn('hr_training_programs', 'hr_training_category_id')) {
            $catId = $request->filled('hr_training_category_id') ? (int) $request->input('hr_training_category_id') : null;
            $program->hr_training_category_id = $catId;
            $cat = $catId ? HrTrainingCategory::query()->find($catId) : null;
            $program->category = $cat ? $cat->name : null;
        } else {
            $program->category = $request->filled('category') ? $request->string('category')->toString() : null;
        }

        if (Schema::hasColumn('hr_training_programs', 'description')) {
            $program->description = $request->filled('description') ? $request->string('description')->toString() : null;
        }
        if (Schema::hasColumn('hr_training_programs', 'venue')) {
            $program->venue = $request->filled('venue') ? $request->string('venue')->toString() : null;
        }
        if (Schema::hasColumn('hr_training_programs', 'duration_hours')) {
            $program->duration_hours = $request->filled('duration_hours') ? (int) $request->input('duration_hours') : null;
        }

        $program->save();

        if (Schema::hasTable('hr_training_program_logs')) {
            if ($programId === 0) {
                $this->appendTrainingProgramLog(
                    $program,
                    'program_created',
                    'Training programme scheduled: ' . $program->title
                );
            } else {
                $this->appendTrainingProgramLog(
                    $program,
                    'program_updated',
                    'Training programme details were updated (title, category, schedule, trainer, venue, etc.).'
                );
            }
        }

        return response()->json([
            'status' => true,
            'message' => $programId > 0 ? 'Training programme updated.' : 'Training programme scheduled.',
            'program_id' => (int) $program->id,
        ]);
    }

    public function addTrainingParticipant(Request $request): JsonResponse
    {
        if (!$this->userCanEditTraining()) {
            return response()->json(['status' => false, 'message' => 'Forbidden.'], 403);
        }

        if (!Schema::hasTable('hr_training_participants')) {
            return response()->json(['status' => false, 'message' => 'Participants table missing. Run migrations.'], 422);
        }

        $validator = Validator::make($request->all(), [
            'hr_training_program_id' => 'required|integer|exists:hr_training_programs,id',
            'staff_id' => 'required|integer|exists:staff,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $program = HrTrainingProgram::query()->find((int) $request->input('hr_training_program_id'));
        if (!$program || strtolower((string) $program->status) !== 'scheduled') {
            return response()->json(['status' => false, 'message' => 'Participants can only be added to scheduled programmes.'], 422);
        }

        $staff = Staff::query()->find((int) $request->input('staff_id'));
        if (!$staff) {
            return response()->json(['status' => false, 'message' => 'Staff not found.'], 404);
        }

        $exists = HrTrainingParticipant::query()
            ->where('hr_training_program_id', $program->id)
            ->where('staff_id', $staff->id)
            ->exists();

        if ($exists) {
            return response()->json(['status' => false, 'message' => 'This staff member is already enrolled.'], 422);
        }

        HrTrainingParticipant::query()->create([
            'hospital_id' => (int) $program->hospital_id,
            'hr_training_program_id' => (int) $program->id,
            'staff_id' => (int) $staff->id,
        ]);

        $this->syncTrainingParticipantCount($program);
        $this->appendTrainingProgramLog(
            $program,
            'participant_added',
            'Participant enrolled: ' . (string) $staff->full_name . ' (Staff ID: ' . ($staff->staff_id ?? '—') . ').'
        );

        return response()->json(['status' => true, 'message' => 'Participant added.']);
    }

    public function removeTrainingParticipant(Request $request): JsonResponse
    {
        if (!$this->userCanEditTraining()) {
            return response()->json(['status' => false, 'message' => 'Forbidden.'], 403);
        }

        if (!Schema::hasTable('hr_training_participants')) {
            return response()->json(['status' => false, 'message' => 'Participants table missing.'], 422);
        }

        $validator = Validator::make($request->all(), [
            'participant_id' => 'required|integer|exists:hr_training_participants,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $participant = HrTrainingParticipant::query()->with('staff')->find((int) $request->input('participant_id'));
        if (!$participant) {
            return response()->json(['status' => false, 'message' => 'Participant not found.'], 404);
        }

        $program = HrTrainingProgram::query()->find((int) $participant->hr_training_program_id);
        if (!$program || strtolower((string) $program->status) !== 'scheduled') {
            return response()->json(['status' => false, 'message' => 'Participants can only be removed while the programme is scheduled.'], 422);
        }

        $name = $participant->staff ? (string) $participant->staff->full_name : 'Participant';
        $empId = $participant->staff?->staff_id ?? '—';
        $participant->delete();
        $this->syncTrainingParticipantCount($program);
        $this->appendTrainingProgramLog(
            $program,
            'participant_removed',
            'Participant removed from roster: ' . $name . ' (Employee ID: ' . $empId . ').'
        );

        return response()->json(['status' => true, 'message' => 'Participant removed.']);
    }

    public function updateTrainingProgramStatus(Request $request): JsonResponse
    {
        if (!$this->userCanEditTraining()) {
            return response()->json(['status' => false, 'message' => 'Forbidden.'], 403);
        }

        if (!Schema::hasTable('hr_training_programs')) {
            return response()->json(['status' => false, 'message' => 'Training table not found.'], 422);
        }

        $validator = Validator::make($request->all(), [
            'hr_training_program_id' => 'required|integer|exists:hr_training_programs,id',
            'status' => 'required|in:Scheduled,Completed,Cancelled',
            'note' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $program = HrTrainingProgram::query()->find((int) $request->input('hr_training_program_id'));
        if (!$program) {
            return response()->json(['status' => false, 'message' => 'Program not found.'], 404);
        }

        $prev = (string) $program->status;
        $next = $request->string('status')->toString();
        $note = $request->filled('note') ? trim($request->string('note')->toString()) : null;

        if ($prev === $next) {
            return response()->json(['status' => true, 'message' => 'No change.']);
        }

        if ($prev !== 'Scheduled' && $next !== $prev) {
            return response()->json(['status' => false, 'message' => 'Status can only be changed from Scheduled to Completed or Cancelled.'], 422);
        }

        if ($prev === 'Scheduled' && !in_array($next, ['Completed', 'Cancelled'], true)) {
            return response()->json(['status' => false, 'message' => 'Invalid status transition.'], 422);
        }

        $program->status = $next;
        $program->save();

        $this->appendTrainingProgramLog(
            $program,
            'status_changed',
            'Programme status updated: ' . $prev . ' → ' . $next . '.',
            $note
        );

        $message = 'Training status updated.';
        if ($next === 'Completed') {
            $message = 'Programme marked completed. Generate certificate PDFs per participant from the roster when you are ready.';
        }

        return response()->json([
            'status' => true,
            'message' => $message,
            'certificates_issued' => 0,
        ]);
    }

    public function generateTrainingParticipantCertificate(Request $request): JsonResponse
    {
        if (!$this->userCanEditTraining()) {
            return response()->json(['status' => false, 'message' => 'Forbidden.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'participant_id' => 'required|integer|exists:hr_training_participants,id',
            'regenerate' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $participant = HrTrainingParticipant::query()->with('staff')->find((int) $request->input('participant_id'));
        if (!$participant) {
            return response()->json(['status' => false, 'message' => 'Participant not found.'], 404);
        }

        $program = HrTrainingProgram::query()->with('trainingCategory')->find((int) $participant->hr_training_program_id);
        if (!$program) {
            return response()->json(['status' => false, 'message' => 'Programme not found.'], 404);
        }

        if (strtolower((string) $program->status) !== 'completed') {
            return response()->json(['status' => false, 'message' => 'Certificates can only be generated after the programme is marked Completed.'], 422);
        }

        $force = $request->boolean('regenerate', false);
        $result = $this->generateAndStoreParticipantCertificate($program, $participant, $force);

        if (!$result['ok']) {
            return response()->json(['status' => false, 'message' => $result['message']], 422);
        }

        $name = $participant->staff ? (string) $participant->staff->full_name : 'Participant';
        if ($result['skipped']) {
            return response()->json([
                'status' => true,
                'message' => 'Certificate already exists. Use regenerate to replace the PDF.',
                'skipped' => true,
            ]);
        }

        $this->appendTrainingProgramLog(
            $program,
            $force ? 'certificate_regenerated' : 'certificate_generated',
            ($force ? 'Certificate PDF replaced for ' : 'Certificate PDF generated for ') . $name . '.',
            $participant->fresh()->certificate_number
        );

        return response()->json(['status' => true, 'message' => $force ? 'Certificate regenerated.' : 'Certificate generated.']);
    }

    public function downloadTrainingCertificate(int $participantId): BinaryFileResponse
    {
        if (!Schema::hasTable('hr_training_participants')) {
            abort(404);
        }

        $participant = HrTrainingParticipant::query()->find($participantId);
        if (!$participant || !$participant->certificate_path) {
            abort(404);
        }

        if (!Storage::disk('local')->exists($participant->certificate_path)) {
            abort(404);
        }

        $abs = Storage::disk('local')->path($participant->certificate_path);
        $filename = 'Training-Certificate-' . preg_replace('/[^A-Za-z0-9_-]+/', '_', (string) ($participant->certificate_number ?: $participant->id)) . '.pdf';

        return response()->download($abs, $filename);
    }
}
