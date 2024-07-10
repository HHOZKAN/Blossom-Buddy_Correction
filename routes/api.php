<?php

use App\Http\Controllers\PlantController;
use App\Http\Controllers\UserPlantController;
use Illuminate\Support\Facades\Route;
use App\Interfaces\AuthControllerInterface;

// Auth routes
Route::post('/login', [AuthControllerInterface::class, 'login'])->name('login');
Route::post('/register', [AuthControllerInterface::class, 'register'])->name('register');
Route::post('/logout', [AuthControllerInterface::class, 'logout'])->middleware('auth:sanctum')->name('logout');
Route::post('/me', [AuthControllerInterface::class, 'me'])->middleware('auth:sanctum')->name('me');


// Plant routes
Route::prefix('plants')->group(function () {
    Route::get('/', [PlantController::class, 'index'])->name('plants.index');
    Route::post('/', [PlantController::class, 'store'])->name('plants.store');
    Route::get('/{common_name}', [PlantController::class, 'show'])->name('plants.show');
    Route::put('/{common_name}', [PlantController::class, 'update'])->name('plants.update');
    Route::delete('/{common_name}', [PlantController::class, 'destroy'])->name('plants.destroy');
});


// User_Plant routes (routes where the user interact with plants)
Route::prefix('user/plant')->group(function () {
    Route::post('/', [UserPlantController::class, 'addPlantUser'])->name('user.plant.addPlantUser')->middleware('auth:sanctum');
    Route::delete('/{id}', [UserPlantController::class, 'deletePlantUser'])->name('user.plant.deletePlantUser')->middleware('auth:sanctum');
});
Route::get('/user/plants', [UserPlantController::class, 'getPlantsUser'])->name('user.plant.getPlantsUser')->middleware('auth:sanctum');