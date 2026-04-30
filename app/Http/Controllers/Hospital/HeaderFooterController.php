<?php

namespace App\Http\Controllers\Hospital;

use App\CentralLogics\Helpers;
use App\Http\Controllers\BaseHospitalController;
use App\Models\HeaderFooter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class HeaderFooterController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();

        $this->middleware('permission:delete-header-footer', ['only' => ['destroy']]);

        $this->routes = [
            'destroy' => route('hospital.settings.header-footer.destroy', ['header_footer' => '__HEADER_FOOTER__']),
            'store' => route('hospital.settings.header-footer.store'),
            'loadtable' => route('hospital.settings.header-footer.load'),
            'showform' => route('hospital.settings.header-footer.showform'),
        ];
    }

    public function index(Request $request)
    {
        $types = HeaderFooter::getTypes();
        $selectedType = $this->resolveType($request->get('type'));

        return view('hospital.settings.header-footer.index', [
            'pathurl' => 'header-footer',
            'routes' => $this->routes,
            'types' => $types,
            'selectedType' => $selectedType,
            'selectedTypeLabel' => $types[$selectedType],
        ]);
    }

    public function loaddata(Request $request)
    {
        $selectedType = $this->resolveType($request->get('type'));

        $data = HeaderFooter::query()
            ->where('type', $selectedType)
            ->select('*');

        return DataTables::of($data)
            ->addColumn('header_preview', function ($row) {
                if (!$row->header_image) {
                    return '<span class="text-muted">No header image</span>';
                }

                $imageUrl = asset('public/storage/' . $row->header_image);

                return '<img src="' . $imageUrl . '" alt="' . e($row->type_label) . '" class="img-fluid rounded border" style="max-height: 72px; max-width: 320px; object-fit: contain;">';
            })
            ->addColumn('footer_preview', function ($row) {
                if (!$row->footer_text) {
                    return '<span class="text-muted">No footer text</span>';
                }

                return nl2br(e(Str::limit(trim($row->footer_text), 180)));
            })
            ->editColumn('updated_at', function ($row) {
                return $row->updated_at ? $row->updated_at->format('d M Y h:i A') : '-';
            })
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.header-footer.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['header_preview', 'footer_preview', 'actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $selectedType = $this->resolveType($request->get('type'));
        $data = null;

        if ($request->filled('id')) {
            $data = HeaderFooter::where('id', $request->id)
                ->firstOrFail();
            $selectedType = $data->type;
        } else {
            $data = HeaderFooter::where('type', $selectedType)
                ->where('type', $selectedType)
                ->first();
        }

        return view('hospital.settings.header-footer.form', [
            'data' => $data,
            'id' => $data?->id,
            'type' => $selectedType,
            'typeLabel' => HeaderFooter::getTypes()[$selectedType],
        ]);
    }

    public function store(Request $request)
    {
        $record = null;

        if ($request->filled('id')) {
            $record = HeaderFooter::where('id', $request->id)
                ->first();

            if (!$record) {
                return response()->json(['status' => false, 'message' => 'Record not found.'], 404);
            }
        }

        $validator = Validator::make($request->all(), [
            'type' => ['required', Rule::in(array_keys(HeaderFooter::getTypes()))],
            'header_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'footer_text' => 'nullable|string|max:5000',
            'remove_header_image' => 'nullable|boolean',
        ]);

        $validator->after(function ($validator) use ($request, $record) {
            $existingRecord = $record ?: HeaderFooter::where('hospital_id', $this->hospital_id)
                ->where('type', $request->type)
                ->first();

            $hasExistingImage = !empty($existingRecord?->header_image);
            $isRemovingExistingImage = (bool) $request->boolean('remove_header_image');
            $hasNewImage = $request->hasFile('header_image');
            $hasFooter = filled(trim((string) $request->footer_text));

            if (!$hasNewImage && (!$hasExistingImage || $isRemovingExistingImage) && !$hasFooter) {
                $validator->errors()->add('footer_text', 'Please add a header image or footer text.');
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        try {
            $headerFooter = $record ?: HeaderFooter::where('type', $request->type)
                ->first();

            $isUpdate = (bool) $headerFooter;

            if ($isUpdate && !auth()->user()->can('edit-header-footer')) {
                return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
            }

            if (!$isUpdate && !auth()->user()->can('create-header-footer')) {
                return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
            }

            if (!$headerFooter) {
                $headerFooter = new HeaderFooter();
                $headerFooter->hospital_id = $this->hospital_id;
                $headerFooter->type = $request->type;
            }

            if ($request->hasFile('header_image')) {
                if ($headerFooter->header_image) {
                    Storage::disk('public')->delete($headerFooter->header_image);
                }

                $headerFooter->header_image = $request->file('header_image')->store('header-footers', 'public');
            } elseif ($request->boolean('remove_header_image') && $headerFooter->header_image) {
                Storage::disk('public')->delete($headerFooter->header_image);
                $headerFooter->header_image = null;
            }

            $headerFooter->footer_text = $request->footer_text;
            $headerFooter->save();

            $message = $headerFooter->wasRecentlyCreated
                ? 'Header footer saved successfully.'
                : 'Header footer updated successfully.';

            return response()->json(['status' => true, 'message' => $message]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An error occurred while saving the data.'], 500);
        }
    }

    public function destroy(HeaderFooter $header_footer)
    {
        try {
            if ($header_footer->hospital_id != $this->hospital_id) {
                return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
            }

            if ($header_footer->header_image) {
                Storage::disk('public')->delete($header_footer->header_image);
            }

            $header_footer->delete();

            return response()->json(['status' => true, 'message' => 'Header footer deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An error occurred while deleting the data.'], 500);
        }
    }

    private function resolveType(?string $type): string
    {
        $types = HeaderFooter::getTypes();

        if ($type && array_key_exists($type, $types)) {
            return $type;
        }

        return array_key_first($types);
    }
}