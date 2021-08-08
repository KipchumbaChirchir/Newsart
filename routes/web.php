<?php

use App\Http\Controllers\Payments\Mpesa\V1\STKPushController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return new STKFailedResource(STKFailedTransactions::all());
// })->name('payment');
Route::get('/', function () {
    return view('welcome');
});
Route::get('/payment', function () {
    return view('pages.payment');
});

Route::get('/connectivity', [STKPushController::class, 'ifConnected']);

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
