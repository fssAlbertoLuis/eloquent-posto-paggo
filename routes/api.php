<?php

use App\Http\Resources\User\UserResource;
use App\Models\Company\MonthlyStatistics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

Route::post('/auth', 'Auth\LoginController@login');
Route::post('/signup', 'CustomerController@create');

Route::middleware('auth:api')->get('/user', 'UserController@getUser');
Route::middleware('auth:api')->patch('/user/profile_edit', 'UserController@profileEdit');
Route::middleware('auth:api')->get('/logout', 'Auth\LoginController@logout');

Route::middleware(['auth:api', 'scope:vital-admin'])->group(function () {
    Route::post('/company/create', 'CompanyController@create');
    Route::get('/company/view/{id}', 'CompanyController@view');
    Route::get('/bug/{idcliente}/{valor}', 'CompanyController@bug');
});

Route::middleware(['auth:api', 'scope:vital-admin,customer'])->group(function () {
    Route::get('/company/all', 'CompanyController@all');
});

Route::middleware(['auth:api', 'scope:vital-admin,general-manager'])->group(function () {
    Route::get('/user/view/{id}', 'UserController@view');
    Route::post('/user/create', 'UserController@create');
    Route::patch('/user/update/{id}', 'UserController@update');
    Route::get('/user/all', 'UserController@all');
    Route::get('/company/statistics', 'CompanyController@getStatistics');
});

Route::middleware(['auth:api', 'scope:general-manager'])->group(function () {
    Route::get('/company/fuel_list', 'CompanyController@fuelList');
    Route::delete('/company/delete_fuel/{id}', 'CompanyController@deleteFuel');
    Route::post('/company/create_fuel', 'CompanyController@createFuel');
    Route::patch('/company/update_fuel/{id}', 'CompanyController@updateFuel');
});

Route::middleware(['auth:api', 'scope:general-manager,manager,user'])->group(function () {
    Route::get('/purchase/confirm/{id}', 'PurchaseController@confirmPurchase');
    Route::get('/customer/search/{search}', 'CustomerController@search');
    Route::post('/user/recharge_customer', 'CustomerController@rechargeCredits');
    Route::get('/company/recharge_list', 'CompanyController@rechargeList');
});

Route::middleware(['auth:api', 'scope:customer'])->group(function () {
    Route::get('/phone_verify/{code}', 'CustomerController@phoneVerify');
    Route::post('/user/recharge', 'UserHistoryController@rechargeCredits');
    Route::post('/user/purchase', 'UserHistoryController@purchase');
    Route::post('/user/cancel_purchase', 'UserHistoryController@cancelPurchase');
    Route::get('/user/purchase_list', 'UserHistoryController@purchaseList');
    Route::get('/user/recharge_list', 'UserHistoryController@rechargeList');
    Route::get('/user/purchase_get/{id}', 'UserHistoryController@purchaseGet');
    Route::get('/resend_code', 'CustomerController@resendCode');
});

Route::middleware(['auth:api', 'scope:customer,user'])->group(function () {
   Route::get('/purchase/get/{id}', 'PurchaseController@getPurchase');
});


Route::middleware('auth:api')->get('/test', function() {
     return Auth::user()->company->monthly_earnings;
});
