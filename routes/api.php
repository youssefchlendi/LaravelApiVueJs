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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('/v1')->namespace('App\Http\Controllers')->group(function(){
    Route::post('activities/add','ActivitiesController@store');
    Route::post('activities/{activity_id}/items','ActivitiesController@storeList');

    Route::post('activities','ActivitiesController@show');
    Route::get('activities/{activity_id}','ActivitiesController@getActivityById');

    Route::put('activities/{activity_id}','ActivitiesController@activityUpdate');
    Route::put('activities/{activity_id}/items/{item_id}','ActivitiesController@itemUpdate');

    Route::delete('activities/{activity_id}','ActivitiesController@activityDestroy');
    Route::delete('activities/{activity_id}/items/{item_id}','ActivitiesController@activityItemDestroy');

    Route::get('tags/', 'ActivitiesController@showTags');
    
    Route::post('tags/add', 'ActivitiesController@storeTag');

    Route::delete('tags/{tag_id}', 'ActivitiesController@tagDestroy');

    Route::post('activities/{activity_id}/tags/{tag_id}', 'ActivitiesController@linkTag');
});
