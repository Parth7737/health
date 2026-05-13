<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{
    Procedure,
    Speciality,
    Package,
    SchemeType,
    ProcedureCategory,
    Investigation,
};
use Illuminate\Http\Request;
use App\Imports\ProceduresImport;
use Illuminate\Support\Facades\DB;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Validator;
use League\Csv\Reader;
use Illuminate\Support\Str;

class ProcedureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $procedures=[];
        $specialities = Speciality::all();
        $packages = Package::all();
        
        if ($request->ajax()) {
            $columns = ['id', 'scheme_id', 'procedure_category_id', 'procedure_name', 'procedure_code_2'];

            $query = Procedure::with(['scheme', 'procedure_category']);

            // Search filter
            if (!empty($request->search['value'])) {
                $search = $request->search['value'];
                $query->where('procedure_name', 'LIKE', "%{$search}%")
                    ->orWhereHas('scheme', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('procedure_category', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    });
            }

            // Order By
            if (isset($request->order)) {
                $columnIndex = $request->order[0]['column'];
                $columnName = $columns[$columnIndex];
                $columnSortOrder = $request->order[0]['dir'];
                $query->orderBy($columnName, $columnSortOrder);
            } else {
                $query->orderBy('id', 'desc');
            }

            // Pagination
            $totalRecords = $query->count();
            $procedures = $query->offset($request->start)
                ->limit($request->length)
                ->get();

            // Format response for DataTables
            return response()->json([
                "draw" => intval($request->draw),
                "recordsTotal" => $totalRecords,
                "recordsFiltered" => $totalRecords,
                "data" => $procedures->map(function ($procedure, $index) use ($request) {
                    $fullText = $procedure->procedure_name;
                    $truncatedText = Str::words($fullText, 5, '...');
                    return [
                        'id' => $request->start + $index + 1,
                        'scheme' => $procedure->scheme->name ?? '-',
                        'category' => $procedure->procedure_category->name ?? '-',
                        'procedure_name' => view('admin-views.procedure.partial-procedure-name', compact('fullText', 'truncatedText'))->render(),
                        'procedure_code_2' => $procedure->procedure_code_2,
                        'actions' => view('admin-views.procedure.partial-actions', compact('procedure'))->render(),
                    ];
                })
            ]);
        }
        return view('admin-views.procedure.index',compact('procedures', 'specialities', 'packages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $specialities=Speciality::get();
        $packages=Package::get();
        $investigations=Investigation::get();
        return view('admin-views.procedure.create',compact('specialities','packages','investigations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'scheme_type_id' => 'required',
            'procedure_category_id' => 'required_if:scheme_type_id,1',
            'speciality_id' => 'required',
            'package_id' => 'required_if:scheme_type_id,2',
            'procedure_code_1' => 'required',
            'procedure_code_2' => 'required',
            'is_multiple_procedure' => 'required',
            'procedure_name' => 'required',
            'procedure_type' => 'required',
            'price' => 'required',
            'stratification_criteria' => 'required',
            'no_of_stratification' => 'required_if:stratification_criteria,Yes',
            'implants_high_end_consumables' => 'required',
            'more_than_one_implant' => 'required_if:implants_high_end_consumables,Yes',
            'special_conditions' => 'required',
            'reservation_public_hospitals' => 'required',
            'reservation_tertiary_hospitals' => 'required',
            'level_of_care' => 'required',
            'los' => 'required',
            'auto_approved' => 'required',
            'mandatory_documents_pre_auth' => 'required|array',
            'mandatory_documents_claim_processing' => 'required|array',
            'procedure_label' => 'required',
            'special_condition_pop_up' => 'required',
            'special_condition_pop_up_message' => 'required_if:special_condition_pop_up,Yes',
            'special_conditions_rule' => 'required',
            'special_conditions_rule_message' => 'required_if:special_conditions_rule,Yes',
            'enhancement_applicable' => 'required',
            'medical_or_surgical' => 'required',
            'day_care_procedure' => 'required',
        ]);

        $procedure = new Procedure;
        $procedure->scheme_type_id = $request->scheme_type_id;
        $procedure->procedure_category_id = $request->procedure_category_id;
        $procedure->speciality_id = $request->speciality_id;
        $procedure->package_id = $request->package_id;
        $procedure->procedure_code_1 = $request->procedure_code_1;
        $procedure->procedure_code_2 = $request->procedure_code_2;
        $procedure->is_multiple_procedure = $request->is_multiple_procedure;
        $procedure->icd_code = $request->icd_code;
        $procedure->procedure_name = $request->procedure_name;
        $procedure->procedure_type = $request->procedure_type;
        $procedure->price = $request->price;
        $procedure->stratification_criteria = $request->stratification_criteria;
        $procedure->no_of_stratification = $request->no_of_stratification??0;
        $procedure->implants_high_end_consumables = $request->implants_high_end_consumables;
        $procedure->no_of_stratification = $request->no_of_stratification??0;
        $procedure->special_conditions = $request->special_conditions??0;
        $procedure->reservation_public_hospitals = $request->reservation_public_hospitals;
        $procedure->reservation_tertiary_hospitals = $request->reservation_tertiary_hospitals;
        $procedure->level_of_care = $request->level_of_care;
        $procedure->los = $request->los;
        $procedure->auto_approved = $request->auto_approved;
        $procedure->mandatory_documents_pre_auth = implode(",",$request->mandatory_documents_pre_auth);
        $procedure->mandatory_documents_claim_processing = implode(",",$request->mandatory_documents_claim_processing);
        $procedure->procedure_label = $request->procedure_label;
        $procedure->special_condition_pop_up = $request->special_condition_pop_up;
        $procedure->special_condition_pop_up_message = $request->special_condition_pop_up_message;
        $procedure->special_conditions_rule = $request->special_conditions_rule;
        $procedure->special_conditions_rule_message = $request->special_conditions_rule_message;
        $procedure->enhancement_applicable = $request->enhancement_applicable;
        $procedure->medical_or_surgical = $request->medical_or_surgical;
        $procedure->day_care_procedure = $request->day_care_procedure;
        $procedure->save();
        
        return response()->json(['success' => true, 'message' => 'Procedure Saved Successfully!']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Procedure $procedure)
    {
        return response()->json(['data'=>$procedure], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Procedure $procedure)
    {
        $specialities=Speciality::get();
        $packages=Package::get();
        $investigations=Investigation::get();
        return view('admin-views.procedure.edit',compact('procedure','specialities','packages','investigations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Procedure $procedure)
    {
        $validatedData = $request->validate([
            'scheme_type_id' => 'required',
            'speciality_id' => 'required',
            'procedure_category_id' => 'required_if:scheme_type_id,1',
            'package_id' => 'required_if:scheme_type_id,2',
            'procedure_code_1' => 'required',
            'procedure_code_2' => 'required',
            'is_multiple_procedure' => 'required',
            'procedure_name' => 'required',
            'procedure_type' => 'required',
            'price' => 'required',
            'stratification_criteria' => 'required',
            'no_of_stratification' => 'required_if:stratification_criteria,Yes',
            'implants_high_end_consumables' => 'required',
            'more_than_one_implant' => 'required_if:implants_high_end_consumables,Yes',
            'special_conditions' => 'required',
            'reservation_public_hospitals' => 'required',
            'reservation_tertiary_hospitals' => 'required',
            'level_of_care' => 'required',
            'los' => 'required',
            'auto_approved' => 'required',
            'mandatory_documents_pre_auth' => 'required|array',
            'mandatory_documents_claim_processing' => 'required|array',
            'procedure_label' => 'required',
            'special_condition_pop_up' => 'required',
            'special_condition_pop_up_message' => 'required_if:special_condition_pop_up,Yes',
            'special_conditions_rule' => 'required',
            'special_conditions_rule_message' => 'required_if:special_conditions_rule,Yes',
            'enhancement_applicable' => 'required',
            'medical_or_surgical' => 'required',
            'day_care_procedure' => 'required',
        ]);

        $procedure->scheme_type_id = $request->scheme_type_id;
        $procedure->procedure_category_id = $request->procedure_category_id;
        $procedure->speciality_id = $request->speciality_id;
        $procedure->package_id = $request->package_id;
        $procedure->procedure_code_1 = $request->procedure_code_1;
        $procedure->procedure_code_2 = $request->procedure_code_2;
        $procedure->is_multiple_procedure = $request->is_multiple_procedure;
        $procedure->icd_code = $request->icd_code;
        $procedure->procedure_name = $request->procedure_name;
        $procedure->procedure_type = $request->procedure_type;
        $procedure->price = $request->price;
        $procedure->stratification_criteria = $request->stratification_criteria;
        $procedure->no_of_stratification = $request->no_of_stratification??0;
        $procedure->implants_high_end_consumables = $request->implants_high_end_consumables;
        $procedure->no_of_stratification = $request->no_of_stratification??0;
        $procedure->special_conditions = $request->special_conditions??0;
        $procedure->reservation_public_hospitals = $request->reservation_public_hospitals;
        $procedure->reservation_tertiary_hospitals = $request->reservation_tertiary_hospitals;
        $procedure->level_of_care = $request->level_of_care;
        $procedure->los = $request->los;
        $procedure->auto_approved = $request->auto_approved;
        $procedure->mandatory_documents_pre_auth = implode(",",$request->mandatory_documents_pre_auth);
        $procedure->mandatory_documents_claim_processing = implode(",",$request->mandatory_documents_claim_processing);
        $procedure->procedure_label = $request->procedure_label;
        $procedure->special_condition_pop_up = $request->special_condition_pop_up;
        $procedure->special_condition_pop_up_message = $request->special_condition_pop_up_message;
        $procedure->special_conditions_rule = $request->special_conditions_rule;
        $procedure->special_conditions_rule_message = $request->special_conditions_rule_message;
        $procedure->enhancement_applicable = $request->enhancement_applicable;
        $procedure->medical_or_surgical = $request->medical_or_surgical;
        $procedure->day_care_procedure = $request->day_care_procedure;
        $procedure->save();
        
        return response()->json(['success' => true, 'message' => 'Procedure Updaated Successfully!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Procedure $procedure)
    {
        $procedure->delete();
        return redirect()->back()->with('success','Procedure Deleted.');
    }

    public function import(Request $request)
    {
        // $speciality_id = $request->speciality_id;
        // $package_id = $request->package_id;
        $file = $request->file('procedure_file');

        // Read CSV file
        $csv = Reader::createFromPath($file->getPathname(), 'r');
        $csv->setHeaderOffset(0);
        
        $data = [];

        foreach ($csv as $row) {
            if (!empty($row['name'])) {
                $name = trim(mb_convert_encoding($row['name'], 'UTF-8', 'ISO-8859-1'));
                $package_id = Package::where('code',$row['package_code'])->value('id');
                $scheme_type_id = SchemeType::where('name',$row['scheme_type'])->value('id');
                $procedure_category_id = ProcedureCategory::where('name',$row['category'])->value('id');
                $speciality_id = Speciality::where('name', $row['speciality'])->where('scheme_type_id',$scheme_type_id)->value('id');
                $price = mb_convert_encoding($row['price'] ?? 0, 'UTF-8', 'ISO-8859-1');

                $price = str_replace(array(',',' '), '', $price);
                $price = is_numeric($price) ? (float)$price : 0;

                $non_nabh_price = mb_convert_encoding($row['non_nabh_price'] ?? 0, 'UTF-8', 'ISO-8859-1');

                $non_nabh_price = str_replace(array(',',' '), '', $non_nabh_price);
                $non_nabh_price = is_numeric($non_nabh_price) ? (float)$non_nabh_price : 0;
                Procedure::updateOrInsert(
                    ['procedure_code_2' => $row['procedure_code_2'],'scheme_type_id' => $scheme_type_id,'procedure_category_id' => $procedure_category_id,'speciality_id' => $speciality_id],
                    [
                        'package_id' => $package_id??'',
                        'scheme_type_id' => $scheme_type_id??'',
                        'procedure_category_id' => $procedure_category_id??'',
                        'procedure_code_1' => $row['procedure_code_1'] ?? null,
                        'speciality_id' => $speciality_id,
                        'procedure_code_2' => $row['procedure_code_2'] ?? null,
                        'is_multiple_procedure' => $row['is_multiple_procedure'] ?? 0,
                        'procedure_name' => $name,
                        'procedure_type' => $row['procedure_type'] ?? 0,
                        'icd_code' => $row['icd_code'] ?? null,
                        'price' => $price ?? 0,
                        'non_nabh_price' => $non_nabh_price ?? 0,
                        'stratification_criteria' => $row['stratification_criteria'] ?? null,
                        'no_of_stratification' => $row['no_of_stratification'] ?? null,
                        'implants_high_end_consumables' => $row['implants_high_end_consumables'] ?? null,
                        'more_than_one_implant' => $row['more_than_one_implant'] ?? null,
                        'special_conditions' => $row['special_conditions'] ?? null,
                        'reservation_public_hospitals' => $row['reservation_public_hospitals'] ?? null,
                        'reservation_tertiary_hospitals' => $row['reservation_tertiary_hospitals'] ?? null,
                        'level_of_care' => $row['level_of_care'] ?? null,
                        'los' => $row['los'] ?? null,
                        'auto_approved' => $row['auto_approved'] ?? 0,
                        'procedure_label' => $row['procedure_label'] ?? null,
                        'special_condition_pop_up' => $row['special_condition_pop_up'] ?? null,
                        // 'special_condition_pop_up_message' => $row['special_condition_pop_up_message'] ?? null,
                        'special_conditions_rule' => $row['special_conditions_rule'] ?? null,
                        // 'special_conditions_rule_message' => $row['special_conditions_rule_message'] ?? null,
                        'enhancement_applicable' => $row['enhancement_applicable'] ?? null,
                        'medical_or_surgical' => $row['medical_or_surgical'] ?? null,
                        'day_care_procedure' => $row['day_care_procedure'] ?? null,
                        'status' => 1,
                        // 'created_at' => now(),
                        // 'updated_at' => now(),
                    ]
                );
            }
        }
        // dd($data);

        // if (!empty($data)) {
        //     DB::table('procedures')->insert($data);
        // }

        return back()->with('success', 'Procedures imported successfully!');
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
            if (!empty($row['Investigation Code']) && !empty($row['Procedure Code']) && !empty($row['Type'])) {
                $investigation_code = trim(mb_convert_encoding($row['Investigation Code'], 'UTF-8', 'ISO-8859-1'));
                $procedure = trim(mb_convert_encoding($row['Procedure Code'], 'UTF-8', 'ISO-8859-1'));
                $type = trim(mb_convert_encoding($row['Type'], 'UTF-8', 'ISO-8859-1'));
        
                $procedures = Procedure::where('procedure_code_2', $procedure)->get();
                $investigation_id = Investigation::where('code', $investigation_code)->value('id');
        
                if ($procedures->count() > 0 && !is_null($investigation_id)) {
                    foreach($procedures as $procedure){
                        if($type == 'Pre'){
                            if (!empty($procedure->mandatory_documents_pre_auth)) {
                                $mandatory_documents_pre_auth_arr = explode(",", $procedure->mandatory_documents_pre_auth);
                                $mandatory_documents_pre_auth_arr[] = $investigation_id;
                                $procedure->mandatory_documents_pre_auth = implode(",", array_unique($mandatory_documents_pre_auth_arr));
                            } else {
                                $procedure->mandatory_documents_pre_auth = $investigation_id;
                            }
                            $procedure->save();
                        }elseif($type == 'Post'){
                            if (!empty($procedure->mandatory_documents_claim_processing)) {
                                $mandatory_documents_claim_processing_arr = explode(",", $procedure->mandatory_documents_claim_processing);
                                $mandatory_documents_claim_processing_arr[] = $investigation_id;
                                $procedure->mandatory_documents_claim_processing = implode(",", array_unique($mandatory_documents_claim_processing_arr));
                            } else {
                                $procedure->mandatory_documents_claim_processing = $investigation_id;
                            }
                            $procedure->save();
                        }
                    }
                }
            }
        }

        return back()->with('success', 'Data imported successfully!');
    }
    public function getSpecialities(Request $request){
        $specialities = Speciality::where('scheme_type_id',$request->scheme_type_id)->get();
        return response()->json(['specialities'=>$specialities]);
    }
    public function mapInvestigationManual()
    {
        $pre_investigations = array_unique(
            Investigation::where('scheme_type_id', 1)->where('type', 'Pre')->pluck('id')->toArray()
        );

        $post_investigations = array_unique(
            Investigation::where('scheme_type_id', 1)->where('type', 'Post')->pluck('id')->toArray()
        );

        Procedure::where('scheme_type_id', 1)->update([
            'mandatory_documents_pre_auth' => implode(",", $pre_investigations),
            'mandatory_documents_claim_processing' => implode(",", $post_investigations),
        ]);
        return redirect()->route('admin.procedure.index')->with('success', 'Investigation Mapped Successfully!');
    }

}
