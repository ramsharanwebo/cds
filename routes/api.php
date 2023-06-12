<?php

use App\Http\Controllers\AttachPermissionsController;
use App\Http\Controllers\CDSUserController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DucketController;
use App\Http\Controllers\GenericEventController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TicketController;
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
    Route::get('/login', 'AuthController@login');
    Route::get('/callback', 'AuthController@callback');
    Route::get('/logout', 'AuthController@logout');

    // roles
    Route::group(['prefix'=>'roles'], function(){
        Route::get('/', [RoleController::class, 'index']);
        Route::get('/{id}', [RoleController::class, 'show']);
        Route::post('/', [RoleController::class, 'create']);
        Route::put('/{id}', [RoleController::class, 'update']);
        Route::delete('/{id}', [RoleController::class, 'delete']);
    });

    //permissions
    Route::group(['prefix'=>'permissions'], function(){
        Route::get('/', [PermissionController::class, 'index']);
        Route::get('/{id}', [PermissionController::class, 'show']);
        Route::post('/', [PermissionController::class, 'create']);
        Route::put('/{id}', [PermissionController::class, 'update']);
        Route::delete('/{id}', [PermissionController::class, 'delete']);
    });
    

    //users
    Route::group(['prefix'=>'users'], function(){
        Route::get('/', [CDSUserController::class, 'index']);
        Route::get('/{id}', [CDSUserController::class, 'show']);
        Route::post('/', [CDSUserController::class, 'create']);
        Route::put('/{id}', [CDSUserController::class, 'update']);
        Route::delete('/{id}', [CDSUserController::class, 'delete']);
    });
    
    //attach permissions to role
    Route::post('/attach-permissions-to-role', [AttachPermissionsController::class, 'attachPermissionToRole']);
    Route::post('/detach-permissions-to-role', [AttachPermissionsController::class, 'detachPermissionToRole']);

    //attach permissions to user
    Route::post('/attach-permissions-to-user', [AttachPermissionsController::class, 'attachPermissionToUser']);
    Route::post('/detach-permissions-to-user', [AttachPermissionsController::class, 'detachPermissionToUser']);

    // customers
    Route::group(['prefix'=>'customers'], function(){
        Route::get('/', [CustomerController::class, 'index']);
        Route::get('/{id}', [CustomerController::class, 'show']);
        Route::post('/', [CustomerController::class, 'create']);
        Route::put('/{id}', [CustomerController::class, 'update']);
        Route::delete(' /{id}', [CustomerController::class, 'delete']);

    });
    
    //Locations
    // Route::group(['prefix'=>'locations'], function(){
    //     Route::get('/', [LocationController::class, 'index']);
    //     Route::get('/{id}', [LocationController::class, 'show']);
    //     Route::post('/', [LocationController::class, 'create']);
    //     Route::put('/{id}', [LocationController::class, 'update']);
    //     Route::delete('/{id}', [LocationController::class, 'delete']);
    // });

    // //assign user to location
    // Route::post('/assign-location-to-user', [LocationController::class, 'assignLocationToUser']);
    // Route::post('/remove-location-to-user', [LocationController::class, 'removeLocationToUser']);


    // //Duckets
    // Route::group(['prefix'=>'duckets'], function(){
    //     Route::get('/', [DucketController::class, 'index']);
    //     Route::get('/{id}', [DucketController::class, 'show']);
    //     Route::post('/', [DucketController::class, 'create']);
    //     Route::put('/{id}', [DucketController::class, 'update']);
    //     Route::delete('/{id}', [DucketController::class, 'delete']);
    // });

    //tickets
    // Route::group(['prefix'=>'tickets'], function(){
    //     Route::get('/', [TicketController::class, 'index']);
    //     Route::get('/{id}', [TicketController::class, 'show']);
    //     Route::post('/', [TicketController::class, 'create']);
    //     Route::put('/{id}', [TicketController::class, 'update']);
    //     Route::delete('/{id}', [TicketController::class, 'delete']);
    // });

    Route::post('/send-event-data', [GenericEventController::class, 'sendEventLog']);
});
