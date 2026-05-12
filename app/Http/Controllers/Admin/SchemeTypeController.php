<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\SchemeType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class SchemeTypeController extends Controller
{
    public $routes = [];

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (! auth()->user() || ! auth()->user()->hasRole('Master Admin')) {
                abort(403, 'Unauthorized');
            }

            return $next($request);
        });

        $this->routes = [
            'destroy' => route('admin.scheme-types.destroy', ['scheme_type' => '__SCHEME_TYPE__']),
            'store' => route('admin.scheme-types.store'),
            'loadtable' => route('admin.scheme-types-load'),
            'showform' => route('admin.scheme-types.showform'),
        ];
    }

    public function index()
    {
        return view('admin-views.scheme-type.index', ['pathurl' => 'scheme-type', 'routes' => $this->routes]);
    }

    public function loaddata(Request $request)
    {
        $data = SchemeType::query()->select('*')->orderByDesc('id');

        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('admin-views.scheme-type.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:scheme_types,name,'.$request->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        SchemeType::updateOrCreate(
            ['id' => $request->id],
            ['name' => $request->name]
        );

        $msg = $request->id ? 'Scheme type updated successfully.' : 'Scheme type created successfully.';

        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = null;
        if ($id) {
            $data = SchemeType::where('id', $id)->first();
        }

        return view('admin-views.scheme-type.form', compact('data', 'id'));
    }

    public function destroy(SchemeType $scheme_type)
    {
        $scheme_type->delete();

        return response()->json(['status' => true, 'message' => 'Scheme type deleted successfully.']);
    }
}
