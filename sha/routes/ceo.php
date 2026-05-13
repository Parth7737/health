<?php
use Illuminate\Support\Facades\Route;
Route::group(['middleware'=>'ceo','namespace' => 'App\Http\Controllers\CEO', 'prefix' => 'ceo', 'as' => 'ceo.'], function () {
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
    Route::post('/dashboard-users', 'DashboardController@dashboardUsers')->name('dashboard-users');

    Route::get('/preauth-request/{id}', 'PreauthController@preauthRequest')->name('preauth-request');
    Route::get('/past-history/{id}', 'PreauthController@pastHistory')->name('past-history');
    Route::post('/request-form-sumbit', 'PreauthController@requestFormSumbit')->name('request-form-sumbit');
    Route::post('/validate-form', 'PreauthController@validateForm')->name('validate-form');
    
    Route::post('/approve-preauth', 'PreauthController@approvePreauth')->name('approve-preauth');
    Route::post('/reject-preauth', 'PreauthController@rejectPreauth')->name('reject-preauth');
    Route::post('/query-preauth', 'PreauthController@queryPreauth')->name('query-preauth');

    Route::post('loadpdf/{id}', 'PreauthController@loadpdf')->name('loadpdf');
    Route::post('verifydocument/{id}', 'PreauthController@verifydocument')->name('verifydocument');

    Route::post('open-tabs', 'PreauthController@openTabs')->name('open-tabs');

    Route::get('case-search', 'CaseSearchController@index')->name('case-search');
    Route::post('/loadcasesearch', 'CaseSearchController@loadcasesearch')->name('loadcasesearch');
    Route::get('view-search/{id}', 'CaseSearchController@viewSearch')->name('viewSearch');
});
