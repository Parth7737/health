<?php
use Illuminate\Support\Facades\Route;
Route::group(['middleware'=>'hospital','namespace' => 'App\Http\Controllers\Hospital', 'prefix' => 'hospital', 'as' => 'hospital.'], function () {
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
    Route::post('/get-facility-data', 'DashboardController@getFacilityData')->name('getData');
    Route::get('/empanelment-registration', 'EmpanelmentRegistrationController@create')->name('empanelmentRegistration.create');
    Route::post('add-hfr-id', 'EmpanelmentRegistrationController@addHfrId')->name('empanelmentRegistration.addHfrId');
    Route::get('establisment-details/{uuid}', 'EmpanelmentRegistrationController@establismentDetails')->name('empanelmentRegistration.establismentDetails');
    Route::post('facility_ownership_sub_type', 'EmpanelmentRegistrationController@facility_ownership_sub_type')->name('empanelmentRegistration.facility_ownership_sub_type');
    Route::post('facility_ownership_sub_type2', 'EmpanelmentRegistrationController@facility_ownership_sub_type2')->name('empanelmentRegistration.facility_ownership_sub_type2');
    Route::post('facility_ownership_sub_type3', 'EmpanelmentRegistrationController@facility_ownership_sub_type3')->name('empanelmentRegistration.facility_ownership_sub_type3');

    Route::post('hospital-create/{uuid}', 'EmpanelmentRegistrationController@hospitalsCreate')->name('empanelmentRegistration.hospitalsCreate');
    Route::post('hospital-address-create/{uuid}', 'EmpanelmentRegistrationController@hospitalsAddressCreate')->name('empanelmentRegistration.hospitalsAddressCreate');
    
    Route::post('get-district', 'EmpanelmentRegistrationController@getDistrict')->name('empanelmentRegistration.getDistrict');
    Route::post('get-village', 'EmpanelmentRegistrationController@getVillage')->name('empanelmentRegistration.getVillage');
    Route::post('get-blocks', 'EmpanelmentRegistrationController@getBlocks')->name('empanelmentRegistration.getBlocks');
    Route::post('/send-otp-on-mobile', 'EmpanelmentRegistrationController@sendOTPOnMobile')->name('SendOTPOnMobile');
    Route::post('/resend-otp-on-mobile', 'EmpanelmentRegistrationController@reSendOTPOnMobile')->name('reSendOTPOnMobile');
    Route::post('/verify-otp', 'EmpanelmentRegistrationController@VerifyOtp')->name('VerifyOtp');
    
    Route::get('hospital-info/{uuid}', 'EmpanelmentRegistrationController@schemeDetails')->name('empanelmentRegistration.schemeDetails');

    Route::post('step-load/{uuid}/{hospitalid}', 'EmpanelmentRegistrationController@stepLoad')->name('empanelmentRegistration.stepLoad');
    Route::post('hospital/save-scheme/{uuid}/{hospitalid}', 'EmpanelmentRegistrationController@saveScheme')->name('empanelmentRegistration.saveScheme');

    Route::post('hospital/save-specialities/{uuid}/{hospitalid}', 'EmpanelmentRegistrationController@saveSpecialities')->name('empanelmentRegistration.saveSpecialities');
    Route::post('hospital/save-services/{uuid}/{hospitalid}', 'EmpanelmentRegistrationController@saveServices')->name('empanelmentRegistration.saveServices');
    Route::post('hospital/save-licenses/{uuid}/{hospitalid}', 'EmpanelmentRegistrationController@saveLicenses')->name('empanelmentRegistration.saveLicenses');
    Route::post('hospital/humanresourse/saveCEO/{uuid}/{hospitalid}', 'EmpanelmentRegistrationController@saveCEO')->name('empanelmentRegistration.saveCEO');
    Route::post('hospital/humanresourse/saveHR/{uuid}/{hospitalid}', 'EmpanelmentRegistrationController@saveHR')->name('empanelmentRegistration.saveHR');
    Route::post('hospital/humanresourse/verifyHPRId/{uuid}/{hospitalid}', 'EmpanelmentRegistrationController@verifyHPRId')->name('verifyHPRId');
    
    Route::post('hospital/humanresourse/loadHrTable/{uuid}/{hospitalid}', 'EmpanelmentRegistrationController@loadHrTable')->name('empanelmentRegistration.loadHrTable');
    Route::post('hospital/humanresource/deleteHR', 'EmpanelmentRegistrationController@deleteHR')->name('empanelmentRegistration.deleteHR');
    Route::post('hospital/humanresource/saveNoNHR/{uuid}/{hospitalid}', 'EmpanelmentRegistrationController@saveNoNHR')->name('empanelmentRegistration.saveNoNHR');
    Route::post('hospital/humanresource/saveHumanSpecialities/{uuid}/{hospitalid}', 'EmpanelmentRegistrationController@saveHumanSpecialities')->name('empanelmentRegistration.saveHumanSpecialities');

    Route::post('hospital/humanresourse/loadSpecialitiesTable/{uuid}/{hospitalid}', 'EmpanelmentRegistrationController@loadSpecialitiesTable')->name('empanelmentRegistration.loadSpecialitiesTable');
    Route::post('hospital/humanresource/deleteSpecialitiesHR', 'EmpanelmentRegistrationController@deleteSpecialitiesHR')->name('empanelmentRegistration.deleteSpecialitiesHR');
    
    Route::post('hospital/accreditation/{uuid}/{hospitalid}', 'EmpanelmentRegistrationController@accreditationForm')->name('empanelmentRegistration.accreditationForm');
    Route::post('hospital/financial/{uuid}/{hospitalid}', 'EmpanelmentRegistrationController@financialForm')->name('empanelmentRegistration.financialForm');
    Route::post('hospital/taxdetails/{uuid}/{hospitalid}', 'EmpanelmentRegistrationController@taxdetailsForm')->name('empanelmentRegistration.taxdetailsForm');

    Route::post('hospital/documents/{uuid}/{hospitalid}', 'EmpanelmentRegistrationController@saveHospitalDocuments')->name('empanelmentRegistration.saveDocuments');   
    
    Route::post('hospital/submitForm/{uuid}/{hospitalid}', 'EmpanelmentRegistrationController@hospitalSubmit')->name('empanelmentRegistration.hospitalSubmit');   

    Route::get('hospital/paymentIntiate/{uuid}/{hospitalid}', 'EmpanelmentRegistrationController@paymentIntiate')->name('empanelmentRegistration.paymentIntiate'); 
    
    Route::post('hospital/preview/{uuid}/{hospitalid}', 'EmpanelmentRegistrationController@hospitalpreview')->name('empanelmentRegistration.preview');

    Route::get('hospital/payment/success', 'EmpanelmentRegistrationController@paymentSuccess')->name('empanelmentRegistration.paymentSuccess');
    Route::get('hospital/payment/fail', 'EmpanelmentRegistrationController@paymentFail')->name('empanelmentRegistration.paymentFail');

    Route::get('hospital/cc-Response', 'EmpanelmentRegistrationController@ccResponse')->name('empanelmentRegistration.ccResponse');

    Route::post('hospital/singleDocument', 'DashboardController@singleDocument')->name('singleDocument');
    Route::post('hospital/updateDocument', 'DashboardController@updateDocument')->name('updateDocument');
    Route::post('submit-query-Response/{hospitalId}/{uuid}', 'EmpanelmentRegistrationController@submitResponse')->name('submitResponse');

    Route::get('single-empanelment-dashboard/{uuid}',  'EmpanelmentRegistrationController@empanelmentDashboard')->name('single-empanelment-dashboard');
    Route::post('/get-work-flow-data/{hospitalId}/{uuid}', 'EmpanelmentRegistrationController@getWorkFlowData')->name('getWorkFlowData');    
    Route::get('upgrade-hospital/{uuid}',  'EmpanelmentUpgradeController@updateApplication')->name('update-application');
    Route::post('load-upgrade-hospital-step/{uuid}/{hospitalId}', 'EmpanelmentUpgradeController@stepLoad')->name('update-application-stepLoad');
    Route::post('upgrade-establishment-details/{uuid}/{hospitalId}', 'EmpanelmentUpgradeController@upgradeEstablishmentDetails')->name('upgrade-establishment-details');
    Route::post('upgrade-address-details/{uuid}/{hospitalId}', 'EmpanelmentUpgradeController@upgradeAddressDetails')->name('upgrade-address-details');
    Route::post('upgrade-scheme-details/{uuid}/{hospitalId}', 'EmpanelmentUpgradeController@upgradeScheme')->name('upgrade-scheme');    
    Route::post('upgrade-specialities-details/{uuid}/{hospitalid}', 'EmpanelmentUpgradeController@upgradeSpecialities')->name('upgradeSpecialities');   
    Route::post('upgrade-service-details/{uuid}/{hospitalid}', 'EmpanelmentUpgradeController@upgradeServices')->name('upgrade-service-details');   
    Route::post('upgrade-licenses-details/{uuid}/{hospitalid}', 'EmpanelmentUpgradeController@upgradeLicenses')->name('upgrade-licenses-details');   
    Route::post('upgrade-ceo-details/{uuid}/{hospitalid}', 'EmpanelmentUpgradeController@upgradeCEO')->name('upgrade-ceo-details');   
    Route::post('humanresourse/loadUHrTable/{uuid}/{hospitalid}', 'EmpanelmentUpgradeController@loadUHrTable')->name('loadUHrTable');
    Route::post('humanresource/deleteUHR', 'EmpanelmentUpgradeController@deleteUHR')->name('deleteUHR');
    Route::post('loadUHRSingleData', 'EmpanelmentUpgradeController@loadUHRSingleData')->name('loadUHRSingleData');
    Route::post('humanresourse/saveUHR/{uuid}/{hospitalid}', 'EmpanelmentUpgradeController@saveUHR')->name('saveUHR');
    Route::post('humanresource/saveUHumanSpecialities/{uuid}/{hospitalid}', 'EmpanelmentUpgradeController@saveUHumanSpecialities')->name('saveUHumanSpecialities');
    Route::post('humanresourse/loadUSpecialitiesTable/{uuid}/{hospitalid}', 'EmpanelmentUpgradeController@loadUSpecialitiesTable')->name('loadUSpecialitiesTable');
    Route::post('humanresource/deleteUSpecialitiesHR', 'EmpanelmentUpgradeController@deleteUSpecialitiesHR')->name('deleteUSpecialitiesHR');
    Route::post('loadUSpecialitiesSingleData', 'EmpanelmentUpgradeController@loadUSpecialitiesSingleData')->name('loadUSpecialitiesSingleData');
    Route::post('humanresource/saveUNoNHR/{uuid}/{hospitalid}', 'EmpanelmentUpgradeController@saveUNoNHR')->name('saveUNoNHR');
    Route::post('UaccreditationForm/{uuid}/{hospitalid}', 'EmpanelmentUpgradeController@UaccreditationForm')->name('UaccreditationForm');
    Route::post('Ufinancial/{uuid}/{hospitalid}', 'EmpanelmentUpgradeController@UfinancialForm')->name('UfinancialForm');
    Route::post('Utaxdetails/{uuid}/{hospitalid}', 'EmpanelmentUpgradeController@UtaxdetailsForm')->name('UtaxdetailsForm');

    Route::post('hospital/ResubmitForm/{uuid}/{hospitalid}', 'EmpanelmentUpgradeController@hospitalReSubmit')->name('hospitalReSubmit');   
    // Route::get('upgrade-hospital/{uuid}', 'EmpanelmentRegistrationController@schemeDetails')->name('upgrade-hospital');

    Route::get('annual-declaration/{uuid}', 'AnnualDeclarationController@index')->name('annualdeclaration');
    Route::post('save-annual-declaration/{uuid}/{hospital_id}', 'AnnualDeclarationController@savedeclaration')->name('savedeclaration');

    Route::get('quality-audit/{uuid}', 'QualityAuditController@index')->name('qualityaudit');
    Route::post('load-quality-audit-step/{uuid}/{hospital_id}', 'QualityAuditController@loadstep')->name('load-quality-audit-step');
    Route::post('quality-audit-save/{uuid}/{hospital_id}', 'QualityAuditController@saveQualityAudit')->name('quality-audit-save');

    // EDC

    Route::get('edc', 'EDCController@index')->name('edcindex');
    Route::get('initiate-edc', 'EDCController@initiateEDC')->name('initiate.actionlist');
    // Route::post('hospitallist', 'EDCController@hospitallist')->name('initiate.hospitallist');
    // Route::get('initiate-action/{hospitalId}/{uuid}', 'EDCController@initiateAction')->name('initiate.action');
    Route::post('save-initiate-action/{hospitalId}/{uuid}', 'EDCController@saveinitiateAction')->name('saveinitiate-action');
    Route::post('update-initiate-action/{actionId}/{uuid}', 'EDCController@updateinitiateAction')->name('updateinitiate-action');
    Route::get('view/{actionid}', 'EDCController@viewAction')->name('viewAction');
    Route::post('load-action-data', 'EDCController@loadedcactiondata')->name('edcactiondata');

    Route::post('checkexisthospital', 'EmpanelmentRegistrationController@checkexisthospital')->name('checkexisthospital');
    Route::post('directSubmit/{uuid}/{hospital_id}', 'EmpanelmentRegistrationController@directSubmit')->name('directSubmit');
    
    Route::get('withdraw-application/{uuid}', 'DashboardController@withdraw')->name('withdraw-application');

});
