<?php
use Illuminate\Support\Facades\Route;
Route::group(['middleware'=>'cpd','namespace' => 'App\Http\Controllers\CPD', 'prefix' => 'cpd', 'as' => 'cpd.'], function () {
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
    Route::post('/dashboard-users', 'DashboardController@dashboardUsers')->name('dashboard-users');

    Route::get('/preauth-request/{id}', 'PreauthController@preauthRequest')->name('preauth-request');
    Route::get('/past-history/{id}', 'PreauthController@pastHistory')->name('past-history');
    Route::post('/request-form-sumbit', 'PreauthController@requestFormSumbit')->name('request-form-sumbit');
    
    Route::post('/submit-preauth-claim', 'PreauthController@approvePreauthClaim')->name('submit-preauth-claim');

    Route::post('/erroneous-claim-action', 'PreauthController@erroneousClaimAction')->name('erroneous-claim-action');

    Route::post('loadpdf/{id}', 'PreauthController@loadpdf')->name('loadpdf');
    Route::post('verifydocument/{id}', 'PreauthController@verifydocument')->name('verifydocument');

    Route::post('loadremark/{id}', 'PreauthController@loadremark')->name('loadremark');
    Route::post('addRemark/{id}', 'PreauthController@addRemark')->name('addRemark');

    Route::post('open-tabs', 'PreauthController@openTabs')->name('open-tabs');

    Route::post('get-deduction', 'PreauthController@getDeduction')->name('get-deduction');
    Route::post('save-deduction', 'PreauthController@saveDeduction')->name('save-deduction');

    Route::get('case-search', 'CaseSearchController@index')->name('case-search');
    Route::post('/loadcasesearch', 'CaseSearchController@loadcasesearch')->name('loadcasesearch');
    Route::get('view-search/{id}', 'CaseSearchController@viewSearch')->name('viewSearch');

    Route::post('/calculate-total', 'PreauthController@calculateTotal')->name('calculate-total');

});
