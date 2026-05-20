<?php
use Illuminate\Support\Facades\Route;
Route::group(['namespace' => 'App\Http\Controllers\Admin', 'prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/', function() {
        return view('auth.login');
    });
    Route::get('login', function() {
        return view('auth.login');
    })->name('login');
});
Route::group(['namespace' => 'App\Http\Controllers\Admin', 'prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['superadmin']], function () {
    Route::resource('dashboard', 'DashboardController');
    Route::resource('speciality', 'SpecialityController');
    Route::resource('hospital-states', 'HospitalStateController');
    Route::resource('hospitalDistrict', 'HospitalDistrictController');

    // roles
    Route::resource('roles', 'RoleController');
    Route::post('load-roles', 'RoleController@rolesload')->name('rolesload');  
    Route::post('show-roles-forms', 'RoleController@showform')->name('roles.showform');  
    // end roles

    // Permissions
    Route::resource('permissions', 'PermissionController');
    Route::post('load-permission', 'PermissionController@permissionload')->name('permissionsload');  
    Route::post('show-permissions-forms', 'PermissionController@showform')->name('permissions.showform');  

    // Modules
    Route::resource('modules', 'ModuleController');
    Route::post('load-modules', 'ModuleController@moduleload')->name('modulesload');  
    Route::post('show-modules-forms', 'ModuleController@showform')->name('modules.showform');  

    // Hospital Types
    Route::resource('hospitaltypes', 'HospitalTypeController');
    Route::post('load-hospital-types', 'HospitalTypeController@loaddata')->name('hospitaltypesload');  
    Route::post('show-hospital-type-forms', 'HospitalTypeController@showform')->name('hospitaltypes.showform');

    // Hospital documents
    Route::resource('hospital-documents', 'EmpanelmentDocumentController');
    Route::post('load-hospital-documents', 'EmpanelmentDocumentController@loaddata')->name('hospital-documentsload');  
    Route::post('show-hospital-document-forms', 'EmpanelmentDocumentController@showform')->name('hospital-documents.showform');

    // Specialities
    Route::resource('specialities', 'SpecialityController');
    Route::post('load-specialities', 'SpecialityController@loaddata')->name('specialitiesload');  
    Route::post('show-specialities-form', 'SpecialityController@showform')->name('specialities.showform');

    // Staff Strengths
    Route::resource('staff-strengths', 'StaffStrengthController');
    Route::post('load-staff-strengths', 'StaffStrengthController@loaddata')->name('staff-strengths-load');
    Route::post('show-staff-strengths-form', 'StaffStrengthController@showform')->name('staff-strengths.showform');

    // Treatment plan masters (procedure / package related)
    Route::resource('scheme-types', 'SchemeTypeController');
    Route::post('load-scheme-types', 'SchemeTypeController@loaddata')->name('scheme-types-load');
    Route::post('show-scheme-types-form', 'SchemeTypeController@showform')->name('scheme-types.showform');

    Route::resource('packages', 'TreatmentPlanPackageController');
    Route::post('load-packages', 'TreatmentPlanPackageController@loaddata')->name('packages-load');
    Route::post('show-packages-form', 'TreatmentPlanPackageController@showform')->name('packages.showform');

    Route::resource('investigations', 'TreatmentPlanInvestigationController');
    Route::post('load-investigations', 'TreatmentPlanInvestigationController@loaddata')->name('investigations-load');
    Route::post('show-investigations-form', 'TreatmentPlanInvestigationController@showform')->name('investigations.showform');

    Route::resource('procedure-categories', 'TreatmentPlanProcedureCategoryController');
    Route::post('load-procedure-categories', 'TreatmentPlanProcedureCategoryController@loaddata')->name('procedure-categories-load');
    Route::post('show-procedure-categories-form', 'TreatmentPlanProcedureCategoryController@showform')->name('procedure-categories.showform');

    Route::resource('procedures', 'TreatmentPlanProcedureController');
    Route::post('load-procedures', 'TreatmentPlanProcedureController@loaddata')->name('procedures-load');
    Route::post('show-procedures-form', 'TreatmentPlanProcedureController@showform')->name('procedures.showform');
    Route::post('procedures/get-specialities', 'TreatmentPlanProcedureController@getSpecialitiesByScheme')->name('procedures.get-specialities');

    Route::resource('implants', 'TreatmentPlanImplantController');
    Route::post('load-implants', 'TreatmentPlanImplantController@loaddata')->name('implants-load');
    Route::post('show-implants-form', 'TreatmentPlanImplantController@showform')->name('implants.showform');

    Route::resource('stratification-categories', 'TreatmentPlanStratificationCategoryController');
    Route::post('load-stratification-categories', 'TreatmentPlanStratificationCategoryController@loaddata')->name('stratification-categories-load');
    Route::post('show-stratification-categories-form', 'TreatmentPlanStratificationCategoryController@showform')->name('stratification-categories.showform');

    Route::resource('stratifications', 'TreatmentPlanStratificationController');
    Route::post('load-stratifications', 'TreatmentPlanStratificationController@loaddata')->name('stratifications-load');
    Route::post('show-stratifications-form', 'TreatmentPlanStratificationController@showform')->name('stratifications.showform');

    Route::resource('followup-links', 'TreatmentPlanFollowupLinkController');
    Route::post('load-followup-links', 'TreatmentPlanFollowupLinkController@loaddata')->name('followup-links-load');
    Route::post('show-followup-links-form', 'TreatmentPlanFollowupLinkController@showform')->name('followup-links.showform');

    Route::resource('addon-links', 'TreatmentPlanAddonLinkController');
    Route::post('load-addon-links', 'TreatmentPlanAddonLinkController@loaddata')->name('addon-links-load');
    Route::post('show-addon-links-form', 'TreatmentPlanAddonLinkController@showform')->name('addon-links.showform');

    Route::resource('non-addon-links', 'TreatmentPlanNonAddonLinkController');
    Route::post('load-non-addon-links', 'TreatmentPlanNonAddonLinkController@loaddata')->name('non-addon-links-load');
    Route::post('show-non-addon-links-form', 'TreatmentPlanNonAddonLinkController@showform')->name('non-addon-links.showform');

    Route::resource('addon-specialities', 'TreatmentPlanAddonSpecialityController');
    Route::post('load-addon-specialities', 'TreatmentPlanAddonSpecialityController@loaddata')->name('addon-specialities-load');
    Route::post('show-addon-specialities-form', 'TreatmentPlanAddonSpecialityController@showform')->name('addon-specialities.showform');
    
    // Services
    Route::resource('services', 'ServiceController');
    Route::post('load-services', 'ServiceController@loaddata')->name('load-services');  
    Route::post('show-service-form', 'ServiceController@showform')->name('services.showform');
    
    // Sub Services
    Route::resource('sub-services', 'SubServiceController');
    Route::post('load-sub-services', 'SubServiceController@loaddata')->name('load-sub-services');  
    Route::post('show-sub-service-form', 'SubServiceController@showform')->name('sub-services.showform');
    
    // Licenses
    Route::resource('licenses', 'LicensesController');
    Route::post('load-licenses', 'LicensesController@loaddata')->name('load-licenses');  
    Route::post('show-license-form', 'LicensesController@showform')->name('licenses.showform');
    
    // Sub Services
    Route::resource('license-types', 'LicenseTypeController');
    Route::post('load-license-types', 'LicenseTypeController@loaddata')->name('load-license-types');  
    Route::post('show-license-type-form', 'LicenseTypeController@showform')->name('license-types.showform');

    // Hospital
    Route::resource('hospitals', 'HospitalController');
    Route::get('hospitals/create/wizard', 'AdminHospitalEmpanelmentController@createForm')->name('hospitals.create-wizard');
    Route::post('hospitals/create-wizard/profile', 'AdminHospitalEmpanelmentController@storeCreateProfile')->name('hospitals.create-wizard.store-profile');
    Route::get('hospitals/{hospital}/edit', 'AdminHospitalEmpanelmentController@edit')->name('hospitals.edit');
    Route::post('hospitals/{hospital}/edit-step-load', 'AdminHospitalEmpanelmentController@stepLoad')->name('hospitals.edit.stepLoad');
    Route::post('hospitals/{hospital}/update-info', 'AdminHospitalEmpanelmentController@updateHospitalInfo')->name('hospitals.update.info');
    Route::post('hospitals/{hospital}/update-specialities', 'AdminHospitalEmpanelmentController@updateSpecialities')->name('hospitals.update.specialities');
    Route::post('hospitals/{hospital}/update-services', 'AdminHospitalEmpanelmentController@updateServices')->name('hospitals.update.services');
    Route::post('hospitals/{hospital}/update-licenses', 'AdminHospitalEmpanelmentController@updateLicenses')->name('hospitals.update.licenses');
    Route::post('hospitals/{hospital}/update-documents', 'AdminHospitalEmpanelmentController@updateDocuments')->name('hospitals.update.documents');
    Route::post('hospitals/{hospital}/hospital-submit', 'AdminHospitalEmpanelmentController@hospitalSubmit')->name('hospitals.hospitalSubmit');
    Route::post('hospitals/{hospital}/approve', 'HospitalController@approve')->name('hospitals.approve');  
    Route::post('hospitals/{hospital}/reject', 'HospitalController@reject')->name('hospitals.reject');  
    Route::get('permission/{id}', 'HospitalController@permission')->name('hospitals.permission');  
    Route::post('load-hospital', 'HospitalController@loadhospital')->name('hospitalload');  
    Route::post('view-status-modal',  'HospitalController@viewstatusmodal')->name('viewstatusmodal');  
    Route::post('change-status',  'HospitalController@changestatus')->name('changestatus');  
    Route::post('autoin',  'HospitalController@autoin')->name('autoin');  

    // Setting
    Route::resource('settings', 'SettingController');

    Route::get('profile', 'DashboardController@profile')->name('profile');
    Route::post('update-profile', 'DashboardController@update_profile')->name('update_profile');
    Route::post('change-password',  'DashboardController@changepassword')->name('changepassword');  

    Route::get('/register-requests', 'UserController@index')->name('register-requests');
    Route::post('/approve-user/{id}', 'UserController@approve')->name('users.approve');
    Route::get('/users', 'UserController@indexUser')->name('users.indexUser');
    Route::get('/users-details/{id}', 'UserController@view')->name('users.view');
    Route::get('/import', 'ExcelImportController@importView')->name('excel.import.view');
    Route::post('/import', 'ExcelImportController@import')->name('excel.import');
    Route::post('/import-with-code', 'ExcelImportController@importWithCode')->name('excel.importWithCode');

});