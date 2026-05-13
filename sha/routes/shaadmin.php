<?php
use Illuminate\Support\Facades\Route;
Route::group(['middleware'=>'shaadmin','namespace' => 'App\Http\Controllers\SHAAdmin', 'prefix' => 'sha-admin', 'as' => 'shaadmin.'], function () {
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
    Route::post('/dashboard-users', 'DashboardController@dashboardUsers')->name('dashboard-users');
    Route::get('createisa', 'DashboardController@createisa')->name('createisa');
    Route::post('getUserData', 'DashboardController@getData')->name('getData');
    Route::post('changeStatus', 'DashboardController@changeStatus')->name('changeStatus');
    Route::get('user-info/{id}/{type}', 'DashboardController@userInfo')->name('userInfo');
    Route::post('register-isa-user', 'DashboardController@registerIsaUser')->name('registerIsaUser');

});

// ISA Admin Routes
Route::group([
    'middleware' => 'shaadmin',  // Using the same middleware
    'namespace' => 'App\Http\Controllers\SHAAdmin', // Assuming ISAAdmin has its own controllers
    'prefix' => 'isa-admin',
    'as' => 'isaadmin.'
], function () {
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
    Route::post('/dashboard-users', 'DashboardController@dashboardUsers')->name('dashboard-users');
    Route::get('createisa', 'DashboardController@createisa')->name('createisa');
    Route::post('getUserData', 'DashboardController@getData')->name('getData');
    Route::post('changeStatus', 'DashboardController@changeStatus')->name('changeStatus');
    Route::get('user-info/{id}/{type}', 'DashboardController@userInfo')->name('userInfo');
    Route::post('register-isa-user', 'DashboardController@registerIsaUser')->name('registerIsaUser');

});
