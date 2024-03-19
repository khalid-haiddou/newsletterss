<?php
use App\Http\Controllers\MailController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\NewsletterController;
use App\Http\Middleware\JwtAuthVal;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/', function () {
    return view('auth.form');
});

Route::middleware(JwtAuthVal::class)->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', function () {
        return view('dashboard');
    });

    Route::resource('category', CategoryController::class)->only([
        'index', 'store', 'update', 'destroy'
    ]);

    Route::resource('newsletter', NewsletterController::class)->only([
        'index', 'store', 'update', 'destroy'
    ]);

    Route::get('/emails', [MailController::class, 'index']);
    Route::get('/deleteEmail/{id}', [MailController::class, 'deleteEmail']);
    Route::get('/deleteAll', [MailController::class, 'deleteAll']);
    Route::get('/Export', [MailController::class, 'export']);

    Route::post('/AddEmail', [MailController::class, 'AddEmail']);
    Route::post('/EditEmail', [MailController::class, 'EditEmail']);
    Route::post('/import', [MailController::class, 'import']);

    Route::post('/send_emails', [App\Http\Controllers\SendMailController::class, 'send_emails'])->name('send_emails');

    Route::get('/forget-password', [App\Http\Controllers\ForgetPasswordManger::class, 'forgetPassword'])->name('forgetPassword');
    Route::post('/forget-password', [App\Http\Controllers\ForgetPasswordManger::class, 'forgetPasswordPost'])->name('forgetPasswordPost');
    Route::get('/reset-password{token}', [App\Http\Controllers\ForgetPasswordManger::class, 'resetPassword'])->name("resetPassword");
    Route::post('/reset-password', [App\Http\Controllers\ForgetPasswordManger::class, 'resetPasswordPost'])->name("resetPasswordPost");

    Route::get('/refresh-token', [AuthController::class, 'refreshToken'])->name('refreshToken');
});
