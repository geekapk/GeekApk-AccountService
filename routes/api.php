<?php

use Illuminate\Http\Request;
use App\Article;

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

Route::post('account/login', 'AccountController@login');
Route::post('account/register', 'AccountController@register');
Route::post('account/verify_email', 'AccountController@verify_email');
Route::post('account/info', 'AccountController@info');
