<?php
use Illuminate\Support\Facades\Route;
Route::group(['middleware'=>'ppd','namespace' => 'App\Http\Controllers\PPD', 'prefix' => 'ppd', 'as' => 'ppd.'], function () {
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
    Route::post('/dashboard-users', 'DashboardController@dashboardUsers')->name('dashboard-users');

    Route::get('/preauth-request/{id}', 'PreauthController@preauthRequest')->name('preauth-request');
    Route::get('/past-history/{id}', 'PreauthController@pastHistory')->name('past-history');
    Route::post('/request-form-sumbit', 'PreauthController@requestFormSumbit')->name('request-form-sumbit');
    Route::post('/validate-form', 'PreauthController@validateForm')->name('validate-form');
    
    Route::post('/approve-preauth', 'PreauthController@approvePreauth')->name('approve-preauth');

    Route::post('/calculate-total', 'PreauthController@calculateTotal')->name('calculate-total');

    Route::post('loadpdf/{id}', 'PreauthController@loadpdf')->name('loadpdf');
    Route::post('verifydocument/{id}', 'PreauthController@verifydocument')->name('verifydocument');

    Route::post('loadremark/{id}', 'PreauthController@loadremark')->name('loadremark');
    Route::post('addRemark/{id}', 'PreauthController@addRemark')->name('addRemark');
    
    Route::post('open-tabs', 'PreauthController@openTabs')->name('open-tabs');

    Route::get('case-search', 'CaseSearchController@index')->name('case-search');
    Route::post('/loadcasesearch', 'CaseSearchController@loadcasesearch')->name('loadcasesearch');
    Route::get('view-search/{id}', 'CaseSearchController@viewSearch')->name('viewSearch');

});

Route::group(['namespace' => 'App\Http\Controllers\PPD', ], function () {
    Route::post('case-profile', 'PreauthController@caseProfile')->name('case-profile');
    Route::post('case-log', 'PreauthController@caseLog')->name('case-log');
    Route::post('hospital-profile', 'PreauthController@hospitalProfile')->name('hospital-profile');
});