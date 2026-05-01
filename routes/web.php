<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Hospital\RadiologyRisController;
use App\Http\Controllers\{ RegisterController, HomeController, CaptchaController, LoginController,RoleController,PermissionController };
use App\Http\Controllers\Auth\PasswordResetLinkController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PasswordResetController;

Route::get('/forgot-password', 'PasswordResetLinkController@create')
    ->middleware('guest')
    ->name('password.request');

// Route::get('/fresh-migrate', function () {
//     return \Artisan::call('migrate:fresh --seed');
// });

Route::get('/seederRun', function() {
    // \Artisan::call('db:seed ServiceSeeder');
    // \Artisan::call('db:seed LicensesSeeder');
    \Artisan::call('db:seed HospitalPermissionsSeeder');
    return true;
});
Route::get('/default-seeder', function() {
    \Artisan::call('db:seed CountryCodeSeeder');
    \Artisan::call('db:seed NationalitySeeder');
    return true;
});
Route::get('/fresh-seeder', function() {
    \Artisan::call('db:seed');
    return true;
});

Route::get('/migrate', function () {
    try {
        \Artisan::call('migrate', ['--force' => true]);
        return response()->json([
            'output' => Artisan::output(),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
        ]);
    }
});
Route::get('/clear-cache', function () {
    return \Artisan::call('optimize:clear');
});
// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('hospital/store', [HomeController::class, 'store'])->name('hospital.store');
Route::post('hospital/getDistrict', [HomeController::class, 'getDistrict'])->name('hospital.getDistrict');
Route::post('hospital/getHospitalSubtype', [HomeController::class, 'getHospitalSubtype'])->name('hospital.getHospitalSubtype');

Route::get('/', [RegisterController::class, 'login'])->name('hospital.login');
Route::get('/signup', [RegisterController::class, 'signup'])->name('hospital.signup');
Route::post('hospital/register', [RegisterController::class, 'store'])->name('hospital.register');

Route::post('register/sendEmailMail', [RegisterController::class, 'sendEmailMail'])->name('sendEmailMail');
Route::post('register/sendMobileMail', [RegisterController::class, 'sendMobileMail'])->name('sendMobileMail');



Route::post('register/verify-email-otp', [RegisterController::class, 'verifyEmailOtp'])->name('verifyEmailOtp');
Route::post('register/resendOTP', [RegisterController::class, 'resendOTP'])->name('resendOTP');
Route::post('register/store', [RegisterController::class, 'store'])->name('register.store');
Route::get('register/dashboard/{uuid}', [RegisterController::class, 'dashboard'])->name('register.dashboard');
Route::post('register/updateProfile/{uuid}', [RegisterController::class, 'updateProfile'])->name('register.updateProfile');
Route::post('register/updateentity/{uuid}', [RegisterController::class, 'updateentity'])->name('register.updateentity');
Route::post('register/updateUserData/{uuid}', [RegisterController::class, 'updateUserData'])->name('register.updateUserData');
Route::post('login/checkUser', [LoginController::class, 'checkUser'])->name('login.checkUser');
Route::post('login/send-otp', [LoginController::class, 'sendOtp'])->name('portal.login.sendOtp');
Route::post('login/verify-otp', [LoginController::class, 'verifyOtp'])->name('portal.login.verifyOtp');
Route::post('login/forgot/send-otp', [LoginController::class, 'sendForgotOtp'])->name('portal.login.forgot.sendOtp');
Route::post('login/forgot/verify-otp', [LoginController::class, 'verifyForgotOtp'])->name('portal.login.forgot.verifyOtp');
Route::post('login/forgot/reset-password', [LoginController::class, 'resetPasswordWithOtp'])->name('portal.login.forgot.resetPassword');
Route::post('login/hospital', [LoginController::class, 'login'])->name('portal.login');

Route::post('/send-mobile-otp', [HomeController::class, 'sendOTPOnMobile'])->name('SendOTPOnMobile');
Route::post('/resend-otp-on-mobile', [HomeController::class, 'reSendOTPOnMobile'])->name('reSendOTPOnMobile');
Route::post('/verify-otp', [HomeController::class, 'verifiyMobileOtp'])->name('verifiyMobileOtp');

Route::post('/send-email-otp', [HomeController::class, 'SendOTPOnEmail'])->name('SendOTPOnEmail');
Route::post('/resend-otp-on-email', [HomeController::class, 'ReSendOTPOnEmail'])->name('ReSendOTPOnEmail');
Route::post('/verify-otp-email', [HomeController::class, 'verifiyEmailOtp'])->name('verifiyEmailOtp');

require __DIR__.'/auth.php';

require __DIR__.'/admin.php';

require __DIR__.'/hospital.php';

//front pages\
Route::get('/appointments', [HomeController::class, 'appointments']);
Route::get('/new-dashboard', [HomeController::class, 'newDashboard'])->name('new-dashboard');
Route::get('/visitor-book', [HomeController::class, 'visitorBook']);

Route::get('/opd-patient', function () {
    return view('front.opd-patient');
});

Route::get('/user-profile', function () {
    return view('front.user-profile');
});

Route::get('csrf-token', function() {
    return response()->json([
        'token' => csrf_token(),
    ]);
})->name('csrf_token');

Route::prefix('integration/radiology/pacs')->group(function () {
    Route::post('ingest', [RadiologyRisController::class, 'pacsIngest'])->name('integration.radiology.pacs.ingest');
    Route::get('worklist', [RadiologyRisController::class, 'pacsWorklistFeed'])->name('integration.radiology.pacs.worklist');
});

Route::get('/patient-profile', function () {
    return view('front.patient-profile');
});

Route::get('/ipd-patient-profile', function () {
    return view('front.ipd-patient-profile');
});
Route::get('/staff-lists', function () {
    return view('front.staff-list');
});

Route::get('/add-page', function() {
    return view('front.add-page');
});

Route::get('/transaction-report', function() {
    return view('front.transaction-report');
});
Route::get('/chat', function() {
    return view('front.chat');
});
Route::get('/settings', function() {
    return view('front.settings');
});
Route::get('/calendar-todo', function() {
    return view('front.calendar-todo');
});

Route::get('/idcard', function() {
    return view('front.idcard');
});

Route::get('/demo-code', function() {
    return view('front.demo-code');
})->name('demo.code');
