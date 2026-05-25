<?php
use Illuminate\Support\Facades\Route;
Route::group(['namespace' => 'App\Http\Controllers\StateAdmin', 'prefix' => 'state-admin', 'as' => 'state-admin.', 'middleware' => ['state-admin']], function () {
    Route::resource('dashboard', 'DashboardController');
    Route::get('onboard-facility', 'DashboardController@onboardFacility')->name('onboard-facility');
});