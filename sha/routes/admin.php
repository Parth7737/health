<?php
use Illuminate\Support\Facades\Route;
Route::group(['middleware'=>'superadmin','namespace' => 'App\Http\Controllers\Admin', 'prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::resource('speciality', 'SpecialityController');
    Route::resource('package', 'PackageController');
    // Route::resource('document', 'DocumentController');
    Route::resource('SchemeType', 'SchemeTypeController');
    Route::resource('hospital-states', 'HospitalStateController');
    Route::resource('hospitalDistrict', 'HospitalDistrictController');
    Route::resource('diabetes', 'DiabetesController');
    Route::resource('hypertension', 'HypertensionController');
    Route::resource('heart_diseases', 'HeartDiseaseController');
    Route::resource('strokes', 'StrokeController');
    Route::resource('cancers', 'CancerController');
    Route::resource('tuberculosis', 'TuberculosisController');
    Route::resource('asthmas', 'AsthmaController');
    Route::resource('appetites', 'AppetiteController');
    Route::resource('bowels', 'BowelController');
    Route::resource('nutrition', 'NutritionController');
    Route::resource('diets', 'DietController');
    Route::resource('admission-types', 'AdmissionTypeController');
    Route::resource('diagnoses', 'DiagnosesController');
    Route::resource('roles', 'RoleController');
    Route::resource('villages', 'VillageController');
    Route::resource('accreditations', 'AccreditationController');
    Route::resource('tds_exemptions', 'TdsExemptionController');
    Route::resource('empanelment-documents', 'EmpanelmentDocumentController');
    Route::resource('facility-details', 'FacilityDetailController');
    Route::resource('facility-types', 'FacilityTypeController');
    Route::resource('facility-speciality-types', 'FacilitySpecialityTypeController');
    Route::resource('facility-ownership-types', 'FacilityOwnershipTypeController');
    Route::resource('facility_ownership_sub_types', 'FacilityOwnershipSubTypeController');
    Route::resource('facility-certificates', 'FacilityRegistrationCertificateController');
    Route::resource('goverment-benefits', 'GovermentBenefitController');
    Route::resource('system_medicines', 'SystemMedicineController');
    Route::resource('entityTypes', 'EntityTypeController');
    Route::resource('entities', 'EntityController');
    Route::resource('registration_cancel_reasons', 'RegistrationCancelReasonController');
    Route::resource('preauth_cancel_reasons', 'PreauthCancelReasonController');
    Route::resource('preauth-reject-reasons', 'PreauthRejectReasonController');
    Route::resource('licenses', 'LicensesController');
    Route::resource('licenses_type', 'LicenseTypeController');
    Route::resource('human_resources', 'HumanResourceController');
    Route::resource('preauth_claim_reasons', 'PreauthClaimReasonController');
    Route::resource('beneficiaries', 'BeneficiaryController');

    Route::resource('document', 'InvestigationController');
    Route::resource('procedure', 'ProcedureController');
    Route::get('/procedures', 'ProcedureController@index')->name('procedures.index');
    Route::resource('procedure-category', 'ProcedureCategoryController');
    Route::resource('implant', 'ImplantController');
    Route::resource('stratification', 'StratificationController');
    Route::resource('stratification-category', 'StratificationCategoryController');
    Route::resource('followup', 'FollowUpController');
    Route::resource('nonaddon', 'NonAddOnController');
    Route::resource('addon', 'AddOnController');
    Route::resource('addon-speciality', 'AddOnSpecialityController');
    Route::resource('service', 'ServiceController');
    Route::resource('sub-service', 'SubServiceController');
    Route::resource('bank-accounts', 'BankAccountController');
    Route::resource('audit-category', 'AuditCategoryController');
    Route::resource('audit-sub-category', 'AuditSubCategoryController');
    Route::resource('audit-list', 'AuditListController');
    Route::get('/getauditsubcategory/{id}', 'AuditListController@getauditsubcategory')->name('getauditsubcategory');
    
    Route::resource('blocks', 'BlockController');
    Route::post('/block-import', 'BlockController@import')->name('block.import');
    Route::post('/village-import', 'VillageController@import')->name('village.import');
    
    Route::get('getDistrict/{id}', 'BlockController@getDistrict')->name('getDistrict');
    Route::get('getblocks/{id}', 'BlockController@getblocks')->name('getblocks');
    
    
    Route::post('/facility_ownership_sub_types2', 'FacilityOwnershipSubTypeController@subtype2')->name('subtype2.store');
    Route::put('/facility_ownership_sub_types2/{id}', 'FacilityOwnershipSubTypeController@subtype2edit')->name('subtype2edit');
    Route::get('/getsubtypes/{id}', 'FacilityOwnershipSubTypeController@getsubtypes')->name('getsubtypes');
    Route::get('/getsubtypes2/{id}', 'FacilityOwnershipSubTypeController@getsubtypes2')->name('getsubtypes2');
    
    Route::post('/facility_ownership_sub_types3', 'FacilityOwnershipSubTypeController@subtype3')->name('subtype3.store');
    Route::put('/facility_ownership_sub_types3/{id}', 'FacilityOwnershipSubTypeController@subtype3edit')->name('subtype3edit');
    
    Route::get('/register-requests', 'UserController@index')->name('register-requests');
    Route::post('/approve-user/{id}', 'UserController@approve')->name('users.approve');
    Route::get('/users', 'UserController@indexUser')->name('users.indexUser');
    Route::get('/users-details/{id}', 'UserController@view')->name('users.view');
    Route::get('/hospitals', 'HospitalController@index')->name('hospitals');
    Route::get('/hospitals/{id}', 'HospitalController@show')->name('hospitals.show');
    Route::get('/import', 'ExcelImportController@importView')->name('excel.import.view');
    Route::post('/import', 'ExcelImportController@import')->name('excel.import');
    Route::post('/import-with-code', 'ExcelImportController@importWithCode')->name('excel.importWithCode');

    
    Route::get('/beneficiaries-import-manual', 'BeneficiaryController@importManual');
    Route::post('/beneficiaries/import', 'BeneficiaryController@import')->name('beneficiaries.import');
    Route::get('/admin/beneficiaries/data', 'BeneficiaryController@getBeneficiariesData')->name('beneficiaries.data');

    Route::post('/implant/import', 'ImplantController@import')->name('implant.import');
    Route::post('/implant/map-procedure', 'ImplantController@mapProcedure')->name('implant.map-procedure');
    Route::post('/stratification/import', 'StratificationController@import')->name('stratification.import');
    Route::post('stratification/map-procedure', 'StratificationController@mapProcedure')->name('stratification.map-procedure');
    Route::post('/followup/import', 'FollowUpController@import')->name('followup.import');
    Route::post('/addon/import', 'AddOnController@import')->name('addon.import');
    Route::post('/addonspeciality/import', 'AddOnSpecialityController@import')->name('addon-speciality.import');
    Route::post('/non-addon/import', 'NonAddOnController@import')->name('non-addon.import');
    Route::post('/procedures/import', 'ProcedureController@import')->name('procedures.import');
    Route::get('/procedure-map-investigations', 'ProcedureController@mapInvestigationManual');
    Route::post('/procedure/get-specialities', 'ProcedureController@getSpecialities')->name('procedure.get-specialities');
    Route::post('/speciality/import', 'SpecialityController@import')->name('speciality.import');
    Route::post('/investigation/import', 'InvestigationController@import')->name('document.import');
    Route::post('investigation/map-procedure', 'ProcedureController@mapProcedure')->name('investigation.map-procedure');
    Route::post('/facilities/import', 'FacilityDetailController@import')->name('facilities.import');});
