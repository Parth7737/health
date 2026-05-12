<?php

namespace App\Http\Controllers;

use App\Models\Hospital;
use App\Models\HrRecruitmentApplication;
use App\Models\HrRecruitmentVacancy;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use App\Scopes\HospitalScope;

/**
 * Public careers portal: list published vacancies for a hospital (by UUID) and accept applications.
 */
class CareersPortalController extends Controller
{
    public function index(string $uuid): View
    {
        $hospital = Hospital::query()
            ->where('uuid', $uuid)
            ->whereIn('status', ['Active', 'Approved'])
            ->firstOrFail();

        $vacancies = collect();
        if (Schema::hasTable('hr_recruitment_vacancies')) {
            $q = HrRecruitmentVacancy::query()
                ->withoutGlobalScope(HospitalScope::class)
                ->where('hospital_id', (int) $hospital->id)
                ->whereRaw('LOWER(status) = ?', ['open']);

            if (Schema::hasColumn('hr_recruitment_vacancies', 'is_published')) {
                $q->where('is_published', true);
            }

            $today = now()->toDateString();
            $q->where(function ($w) use ($today) {
                $w->whereNull('open_from')
                    ->orWhereDate('open_from', '<=', $today);
            })->where(function ($w) use ($today) {
                $w->whereNull('open_till')
                    ->orWhereDate('open_till', '>=', $today);
            });

            $vacancies = $q
                ->with(['department:id,name', 'designation:id,name'])
                ->orderByDesc('id')
                ->get();
        }

        return view('front.careers', [
            'hospital' => $hospital,
            'vacancies' => $vacancies,
            'applyUrl' => route('careers.apply', ['uuid' => $uuid]),
        ]);
    }

    public function apply(Request $request, string $uuid): JsonResponse
    {
        $hospital = Hospital::query()
            ->where('uuid', $uuid)
            ->whereIn('status', ['Active', 'Approved'])
            ->first();

        if (!$hospital) {
            return response()->json(['status' => false, 'message' => 'Hospital not found.'], 404);
        }

        if (!Schema::hasTable('hr_recruitment_applications')) {
            return response()->json(['status' => false, 'message' => 'Applications are not available.'], 422);
        }

        $validator = Validator::make($request->all(), [
            'vacancy_id' => 'required|integer|exists:hr_recruitment_vacancies,id',
            'full_name' => 'required|string|max:150',
            'email' => 'required|email|max:150',
            'phone' => 'nullable|string|max:25',
            'cover_letter' => 'nullable|string|max:5000',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $vacancy = HrRecruitmentVacancy::withoutGlobalScopes()->find((int) $request->input('vacancy_id'));
        if (!$vacancy || (int) $vacancy->hospital_id !== (int) $hospital->id) {
            return response()->json(['status' => false, 'message' => 'Vacancy not found.'], 404);
        }

        if (strtolower((string) $vacancy->status) !== 'open') {
            return response()->json(['status' => false, 'message' => 'This position is not accepting applications.'], 422);
        }

        if (Schema::hasColumn('hr_recruitment_vacancies', 'is_published') && !$vacancy->is_published) {
            return response()->json(['status' => false, 'message' => 'This vacancy is not published.'], 422);
        }

        $today = Carbon::today();
        if ($vacancy->open_from && $today->lt(Carbon::parse($vacancy->open_from)->startOfDay())) {
            return response()->json(['status' => false, 'message' => 'Applications have not opened yet.'], 422);
        }
        if ($vacancy->open_till && $today->gt(Carbon::parse($vacancy->open_till)->endOfDay())) {
            return response()->json(['status' => false, 'message' => 'The application period has ended.'], 422);
        }

        $resumePath = null;
        if ($request->hasFile('resume')) {
            $resumePath = $request->file('resume')->store('recruitment/resumes', 'public');
        }

        HrRecruitmentApplication::withoutGlobalScopes()->create([
            'hospital_id' => (int) $vacancy->hospital_id,
            'hr_recruitment_vacancy_id' => (int) $vacancy->id,
            'full_name' => $request->string('full_name')->toString(),
            'email' => $request->string('email')->toString(),
            'phone' => $request->filled('phone') ? $request->string('phone')->toString() : null,
            'cover_letter' => $request->filled('cover_letter') ? $request->string('cover_letter')->toString() : null,
            'resume_path' => $resumePath,
            'status' => 'Applied',
            'applied_at' => now(),
        ]);

        $this->refreshVacancyApplicantStats((int) $vacancy->id);

        return response()->json([
            'status' => true,
            'message' => 'Application submitted successfully.',
        ]);
    }

    private function refreshVacancyApplicantStats(int $vacancyId): void
    {
        if ($vacancyId <= 0 || !Schema::hasTable('hr_recruitment_applications')) {
            return;
        }

        $vacancy = HrRecruitmentVacancy::withoutGlobalScopes()->find($vacancyId);
        if (!$vacancy) {
            return;
        }

        $applicants = HrRecruitmentApplication::withoutGlobalScopes()
            ->where('hr_recruitment_vacancy_id', $vacancyId)
            ->count();

        $shortlisted = HrRecruitmentApplication::withoutGlobalScopes()
            ->where('hr_recruitment_vacancy_id', $vacancyId)
            ->whereIn('status', ['Shortlisted', 'Interview', 'Selected', 'Hired'])
            ->count();

        $vacancy->applicants = $applicants;
        $vacancy->shortlisted = $shortlisted;
        $vacancy->save();
    }
}
