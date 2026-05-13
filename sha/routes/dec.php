<?php
use Illuminate\Support\Facades\Route;
Route::group(['middleware'=>'dec','namespace' => 'App\Http\Controllers\Dec', 'prefix' => 'division/dec', 'as' => 'dec.'], function () {
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
    Route::get('/worklist', 'DashboardController@worklist')->name('worklist');
    Route::post('/get-facility-data', 'DashboardController@getFacilityData')->name('getData');
    Route::post('/get-work-flow-data/{hospitalId}/{uuid}', 'DECController@getWorkFlowData')->name('getWorkFlowData');    
    Route::get('empanelment/submittedApplicationWork/{hospitalId}/{uuid}', 'DECController@getHospital')->name('gethospital');
    Route::post('dec/preview/{hospitalid}/{uuid}', 'DECController@hospitalpreview')->name('hospital.preview');
    Route::get('initiate-verification/{hospitalid}/{uuid}', 'DECController@initiateVerification')->name('initiate.verification');
    Route::post('initiate-verification/{hospitalid}/{uuid}', 'DECController@initiateVerificationSubmit')->name('initiate.verificationsave');

    Route::post('load-step/{hospitalId}/{uuid}', 'DECController@loadStep')->name('stepLoad');
    Route::post('submit-establishment-review/{hospitalId}/{uuid}', 'DECController@saveEstablishmentReview')->name('saveEstablishmentReview');
    Route::post('submit-address-review/{hospitalId}/{uuid}', 'DECController@saveAddressReview')->name('saveAddressReview');
    Route::post('submit-speciality-review/{hospitalId}/{uuid}', 'DECController@saveSpecialityReview')->name('saveSpecialityReview');
    Route::post('submit-services-review/{hospitalId}/{uuid}', 'DECController@saveServicesReview')->name('saveServicesReview');
    Route::post('submit-licenses-review/{hospitalId}/{uuid}', 'DECController@saveLicensesReview')->name('saveLicensesReview');
    Route::post('submit-ceo-review/{hospitalId}/{uuid}', 'DECController@saveCEOReview')->name('saveCEOReview');
    Route::post('submit-mhr-review/{hospitalId}/{uuid}', 'DECController@saveMHRReview')->name('saveMHRReview');
    Route::post('submit-sshr-review/{hospitalId}/{uuid}', 'DECController@saveSSHRReview')->name('saveSSHRReview');
    Route::post('submit-specialities-review/{hospitalId}/{uuid}', 'DECController@saveSPECReview')->name('saveSPECReview');
    Route::post('submit-accreditation-review/{hospitalId}/{uuid}', 'DECController@saveAccreditationReview')->name('saveAccreditationReview');
    Route::post('submit-finance-review/{hospitalId}/{uuid}', 'DECController@saveFinancialReview')->name('saveFinancialReview');
    Route::post('submit-taxdetails-review/{hospitalId}/{uuid}', 'DECController@saveTaxdetailsReview')->name('saveTaxdetailsReview');
    Route::post('submit-document-review/{hospitalId}/{uuid}', 'DECController@saveDocumentReview')->name('saveDocumentReview');
    Route::post('submit-verifier-report/{hospitalId}/{verifyId}/{uuid}', 'DECController@submitVerifierReport')->name('submitVerifierReport');    
    Route::post('submitDecResponse/{hospitalId}/{uuid}', 'DECController@submitDecResponse')->name('submitDecResponse');    
    
    Route::post('hospitaltypechart', 'DECController@hospitaltypechart')->name('hospitaltypechart');
    Route::post('bedsizechart', 'DECController@bedsizechart')->name('bedsizechart');
    Route::post('statusChart', 'DECController@statusChart')->name('statusChart');
    Route::post('trandsChart', 'DECController@trandsChart')->name('trandsChart');
    Route::post('specialiitieschart', 'DECController@specialiitieschart')->name('specialiitieschart');
    Route::post('loadstatitacs', 'DashboardController@loadstatitacs')->name('loadstatitacs');
});
