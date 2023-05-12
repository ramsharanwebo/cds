<?php

use App\Http\Controllers\AttachPermissionsController;
use App\Http\Controllers\CDSUserController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DucketController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
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


Route::group(['prefix'=> 'v1'], function(){
    // roles
    Route::get('/roles', [RoleController::class, 'index']);
    Route::get('/roles/{id}', [RoleController::class, 'show']);
    Route::post('/roles', [RoleController::class, 'create']);
    Route::put('/roles/{id}', [RoleController::class, 'update']);
    Route::delete('/roles/{id}', [RoleController::class, 'delete']);

    //permissions
    Route::get('/permissions', [PermissionController::class, 'index']);
    Route::get('/permissions/{id}', [PermissionController::class, 'show']);
    Route::post('/permissions', [PermissionController::class, 'create']);
    Route::put('/permissions/{id}', [PermissionController::class, 'update']);
    Route::delete('/permissions/{id}', [PermissionController::class, 'delete']);

    //users
    Route::get('/users', [CDSUserController::class, 'index']);
    Route::get('/users/{id}', [CDSUserController::class, 'show']);
    Route::post('/users', [CDSUserController::class, 'create']);
    Route::put('/users/{id}', [CDSUserController::class, 'update']);
    Route::delete('/users/{id}', [CDSUserController::class, 'delete']);

    //attach permissions to role
    Route::post('/attach-permissions-to-role', [AttachPermissionsController::class, 'attachPermissionToRole']);
    Route::post('/detach-permissions-to-role', [AttachPermissionsController::class, 'detachPermissionToRole']);

    //attach permissions to user
    Route::post('/attach-permissions-to-user', [AttachPermissionsController::class, 'attachPermissionToUser']);
    Route::post('/detach-permissions-to-user', [AttachPermissionsController::class, 'detachPermissionToUser']);

    // customers
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::get('/customers/{id}', [CustomerController::class, 'show']);
    Route::post('/customers', [CustomerController::class, 'create']);
    Route::put('/customers/{id}', [CustomerController::class, 'update']);
    Route::delete('/customers/{id}', [CustomerController::class, 'delete']);

    //Locations
    Route::group(['prefix'=>'locations'], function(){
        Route::get('/', [LocationController::class, 'index']);
        Route::get('/{id}', [LocationController::class, 'show']);
        Route::post('/', [LocationController::class, 'create']);
        Route::put('/{id}', [LocationController::class, 'update']);
        Route::delete('/{id}', [LocationController::class, 'delete']);
    });

    //assign user to location
    Route::post('/assign-location-to-user', [LocationController::class, 'assignLocationToUser']);
    Route::post('/remove-location-to-user', [LocationController::class, 'removeLocationToUser']);


    //Duckets
    Route::group(['prefix'=>'duckets'], function(){
        Route::get('/', [DucketController::class, 'index']);
        Route::get('/{id}', [DucketController::class, 'show']);
        Route::post('/', [DucketController::class, 'create']);
        Route::put('/{id}', [DucketController::class, 'update']);
        Route::delete('/{id}', [DucketController::class, 'delete']);
    });
});
