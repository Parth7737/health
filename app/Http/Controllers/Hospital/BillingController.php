<?php

namespace App\Http\Controllers\Hospital;

use App\CentralLogics\Helpers;
use App\Http\Controllers\BaseHospitalController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BillingController extends BaseHospitalController
{
    public function index()
    {
        $data = [];
        $data['title'] = 'Billing & Finance';
        return view('hospital.billing.index', compact('data'));
    }
}
