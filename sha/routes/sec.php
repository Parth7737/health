<?php
use Illuminate\Support\Facades\Route;
Route::group(['middleware'=>'sec','namespace' => 'App\Http\Controllers\Sec', 'prefix' => 'state-committee', 'as' => 'sec.'], function () {
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
    Route::get('/worklist', 'DashboardController@worklist')->name('worklist');
    Route::post('/get-facility-data', 'DashboardController@getFacilityData')->name('getData');
    Route::post('/get-work-flow-data/{hospitalId}/{uuid}', 'SECController@getWorkFlowData')->name('getWorkFlowData');    
    Route::get('empanelment/submittedApplicationWork/{hospitalId}/{uuid}', 'SECController@getHospital')->name('gethospital');
    Route::post('dec/preview/{hospitalid}/{uuid}', 'SECController@hospitalpreview')->name('hospital.preview');
    Route::get('initiate-verification/{hospitalid}/{uuid}', 'SECController@initiateVerification')->name('initiate.verification');
    Route::post('initiate-verification/{hospitalid}/{uuid}', 'SECController@initiateVerificationSubmit')->name('initiate.verificationsave');

    Route::post('load-step/{hospitalId}/{uuid}', 'SECController@loadStep')->name('stepLoad');
    Route::post('submit-establishment-review/{hospitalId}/{uuid}', 'SECController@saveEstablishmentReview')->name('saveEstablishmentReview');
    Route::post('submit-address-review/{hospitalId}/{uuid}', 'SECController@saveAddressReview')->name('saveAddressReview');
    Route::post('submit-speciality-review/{hospitalId}/{uuid}', 'SECController@saveSpecialityReview')->name('saveSpecialityReview');
    Route::post('submit-services-review/{hospitalId}/{uuid}', 'SECController@saveServicesReview')->name('saveServicesReview');
    Route::post('submit-licenses-review/{hospitalId}/{uuid}', 'SECController@saveLicensesReview')->name('saveLicensesReview');
    Route::post('submit-ceo-review/{hospitalId}/{uuid}', 'SECController@saveCEOReview')->name('saveCEOReview');
    Route::post('submit-mhr-review/{hospitalId}/{uuid}', 'SECController@saveMHRReview')->name('saveMHRReview');
    Route::post('submit-sshr-review/{hospitalId}/{uuid}', 'SECController@saveSSHRReview')->name('saveSSHRReview');
    Route::post('submit-specialities-review/{hospitalId}/{uuid}', 'SECController@saveSPECReview')->name('saveSPECReview');
    Route::post('submit-accreditation-review/{hospitalId}/{uuid}', 'SECController@saveAccreditationReview')->name('saveAccreditationReview');
    Route::post('submit-finance-review/{hospitalId}/{uuid}', 'SECController@saveFinancialReview')->name('saveFinancialReview');
    Route::post('submit-taxdetails-review/{hospitalId}/{uuid}', 'SECController@saveTaxdetailsReview')->name('saveTaxdetailsReview');
    Route::post('submit-document-review/{hospitalId}/{uuid}', 'SECController@saveDocumentReview')->name('saveDocumentReview');
    Route::post('submit-verifier-report/{hospitalId}/{verifyId}/{uuid}', 'SECController@submitVerifierReport')->name('submitVerifierReport');    
    Route::get('annual-declaration', 'SECController@annualdeclaration')->name('annualdeclaration');
    Route::post('getAnnualData', 'SECController@getAnnualData')->name('getAnnualData');
    Route::get('edc', 'EDCController@index')->name('edcindex');
    Route::get('initiate-edc', 'EDCController@initiateEDC')->name('initiate.actionlist');
    Route::post('hospitallist', 'EDCController@hospitallist')->name('initiate.hospitallist');
    Route::get('initiate-action/{hospitalId}/{uuid}', 'EDCController@initiateAction')->name('initiate.action');
    Route::post('save-initiate-action/{hospitalId}/{uuid}', 'EDCController@saveinitiateAction')->name('saveinitiate-action');
    Route::get('view/{actionid}', 'EDCController@viewAction')->name('viewAction');
    Route::post('load-action-data', 'EDCController@loadedcactiondata')->name('edcactiondata');
    Route::post('update-initiate-action/{actionId}/{uuid}', 'EDCController@updateinitiateAction')->name('updateinitiate-action');
        
    Route::post('hospitaltypechart', 'SECController@hospitaltypechart')->name('hospitaltypechart');
    Route::post('bedsizechart', 'SECController@bedsizechart')->name('bedsizechart');
    Route::post('statusChart', 'SECController@statusChart')->name('statusChart');
    Route::post('trandsChart', 'SECController@trandsChart')->name('trandsChart');
    Route::post('specialiitieschart', 'SECController@specialiitieschart')->name('specialiitieschart');
    Route::post('loadstatitacs', 'DashboardController@loadstatitacs')->name('loadstatitacs');
});
