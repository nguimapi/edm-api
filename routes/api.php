<?php

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


Route::post('oauth/token','\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');
Route::post('confirm','User\UserFileController@confirmUpload');

Route::name('me')->get('users/me','User\UserController@me');

Route::resource('users.files', 'User\UserFileController')->only(['index', 'store', 'update', 'destroy']);
Route::resource('users.folders', 'User\UserFolderController')->only(['index', 'show', 'store', 'update', 'destroy']);

