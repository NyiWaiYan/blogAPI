<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Helper\ResponseHelper;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProfileController;

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

Route::post('register',[AuthController::class,'register']);
Route::post('login',[AuthController::class,'login']);

Route::middleware(['auth:api'])->group(function(){
    //profile
    Route::get('profile',[ProfileController::class,'profile']); 
      Route::get('profile-posts',[ProfileController::class,'posts']); 
    //categories
    Route::get('categories',[CategoryController::class,'index']);
    //post
    Route::get('post',[PostController::class,'index']);
    Route::post('post',[PostController::class,'create']);
    Route::get('post/{id}',[PostController::class,'show']);
    //logout
    Route::post('logout',[AuthController::class,'logout']);


});
