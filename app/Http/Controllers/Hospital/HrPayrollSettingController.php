<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\HrPayrollComponent;
use App\Models\HrPayrollSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class HrPayrollSettingController extends BaseHospitalController
{
    public function index(): View
    {
        $components = HrPayrollComponent::query()
            ->orderBy('component_type')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $settings = HrPayrollSetting::query()->first();

        return view('hospital.settings.hr.payroll-settings.index', [
            'pathurl' => 'hr-payroll-settings',
            'components' => $components,
            'settings' => $settings,
        ]);
    }

    public function storeComponent(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'nullable|integer|exists:hr_payroll_components,id',
            'name' => 'required|string|max:120',
            'component_type' => 'required|in:Allowance,Deduction',
            'value_type' => 'required|in:Fixed,Percentage',
            'value' => 'required|numeric|min:0',
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $payload = [
            'hospital_id' => $this->hospital_id,
            'name' => (string) $request->input('name'),
            'component_type' => (string) $request->input('component_type'),
            'value_type' => (string) $request->input('value_type'),
            'value' => (float) $request->input('value', 0),
            'sort_order' => (int) $request->input('sort_order', 0),
            'is_active' => $request->boolean('is_active', true),
        ];

        HrPayrollComponent::query()->updateOrCreate(
            ['id' => $request->input('id')],
            $payload
        );

        return back()->with('success', $request->filled('id') ? 'Payroll component updated.' : 'Payroll component created.');
    }

    public function destroyComponent(HrPayrollComponent $component): RedirectResponse
    {
        if ((int) $component->hospital_id !== (int) $this->hospital_id) {
            abort(403);
        }

        $component->delete();
        return back()->with('success', 'Payroll component deleted.');
    }

    public function saveGeneralSettings(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'standard_working_days' => 'required|integer|min:1|max:31',
            'leave_deduction_per_day' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        HrPayrollSetting::query()->updateOrCreate(
            ['hospital_id' => $this->hospital_id],
            [
                'standard_working_days' => (int) $request->input('standard_working_days', 30),
                'leave_deduction_per_day' => (float) $request->input('leave_deduction_per_day', 0),
            ]
        );

        return back()->with('success', 'Payroll settings saved.');
    }
}
