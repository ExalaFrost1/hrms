<?php

// routes/api.php (add these routes to your existing api.php file)

use App\Http\Controllers\Api\DiscordAttendanceController;
use App\Http\Controllers\Api\DiscordLeaveController;
use App\Http\Controllers\Api\DiscordUserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Discord Bot API Routes
|--------------------------------------------------------------------------
| These routes replicate the functionality from your Python Discord bot
| Google Sheets integration, but using Laravel and MySQL database.
*/

Route::prefix('discord')->middleware(['api'])->group(function () {

    // Attendance Routes (replaces Google Sheets employee data operations)
    Route::prefix('attendance')->group(function () {
        Route::get('/load', [DiscordAttendanceController::class, 'loadEmployeeData']);
        Route::post('/save', [DiscordAttendanceController::class, 'saveEmployeeData']);
        Route::post('/update-status', [DiscordAttendanceController::class, 'updateAttendanceStatus']);
        Route::get('/today', [DiscordAttendanceController::class, 'getTodayAttendance']);
        Route::get('/stats', [DiscordAttendanceController::class, 'getEmployeeStats']);
    });

    // Leave Management Routes (replaces Google Sheets leave operations)
    Route::prefix('leave')->group(function () {
        Route::post('/request', [DiscordLeaveController::class, 'saveLeaveRequest']);
        Route::put('/update', [DiscordLeaveController::class, 'updateLeaveRequest']);
        Route::get('/requests', [DiscordLeaveController::class, 'getLeaveRequests']);
        Route::get('/balance', [DiscordLeaveController::class, 'getEmployeeLeaveBalance']);
        Route::get('/balance/formatted', [DiscordLeaveController::class, 'getFormattedLeaveBalance']);
        Route::post('/check-balance', [DiscordLeaveController::class, 'checkSufficientBalance']);
        Route::post('/calculate-days', [DiscordLeaveController::class, 'calculateLeaveDays']);
        Route::post('/categorize', [DiscordLeaveController::class, 'categorizeLeaveType']);
        Route::get('/report', [DiscordLeaveController::class, 'generateLeaveReport']);
    });

    // User Management Routes (Discord user mappings)
    Route::prefix('user')->group(function () {
        Route::post('/mapping', [DiscordUserController::class, 'createOrUpdateMapping']);
        Route::get('/mapping', [DiscordUserController::class, 'getMapping']);
        Route::post('/initialize-balances', [DiscordUserController::class, 'initializeYearlyBalances']);
    });
});

/*
|--------------------------------------------------------------------------
| Alternative routes with authentication middleware
|--------------------------------------------------------------------------
| If you want to secure these endpoints, uncomment the lines below
| and add appropriate authentication middleware
*/

/*
Route::prefix('discord')->middleware(['api', 'auth:sanctum'])->group(function () {
    // Same routes as above but with authentication
    // This would require your Discord bot to authenticate with Laravel
});
*/

// =====================================================
// config/leave.php (create this new configuration file)
// =====================================================
