<?php
use Illuminate\Support\Facades\Route;
Route::group(['middleware'=>'sha','namespace' => 'App\Http\Controllers\SHA', 'prefix' => 'sha', 'as' => 'sha.'], function () {
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
    Route::post('/update-recovery-status', 'PreauthController@updateRecoveryStatus')->name('update-recovery-status');

    Route::post('/approve-preauth', 'PreauthController@approvePreauth')->name('approve-preauth');
    Route::post('/reject-preauth', 'PreauthController@rejectPreauth')->name('reject-preauth');
    Route::post('/query-preauth', 'PreauthController@queryPreauth')->name('query-preauth');

    Route::post('bulk-approve-request', 'PreauthController@bulkApprove')->name('bulkApprove');

    Route::post('open-tabs', 'PreauthController@openTabs')->name('open-tabs');

    Route::post('loadpdf/{id}', 'PreauthController@loadpdf')->name('loadpdf');
    Route::post('verifydocument/{id}', 'PreauthController@verifydocument')->name('verifydocument');

    Route::get('case-search', 'CaseSearchController@index')->name('case-search');
    Route::post('/loadcasesearch', 'CaseSearchController@loadcasesearch')->name('loadcasesearch');
    Route::get('view-search/{id}', 'CaseSearchController@viewSearch')->name('viewSearch');
    Route::get('downloadreport', 'DashboardController@downloadreport')->name('downloadreport');
});
