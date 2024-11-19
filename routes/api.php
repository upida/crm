<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ManagerController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function() {

    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'update']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::group(['prefix' => 'company', 'middleware' => [RoleMiddleware::class.':admin']], function() {
        Route::get('/', [CompanyController::class, 'index']);
        Route::post('/', [CompanyController::class, 'store']);
        Route::get('/{company}', [CompanyController::class, 'show']);
        Route::put('/{company}', [CompanyController::class, 'update']);
        Route::delete('/{company}', [CompanyController::class, 'destroy']);
    });

    Route::group(['prefix' => '/{company}/employee', 'middleware' => [RoleMiddleware::class.':admin,manager,employee']], function() {
        Route::get('/', [EmployeeController::class, 'index']);
        Route::get('/{employee}', [EmployeeController::class, 'show']);
    });

    Route::group(['prefix' => '/{company}', 'middleware' => [RoleMiddleware::class.':admin,manager']], function() {
        Route::group(['prefix' => '/manager'], function() {
            Route::get('/', [ManagerController::class, 'index']);
            Route::get('/{manager}', [ManagerController::class, 'show']);
            Route::put('/{manager}', [ManagerController::class, 'update']);
        });
        
        Route::group(['prefix' => '/employee'], function() {
            Route::post('/', [EmployeeController::class, 'store']);
            Route::put('/{employee}', [EmployeeController::class, 'update']);
            Route::delete('/{employee}', [EmployeeController::class, 'destroy']);
        });
    });

});

