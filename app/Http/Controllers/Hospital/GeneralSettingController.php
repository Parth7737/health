<?php

namespace App\Http\Controllers\Hospital;

use App\CentralLogics\Helpers;
use App\Http\Controllers\BaseHospitalController;
use App\Models\BusinessSetting;
use App\Models\Hospital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GeneralSettingController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();

        $this->middleware('permission:edit-hospital-data', ['only' => ['update']]);

        $this->routes = [
            'update' => route('hospital.settings.general-setting.update'),
        ];
    }

    public function index()
    {
        abort_if(!$this->hospital_id, 404, 'Hospital not found.');

        $hospital = Hospital::with([
            'user:id,enable_step,parent_id',
            'type:id,name',
            'hospital_admin:id,hospital_id,name,email,mobile_no,gender,state',
            'documents',
            'documents.doc:id,name',
            'specialities',
            'specialities.speciality:id,name,code',
            'services',
            'services.action',
            'services.subService:id,name',
            'licenses',
            'licenses.licenseType:id,name',
            'branches',
        ])->findOrFail($this->hospital_id);

        $empanelmentStepStatus = $hospital->user && $hospital->user->enable_step
            ? json_decode($hospital->user->enable_step)
            : json_decode(Helpers::get_settings('empanelment_step_status'));

        $defaultLogo = BusinessSetting::where('key', 'front_logo')->value('value');
        $defaultLogoUrl = $defaultLogo
            ? asset('public/storage/' . $defaultLogo)
            : asset('public/front/assets/img/paracare-logo.png');
        $hospitalLogoUrl = $hospital->image
            ? asset('public/storage/' . $hospital->image)
            : $defaultLogoUrl;

        return view('hospital.settings.general-setting.index', [
            'hospital' => $hospital,
            'empanelmentStepStatus' => $empanelmentStepStatus,
            'hospitalLogoUrl' => $hospitalLogoUrl,
            'defaultLogoUrl' => $defaultLogoUrl,
            'stickerWidthMm' => (string) $this->getHospitalSetting('sticker_width_mm', '90'),
            'stickerHeightMm' => (string) $this->getHospitalSetting('sticker_height_mm', '45'),
            'mrnFormat' => (string) $this->getHospitalSetting('mrn_format', 'MRN-{sequence:05}'),
            'routes' => $this->routes,
        ]);
    }

    public function update(Request $request)
    {
        abort_if(!$this->hospital_id, 404, 'Hospital not found.');

        $hospital = Hospital::findOrFail($this->hospital_id);

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', Rule::unique('hospitals', 'name')->ignore($hospital->id)],
            'code' => ['required', 'string', 'max:255', Rule::unique('hospitals', 'code')->ignore($hospital->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('hospitals', 'email')->ignore($hospital->id)],
            'phone' => ['required', 'string', 'max:20', Rule::unique('hospitals', 'phone')->ignore($hospital->id)],
            'city' => ['required', 'string', 'max:255'],
            'pincode' => ['required', 'string', 'max:20'],
            'landmark' => ['nullable', 'string', 'max:500'],
            'address' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'sticker_width_mm' => ['required', 'numeric', 'min:20', 'max:150'],
            'sticker_height_mm' => ['required', 'numeric', 'min:15', 'max:120'],
            'mrn_format' => ['required', 'string', 'max:120'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        try {
            if ($request->hasFile('image')) {
                if ($hospital->image) {
                    Storage::disk('public')->delete($hospital->image);
                }

                $hospital->image = $request->file('image')->store('hospital-logos', 'public');
            }

            $hospital->name = $request->name;
            $hospital->code = $request->code;
            $hospital->email = $request->email;
            $hospital->phone = $request->phone;
            $hospital->city = $request->city;
            $hospital->pincode = $request->pincode;
            $hospital->landmark = $request->landmark;
            $hospital->address = $request->address;
            $hospital->save();

            $this->setHospitalSetting('sticker_width_mm', (string) $request->sticker_width_mm);
            $this->setHospitalSetting('sticker_height_mm', (string) $request->sticker_height_mm);
            $this->setHospitalSetting('mrn_format', trim((string) $request->mrn_format));

            return response()->json([
                'status' => true,
                'message' => 'Hospital basic information updated successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while updating hospital data.',
            ], 500);
        }
    }

    private function hospitalSettingKey(string $key): string
    {
        return "hospital_{$this->hospital_id}_{$key}";
    }

    private function getHospitalSetting(string $key, ?string $default = null): ?string
    {
        $value = BusinessSetting::where('key', $this->hospitalSettingKey($key))->value('value');
        return $value !== null ? (string) $value : $default;
    }

    private function setHospitalSetting(string $key, string $value): void
    {
        BusinessSetting::updateOrCreate(
            ['key' => $this->hospitalSettingKey($key)],
            ['value' => $value]
        );
    }
}