<?php

use Illuminate\Support\Facades\Route;
use App\Interfaces\AuthControllerInterface;

// Auth routes
Route::post('/login', [AuthControllerInterface::class, 'login'])->name('login');
Route::post('/register', [AuthControllerInterface::class, 'register'])->name('register');
Route::post('/logout', [AuthControllerInterface::class, 'logout'])->middleware('auth:sanctum')->name('logout');
Route::post('/me', [AuthControllerInterface::class, 'me'])->middleware('auth:sanctum')->name('me');
