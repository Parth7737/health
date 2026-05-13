<?php
use Illuminate\Support\Facades\Route;
Route::group(['middleware'=>'decverifier','namespace' => 'App\Http\Controllers\DecVerifier', 'prefix' => 'district/verifier', 'as' => 'decverifier.'], function () {
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
    Route::get('/worklist', 'DashboardController@worklist')->name('worklist');
    Route::get('/get-facility-data', 'DashboardController@getFacilityData')->name('getData');
    Route::get('empanelment/submittedApplicationWork/{hospitalId}/{uuid}', 'DashboardController@getHospital')->name('gethospital');
    Route::post('/get-work-flow-data/{hospitalId}/{uuid}', 'DashboardController@getWorkFlowData')->name('getWorkFlowData');    
    Route::post('load-step/{hospitalId}/{uuid}', 'DashboardController@loadStep')->name('stepLoad');

    Route::post('submit-establishment-review/{hospitalId}/{uuid}', 'DashboardController@saveEstablishmentReview')->name('saveEstablishmentReview');
    Route::post('submit-address-review/{hospitalId}/{uuid}', 'DashboardController@saveAddressReview')->name('saveAddressReview');
    Route::post('submit-speciality-review/{hospitalId}/{uuid}', 'DashboardController@saveSpecialityReview')->name('saveSpecialityReview');
    Route::post('submit-services-review/{hospitalId}/{uuid}', 'DashboardController@saveServicesReview')->name('saveServicesReview');
    Route::post('submit-licenses-review/{hospitalId}/{uuid}', 'DashboardController@saveLicensesReview')->name('saveLicensesReview');
    Route::post('submit-ceo-review/{hospitalId}/{uuid}', 'DashboardController@saveCEOReview')->name('saveCEOReview');
    Route::post('submit-mhr-review/{hospitalId}/{uuid}', 'DashboardController@saveMHRReview')->name('saveMHRReview');
    Route::post('submit-sshr-review/{hospitalId}/{uuid}', 'DashboardController@saveSSHRReview')->name('saveSSHRReview');
    Route::post('submit-specialities-review/{hospitalId}/{uuid}', 'DashboardController@saveSPECReview')->name('saveSPECReview');
    Route::post('submit-accreditation-review/{hospitalId}/{uuid}', 'DashboardController@saveAccreditationReview')->name('saveAccreditationReview');
    Route::post('submit-finance-review/{hospitalId}/{uuid}', 'DashboardController@saveFinancialReview')->name('saveFinancialReview');
    Route::post('submit-taxdetails-review/{hospitalId}/{uuid}', 'DashboardController@saveTaxdetailsReview')->name('saveTaxdetailsReview');
    Route::post('submit-document-review/{hospitalId}/{uuid}', 'DashboardController@saveDocumentReview')->name('saveDocumentReview');
    Route::post('submit-verifier-report/{hospitalId}/{verifyId}/{uuid}', 'DashboardController@submitVerifierReport')->name('submitVerifierReport');    
});
