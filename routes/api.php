<?php

use App\Http\Controllers\Payments\Mpesa\V1\MpesaC2BController;
use App\Http\Controllers\Payments\Mpesa\V1\STKPushController;
use App\Http\Controllers\SMS\ORACOM\SMSController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
// Imdex Page
Route::get('/', function(){
    return view('welcome');
});

// STK Push
Route::post('/access/token', [STKPushController::class, 'generateAccessToken'])->name('access.token');
Route::post('/lipa/password', [STKPushController::class, 'lipaNaMpesaPassword'])->name('lipa.password');
Route::post('/stk/push', [STKPushController::class, 'customerMpesaSTKPush'])->name('stk.push');
Route::post('/callback/url', [STKPushController::class, 'mpesaResponse'])->name('callback.url');

// Lipa na Mpesa
Route::post('/register/url', [MpesaC2BController::class, 'mpesaRegisterUrls']);
Route::post('/transaction/confirmation', [MpesaC2BController::class, 'mpesaConfirmation']);
Route::post('/validation', [MpesaC2BController::class, 'mpesaValidation']);

// Send SMS
Route::post('/sms/send', [SMSController::class, 'sendSMS']);
