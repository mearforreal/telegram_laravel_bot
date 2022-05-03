<?php

use App\Http\Controllers\TelegramWebHookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Telegram\Bot\Laravel\Facades\Telegram;

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
//    $update = Telegram::commandsHandler(true);
Route::post('/Q6GXXvKXpUlSlJSY1eci/webhook',[TelegramWebHookController::class,'index']);

Route::get('/', function () {
    return view('welcome');
});
