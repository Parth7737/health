<?php
use Illuminate\Support\Facades\Route;
Route::group(['middleware'=>'aco','namespace' => 'App\Http\Controllers\ACO', 'prefix' => 'aco', 'as' => 'aco.'], function () {
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
    Route::post('/dashboard-users', 'DashboardController@dashboardUsers')->name('dashboard-users');

    Route::get('/preauth-request/{id}', 'PreauthController@preauthRequest')->name('preauth-request');
    Route::get('/past-history/{id}', 'PreauthController@pastHistory')->name('past-history');
    Route::post('/request-form-sumbit', 'PreauthController@requestFormSumbit')->name('request-form-sumbit');
    Route::post('/validate-form', 'PreauthController@validateForm')->name('validate-form');
    
    Route::post('/erroneous-claim-action', 'PreauthController@erroneousClaimAction')->name('erroneous-claim-action');
    
    Route::get('/hospital-recovery-amount', 'PreauthController@hospitalRecoveryAmount')->name('hospital-recovery-amount');
    Route::get('/search-recovery-hospitals', 'PreauthController@searchRecoveryHospitals')->name('search-recovery-hospitals');
    Route::get('/hospital-recovery/{id}', 'PreauthController@hospitalRecovery')->name('hospital-recovery');
    Route::get('/hospital-recovery-history/{id}', 'PreauthController@hospitalRecoveryHistory')->name('hospital-recovery-history');
    Route::post('/recovery-request-submit', 'PreauthController@recoveryRequestSubmit')->name('recovery-request-submit');
    
    Route::get('/adjustment-upload', 'PreauthController@adjustmentUpload')->name('adjustment-upload');
    Route::post('/upload-excel', 'PreauthController@uploadExcel')->name('upload-excel');

    Route::post('/approve-preauth', 'PreauthController@approvePreauth')->name('approve-preauth');
    Route::post('/reject-preauth', 'PreauthController@rejectPreauth')->name('reject-preauth');
    Route::post('/query-preauth', 'PreauthController@queryPreauth')->name('query-preauth');

    Route::post('bulk-approve-request', 'PreauthController@bulkApprove')->name('bulkApprove');

    Route::get('/bank-account-details', 'PreauthController@bankAccountDetails')->name('bank-account-details');
    Route::post('/loadbankdetails', 'PreauthController@loadbankdetails')->name('loadbankdetails');

    Route::get('case-search', 'CaseSearchController@index')->name('case-search');
    Route::post('/loadcasesearch', 'CaseSearchController@loadcasesearch')->name('loadcasesearch');
    Route::get('view-search/{id}', 'CaseSearchController@viewSearch')->name('viewSearch');
    Route::post('loadpdf/{id}', 'PreauthController@loadpdf')->name('loadpdf');

});
