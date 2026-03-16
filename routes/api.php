<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MetaController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\BudgetController;
use App\Http\Controllers\Api\GoalController;
use App\Http\Controllers\Api\GoalInstallmentController;
use App\Http\Controllers\Api\PasswordResetController;

Route::prefix('v1')->group(function () {
    Route::get('/meta/currencies', [MetaController::class, 'currencies']);

    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/forgot-password', [PasswordResetController::class, 'sendResetLink']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        Route::get('/profile', [ProfileController::class, 'show']);
        Route::put('/profile', [ProfileController::class, 'update']);

        Route::get('/dashboard/summary', [DashboardController::class, 'summary']);

        Route::get('/transactions', [TransactionController::class, 'index']);
        Route::post('/transactions', [TransactionController::class, 'store']);
        Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy']);

        Route::get('/budgets/current', [BudgetController::class, 'current']);
        Route::put('/budgets', [BudgetController::class, 'upsert']);

        Route::get('/goals', [GoalController::class, 'index']);
        Route::post('/goals', [GoalController::class, 'store']);
        Route::put('/goals/{goal}', [GoalController::class, 'update']);
        Route::delete('/goals/{goal}', [GoalController::class, 'destroy']);

        Route::get('/goals/{goal}/installments', [GoalInstallmentController::class, 'index']);
        Route::post('/goals/{goal}/installments', [GoalInstallmentController::class, 'store']);
        Route::delete('/goal-installments/{installment}', [GoalInstallmentController::class, 'destroy']);
    });
});
