<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{
    Stratification,
    StratificationCategory,
    Procedure,
};
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Validator;
use League\Csv\Reader;

class StratificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stratifications=Stratification::latest()->get();
        return view('admin-views.stratification.index',compact('stratifications'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories=StratificationCategory::get();
        $procedures=Procedure::get();
        return view('admin-views.stratification.create',compact('categories','procedures'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'stratification_category_id' => 'required',
            'rule' => 'required',
            'code' => 'required',
            'code2' => 'required',
            'procedure_id' => 'required|array',
            'price' => 'required',
        ]);

        $stratification = new Stratification;
        $stratification->stratification_category_id = $request->stratification_category_id;
        $stratification->name = $request->name;
        $stratification->rule = $request->rule;
        $stratification->code = $request->code;
        $stratification->code2 = $request->code2;
        $stratification->price = $request->price;
        $stratification->procedure_id = implode(",",$request->procedure_id);
        $stratification->save();
        
        return response()->json(['success' => true, 'message' => 'Stratification Saved Successfully!']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Stratification $stratification)
    {
        return response()->json(['data'=>$stratification], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Stratification $stratification)
    {
        $categories=StratificationCategory::get();
        $procedures=Procedure::get();
        return view('admin-views.stratification.edit',compact('stratification','categories','procedures'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Stratification $stratification)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'stratification_category_id' => 'required',
            'rule' => 'required',
            'code' => 'required',
            'code2' => 'required',
            'procedure_id' => 'required|array',
            'price' => 'required',
        ]);

        $stratification->stratification_category_id = $request->stratification_category_id;
        $stratification->name = $request->name;
        $stratification->rule = $request->rule;
        $stratification->code = $request->code;
        $stratification->code2 = $request->code2;
        $stratification->price = $request->price;
        $stratification->procedure_id = implode(",",$request->procedure_id);
        $stratification->save();
        
        return response()->json(['success' => true, 'message' => 'Stratification Updated Successfully!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stratification $stratification)
    {
        $stratification->delete();
        return redirect()->back()->with('success','Stratification Deleted.');
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv'
        ]);

        $file = $request->file('file');

        $csv = Reader::createFromPath($file->getPathname(), 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $row) {
            if (!empty($row['Name'])) {
                $code = mb_convert_encoding($row['Code'], 'UTF-8', 'ISO-8859-1');
                $rule = mb_convert_encoding($row['Rule'], 'UTF-8', 'ISO-8859-1');
                $code2 = mb_convert_encoding($row['Code2'], 'UTF-8', 'ISO-8859-1');
                $category = mb_convert_encoding($row['Category'] ?? '', 'UTF-8', 'ISO-8859-1');
                $category_id = StratificationCategory::where('name', $category)->value('id');
                $name = mb_convert_encoding($row['Name'] ?? '', 'UTF-8', 'ISO-8859-1');
                $price = mb_convert_encoding($row['Price'] ?? 0, 'UTF-8', 'ISO-8859-1');

                $price = str_replace(array(',','"+"','None'), '', $price);
                $price = is_numeric($price) ? (float)$price : 0;

                Stratification::updateOrInsert(
                    ['code' => $code,'rule'=>$rule,'code2' => $code2,'stratification_category_id' => $category_id,'name' => $name],
                    [
                        'code' => $code,
                        'rule' => $rule,
                        'code2' => $code2,
                        'stratification_category_id' => $category_id,
                        'name' => $name,
                        'price' => $price,
                    ]
                );
            }
        }

        return back()->with('success', 'Data imported successfully!');
    }
    public function mapProcedure(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv'
        ]);

        $file = $request->file('file');

        $csv = Reader::createFromPath($file->getPathname(), 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $row) {
            if (!empty($row['Stratification Code']) && !empty($row['Procedure Code'])) {
                $stratification_code = trim(mb_convert_encoding($row['Stratification Code'], 'UTF-8', 'ISO-8859-1'));
                $procedure = trim(mb_convert_encoding($row['Procedure Code'], 'UTF-8', 'ISO-8859-1'));
        
                $stratifications = Stratification::where('code', $stratification_code)->get();
                $procedures = Procedure::where('procedure_code_2', $procedure)->get();

                if ($stratifications->count() > 0 && $procedures->count() > 0) {
                    foreach ($stratifications as $stratification) {
                        if (!empty($stratification->procedure_id)) {
                            $procedure_arr = explode(",", $stratification->procedure_id);
                        } else {
                            $procedure_arr = [];
                        }

                        foreach ($procedures as $proc) {
                            $procedure_arr[] = $proc->id;
                        }

                        $stratification->procedure_id = implode(",", array_unique($procedure_arr));
                        $stratification->save();
                    }
                }

            }
        }

        return back()->with('success', 'Data imported successfully!');
    }
}
