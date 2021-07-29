<?php

use App\Http\Controllers\PostController;
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
Route::get('/posts',[PostController::class,'index']);
Route::post('/post-request',[PostController::class,'postRequest']);
Route::put('/put-request',[PostController::class,'putRequest']);
Route::patch('/patch-request',[PostController::class,'patchRequest']);
Route::delete('/delete-request',[PostController::class,'deleteRequest']);
Route::post('/modify-headers',[PostController::class,'modifyHeaders']);
Route::post('/request-with-query-params',[PostController::class,'requestWithQueryParams']);



Route::post('/store',[PostController::class,'store']);
Route::get('/find/{id}',[PostController::class,'find']);
Route::put('/update/{id}',[PostController::class,'update']);
Route::post('/save/{id}',[PostController::class,'save']);
