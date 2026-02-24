<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

use App\Models\User;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Password reset
    Route::get('/forgot-password',          [ForgotPasswordController::class, 'show'])->name('password.request');
    Route::post('/forgot-password',         [ForgotPasswordController::class, 'send'])->name('password.email');
    Route::get('/reset-password/{token}',   [ResetPasswordController::class, 'show'])->name('password.reset');
    Route::post('/reset-password',          [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Products
    Route::resource('products', ProductController::class);

    // Sales
    Route::resource('sales', SaleController::class)->except(['edit', 'update']);
    Route::patch('sales/{sale}/status', [SaleController::class, 'updateStatus'])->name('sales.update-status');

    // Customers
    Route::resource('customers', CustomerController::class);

    // Users (admin only)
    Route::resource('users', UserController::class)
        ->except(['show'])
        ->middleware('role:admin');
});

