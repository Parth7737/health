<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\MedicineFrequency;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Yajra\DataTables\Facades\DataTables;

class FrequencyController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-frequency', ['only' => ['store']]);
        $this->middleware('permission:edit-frequency', ['only' => ['update']]);
        $this->middleware('permission:delete-frequency', ['only' => ['destroy']]);
        $this->routes = [
            'destroy'   => route('hospital.settings.pharmacy.frequency.destroy', ['frequency' => '__FREQUENCY__']),
            'store'     => route('hospital.settings.pharmacy.frequency.store'),
            'loadtable' => route('hospital.settings.pharmacy.frequency-load'),
            'showform'  => route('hospital.settings.pharmacy.frequency.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.settings.pharmacy.frequency.index', [
            'pathurl' => 'frequency',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = MedicineFrequency::select('*');
        return DataTables::of($data)
            ->editColumn('schedule_times', function ($row) {
                if (empty($row->schedule_times) || !is_array($row->schedule_times)) {
                    return '-';
                }

                return implode(', ', array_map(
                    fn (string $time) => $this->formatTime12($time),
                    $row->schedule_times
                ));
            })
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.pharmacy.frequency.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if ($id) {
            $data = MedicineFrequency::where('id', $id)->first();
        }
        return view('hospital.settings.pharmacy.frequency.form', compact('data', 'id'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'frequency' => 'required|string|max:255|unique:medicine_frequencies,frequency,' . $request->id . ',id,hospital_id,' . $this->hospital_id,
            'no_of_medicine' => 'required|integer|min:1|max:12',
            'schedule_times' => 'required|array|min:1',
            'schedule_times.*' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $normalizedTimes = $this->normalizeScheduleTimes($request->input('schedule_times', []));
        $expectedCount = (int) $request->input('no_of_medicine');

        if (count($normalizedTimes) !== $expectedCount) {
            return response()->json([
                'status' => false,
                'message' => 'Please provide exactly ' . $expectedCount . ' MAR dose time(s).',
            ], 422);
        }

        if (count($normalizedTimes) !== count(array_unique($normalizedTimes))) {
            return response()->json([
                'status' => false,
                'message' => 'Duplicate MAR dose times are not allowed.',
            ], 422);
        }

        MedicineFrequency::updateOrCreate(
            ['id' => $request->id],
            [
                'hospital_id' => $this->hospital_id,
                'frequency' => $request->frequency,
                'no_of_medicine' => $expectedCount,
                'schedule_times' => $normalizedTimes,
            ]
        );

        $msg = $request->id ? 'Frequency updated successfully.' : 'Frequency created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(MedicineFrequency $frequency)
    {
        if ($frequency->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }
        $frequency->delete();
        return response()->json(['status' => true, 'message' => 'Frequency deleted successfully.']);
    }

    /**
     * @param array<int, mixed> $times
     * @return array<int, string>
     */
    private function normalizeScheduleTimes(array $times): array
    {
        return collect($times)
            ->map(fn ($time) => $this->parseTimeTo24Hour((string) $time))
            ->filter()
            ->values()
            ->all();
    }

    private function parseTimeTo24Hour(string $time): ?string
    {
        $time = trim($time);

        if (preg_match('/^(\d{1,2}):(\d{2})\s*(AM|PM)$/i', $time, $matches)) {
            $hour = (int) $matches[1];
            $minute = (int) $matches[2];
            $period = strtoupper($matches[3]);

            if ($period === 'PM' && $hour !== 12) {
                $hour += 12;
            }
            if ($period === 'AM' && $hour === 12) {
                $hour = 0;
            }

            return sprintf('%02d:%02d', $hour, $minute);
        }

        if (preg_match('/^\d{1,2}:\d{2}$/', $time)) {
            [$hour, $minute] = array_map('intval', explode(':', $time));

            return sprintf('%02d:%02d', $hour, $minute);
        }

        return null;
    }

    private function formatTime12(string $time): string
    {
        $normalized = $this->parseTimeTo24Hour($time);
        if (!$normalized) {
            return $time;
        }

        [$hour, $minute] = array_map('intval', explode(':', $normalized));
        $period = $hour >= 12 ? 'PM' : 'AM';
        $hour12 = $hour % 12;
        if ($hour12 === 0) {
            $hour12 = 12;
        }

        return sprintf('%d:%02d %s', $hour12, $minute, $period);
    }
}
