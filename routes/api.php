<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\dataController;
use App\Http\Controllers\chatController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('registeruser', 'registeruser');
    Route::post('registerexpert', 'registerexpert');
    Route::post('logout', 'logout');
   // Route::post('refresh', 'refresh');
    Route::post('isvalid', 'isvalid');
    Route::post('converttouser', 'converttouser');

});

Route::controller(dataController::class)->group(function () {
    Route::get('medical', 'medical');
    Route::get('business', 'business');
    Route::get('vocational', 'vocational');
    Route::get('family', 'family');
    Route::get('psychological', 'psychological');
    Route::get('expertdetails', 'expertdetails');
    Route::get('profiledetails', 'profiledetails');
    Route::get('highrating', 'highrating');
    Route::post('editprofile', 'editprofile');
    Route::get('searchall/{name}','searchall');
    Route::post('search','search');
    Route::post('getexperttime','getexperttime');
    Route::post('reserve','reserve');
    Route::post('getreservedtime','getreservedtime');
    Route::post('getreservedtimeforuser','getreservedtimeforuser');
    Route::post('addtofavourite','addtofavourite');
    Route::post('checkfavourite','checkfavourite');
    Route::post('getlistfavourite','getlistfavourite');
    Route::post('rate','rate');
    Route::post('checkisrated','checkisrated');
    Route::post('gettotalexperttime','gettotalexperttime');
});



Route::controller(chatController::class)->group(function () {
    Route::post('send','send');
    Route::post('getchats','getchats');
    Route::post('getmessagesofchat','getmessagesofchat');
    Route::post('setchat','setchat');
});
