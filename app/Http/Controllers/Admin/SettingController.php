<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use League\Csv\Reader;
use Yajra\DataTables\Facades\DataTables;
use App\Models\BusinessSetting;

class SettingController extends Controller
{
    public $routes = [];

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user() || !auth()->user()->hasRole('Master Admin')) {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        });

        $this->routes = [          
            'store'   => route('admin.settings.store'),   
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin-views.settings.index', ['pathurl' => 'settings', 'routes' => $this->routes]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'front_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'registration_fee' => 'nullable',
            'site_title' => 'nullable',
            'ccavenue_status' => 'nullable|in:0,1',
            'client_id' => 'nullable|string|max:255',
            'secret_id' => 'nullable|string|max:255',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        if ($request->hasFile('background_image')) {
            $filePath = $request->file('background_image')->store('logo', 'public'); 
            $this->updateSetting('background_image', $filePath);
        }  

        if ($request->hasFile('front_logo')) {
            $frontLogoPath = $request->file('front_logo')->store('logo', 'public'); 
            $this->updateSetting('front_logo', $frontLogoPath);
        }  

        $this->updateSetting('site_title', $request->site_title);
        $this->updateSetting('registration_fee', $request->registration_fee);

        $ccavenueData = [
            'status'     => $request->ccavenue_status ?? 0,
            'client_id'  => $request->client_id,
            'secret_id'  => $request->secret_id,
        ];

        $this->updateSetting('ccavenue', json_encode($ccavenueData));

        $empanelment_step_status = [
            'speciality_status'     => $request->speciality_status ?? 0,
            'service_status'     => $request->service_status ?? 0,
            'licenses_status'     => $request->licenses_status ?? 0,
        ];

        $this->updateSetting('empanelment_step_status', json_encode($empanelment_step_status));
        
        $msg = 'Setting saved successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function updateSetting($key, $value)
    {
        BusinessSetting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
