<?php

namespace App\Http\Controllers\Hospital;

use App\CentralLogics\Helpers;
use App\Http\Controllers\BaseHospitalController;
use App\Models\ChargeMaster;
use App\Models\DoctorOpdCharge;
use App\Models\Tpa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class ChargeMasterController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->routes = [
            'destroy'   => route('hospital.charges.charge-masters.destroy', ['charge_master' => '__CHARGE_MASTER__']),
            'store'     => route('hospital.charges.charge-masters.store'),
            'loadtable' => route('hospital.charges.charge-masters.load'),
            'showform'  => route('hospital.charges.charge-masters.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.charges.charge-masters.index', [
            'pathurl' => 'charge-masters',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = ChargeMaster::query()->with('related');

        return DataTables::of($data)
            ->addColumn('related_to', function ($row) {
                if ($row->related instanceof DoctorOpdCharge) {
                    return 'Doctor: ' . ($row->related->doctor?->full_name ?? 'N/A');
                }
                return $row->related_type ? class_basename($row->related_type) : '-';
            })
            ->editColumn('category', function ($row) {
                return ucwords(str_replace('_', ' ', $row->category));
            })
            ->editColumn('calculation_type', function ($row) {
                return ucfirst(str_replace('_', ' ', $row->calculation_type));
            })
            ->addColumn('status_badge', function ($row) {
                return $row->is_active
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-secondary">Inactive</span>';
            })
            ->addColumn('actions', function ($row) {
                return view('hospital.charges.charge-masters.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['status_badge', 'actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = null;

        if ($id) {
            $data = ChargeMaster::where('id', $id)
                ->where('hospital_id', $this->hospital_id)
                ->first();
        }

        $tpas = Tpa::query()->orderBy('name')->get(['id', 'name']);
        $tpaRates = $data
            ? $data->tpaRates()->pluck('rate', 'tpa_id')
            : collect();

        return view('hospital.charges.charge-masters.form', compact('data', 'id', 'tpas', 'tpaRates'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code'              => [
                'required', 'string', 'max:100',
                Rule::unique('charge_masters', 'code')
                    ->where('hospital_id', $this->hospital_id)
                    ->ignore($request->id),
            ],
            'name'              => 'required|string|max:255',
            'category'          => 'required|string|max:100',
            'calculation_type'  => 'required|in:fixed,daily',
            'billing_frequency' => 'required|in:one_time,per_day',
            'standard_rate'     => 'required|numeric|min:0',
            'description'       => 'nullable|string|max:500',
            'is_active'         => 'nullable|boolean',
            'tpa_rates' => 'nullable|array',
            'tpa_rates.*.tpa_id' => 'required|integer|exists:tpas,id',
            'tpa_rates.*.rate' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        DB::transaction(function () use ($request) {
            $chargeMaster = ChargeMaster::updateOrCreate(
                ['id' => $request->id],
                [
                    'hospital_id'       => $this->hospital_id,
                    'code'              => strtoupper(trim($request->code)),
                    'name'              => $request->name,
                    'category'          => $request->category,
                    'calculation_type'  => $request->calculation_type,
                    'billing_frequency' => $request->billing_frequency,
                    'standard_rate'     => $request->standard_rate,
                    'description'       => $request->description,
                    'is_active'         => $request->boolean('is_active', true),
                ]
            );

            $submittedTpaIds = [];
            $rows = $request->input('tpa_rates', []);
            foreach ($rows as $row) {
                $tpaId = (int) ($row['tpa_id'] ?? 0);
                if (!$tpaId) {
                    continue;
                }

                $submittedTpaIds[] = $tpaId;
                $rate = $row['rate'] ?? null;

                if ($rate === null || $rate === '') {
                    $chargeMaster->tpaRates()->where('tpa_id', $tpaId)->delete();
                    continue;
                }

                $chargeMaster->tpaRates()->updateOrCreate(
                    [
                        'hospital_id' => $this->hospital_id,
                        'tpa_id' => $tpaId,
                    ],
                    ['rate' => (float) $rate]
                );
            }

            $deleteQuery = $chargeMaster->tpaRates();
            if (!empty($submittedTpaIds)) {
                $deleteQuery->whereNotIn('tpa_id', $submittedTpaIds);
            }
            $deleteQuery->delete();
        });

        $msg = $request->id ? 'Charge Master updated successfully.' : 'Charge Master created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(ChargeMaster $charge_master)
    {
        if ($charge_master->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }

        if ($charge_master->related_type) {
            return response()->json([
                'status'  => false,
                'message' => 'This charge master is auto-managed. Delete the linked record (e.g. Doctor OPD Charge) to remove it.',
            ], 422);
        }

        $charge_master->delete();

        return response()->json(['status' => true, 'message' => 'Charge Master deleted successfully.']);
    }
}
