<?php
use Illuminate\Support\Facades\Route;
Route::group(['middleware'=>'preauth','namespace' => 'App\Http\Controllers\Preauth', 'prefix' => 'preauth', 'as' => 'preauth.'], function () {
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
    Route::post('/dashboard-users', 'DashboardController@dashboardUsers')->name('dashboard-users');

    Route::get('/search-beneficiary', 'DashboardController@searchBeneficiary')->name('search-beneficiary');
    Route::get('/test_verification', 'PreauthController@test_verification')->name('test_verification');
    Route::get('/test_mail', 'PreauthController@testMail')->name('test_mail');

    Route::post('/send-otp-on-aadhar', 'PreauthController@sendOTPOnAadhar')->name('send-aadhar-otp');
    Route::post('/resend-otp-on-aadhar', 'PreauthController@reSendOTPOnAadhar')->name('resend-aadhar-otp');
    Route::post('/verify-aadhar-otp', 'PreauthController@verifyAadharOtp')->name('verify-aadhar-otp');

    Route::post('/fetch-card', 'PreauthController@fetchCard')->name('fetch-card');
    Route::post('/verify-card', 'PreauthController@verifyCard')->name('verify-card');
    Route::post('/register-patient-ses', 'PreauthController@registerPatientSession')->name('register-patient-ses');
    Route::get('/register-patient', 'PreauthController@registerPatient')->name('register-patient');
    Route::post('/register-patient', 'PreauthController@registerPatientStore')->name('register-patient.store');
    Route::post('/send-otp-on-mobile', 'PreauthController@sendOTPOnMobile')->name('send-mobile-otp');
    Route::post('/resend-otp-on-mobile', 'PreauthController@reSendOTPOnMobile')->name('resend-mobile-otp');
    Route::post('/verify-mobile-otp', 'PreauthController@verifyMobileOtp')->name('verify-mobile-otp');

    Route::get('/preauth-request/{id}', 'PreauthController@preauthRequest')->name('preauth-request');

    Route::post('/general-information', 'PreauthController@generalInformation')->name('general-information.store');
    Route::post('/family-history', 'PreauthController@familyHistory')->name('family-history.store');
    Route::post('/personal-history', 'PreauthController@personalHistory')->name('personal-history.store');
    Route::post('/authentication-consent', 'PreauthController@authenticationConsent')->name('authentication-consent.store');
    Route::post('/admission-details', 'PreauthController@admissionDetails')->name('admission-details.store');
    Route::post('/diagnosis', 'PreauthController@diagnosis')->name('diagnosis.store');
    Route::post('/delete-diagnosis', 'PreauthController@deleteDiagnosis')->name('diagnosis.destroy');

    Route::post('/get-procedures', 'PreauthController@getProcedures')->name('get-procedures');
    Route::post('/get-procedure-details', 'PreauthController@getProcedureDetail')->name('get-procedure-details');
    Route::post('/get-implant-details', 'PreauthController@getImplantDetail')->name('get-implant-details');
    Route::post('/get-stratification-details', 'PreauthController@getStratificationDetail')->name('get-stratification-details');
    Route::post('/procedure', 'PreauthController@procedure')->name('procedure.store');
    Route::post('/delete-procedure', 'PreauthController@deleteProcedure')->name('procedure.destroy');
    Route::post('/delete-implant', 'PreauthController@deleteImplant')->name('procedure.delete-implant');

    Route::post('/care-team', 'PreauthController@careTeam')->name('care-team.store');
    Route::post('/delete-team', 'PreauthController@deleteTeam')->name('care-team.destroy');
    
    Route::post('/investigation', 'PreauthController@investigation')->name('investigation.store');
    Route::post('/request-form-sumbit', 'PreauthController@requestFormSumbit')->name('request-form-sumbit');
    Route::post('/validate-form', 'PreauthController@validateForm')->name('validate-form');

    Route::post('/cancel-registration', 'PreauthController@cancelRegistration')->name('cancel-registration');
    Route::post('/cancel-preauth', 'PreauthController@cancelPreauth')->name('cancel-preauth');
    Route::post('/query-preauth', 'PreauthController@queryPreauth')->name('query-preauth');
    Route::post('/u100-query-preauth', 'PreauthController@u100QueryPreauth')->name('u100-query-preauth');
    Route::post('/discharge-patient', 'PreauthController@dischargePatient')->name('discharge-patient');
    Route::post('/claim-patient', 'PreauthController@claimPatient')->name('claim-patient');
    Route::post('/query-claim', 'PreauthController@queryClaim')->name('query-claim');
    Route::post('/raise-errorneous-claim', 'PreauthController@raiseErrorneousClaim')->name('raise-errorneous-claim');
    Route::post('/errorneous-query-claim', 'PreauthController@errorneousQueryClaim')->name('errorneous-query-claim');

    Route::post('/resubmit-preauth', 'PreauthController@resubmitPreauth')->name('resubmit');
    Route::post('refresh-resubmit', 'PreauthController@refreshResubmit')->name('refresh-resubmit');
    Route::post('/procedure/delete-temp', 'PreauthController@procedureDeleteTemp')->name('procedure.delete-temp');
    Route::post('/procedure/delete-temp-implant', 'PreauthController@procedureDeleteTempImplant')->name('procedure.delete-temp-implant');
    Route::post('/procedure/delete-enhancement', 'PreauthController@procedureEnhancementDelete')->name('procedure.delete-enhancement');
    Route::post('/enhancement-preauth', 'PreauthController@enhancementPreauth')->name('enhancement');

    Route::post('loadremark/{id}', 'PreauthController@loadremark')->name('loadremark');
    Route::post('addRemark/{id}', 'PreauthController@addRemark')->name('addRemark');

    
    Route::get('/update-old-preauth-amount', 'PreauthController@updateOldPreauthAmount');
    Route::get('/update-old-preauth-procedures', 'PreauthController@updateOldPreauthProcedures');
});
