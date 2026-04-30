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