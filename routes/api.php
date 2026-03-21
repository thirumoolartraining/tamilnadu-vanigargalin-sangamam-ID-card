<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VanigamController;

/*
|--------------------------------------------------------------------------
| API Routes - Tamil Nadu Vanigargalin Sangamam
|--------------------------------------------------------------------------
*/

// Health check (no throttle - monitoring/internal use)
Route::get('/health', [VanigamController::class, 'health']);

// Tamil Nadu Vanigargalin Sangamam API
Route::prefix('vanigam')->group(function () {
    // === OTP Endpoints - DDoS + Brute Force Protection ===
    // Rate limit: 50 per 5 minutes
    Route::post('/check-member', [VanigamController::class, 'checkMember'])
        ->middleware('throttle:50,5');

    Route::post('/send-otp', [VanigamController::class, 'sendOtp'])
        ->middleware('throttle:50,5');

    Route::post('/verify-otp', [VanigamController::class, 'verifyOtp'])
        ->middleware('throttle:50,5');

    // === Validation Endpoints - Uploads & Lookups ===
    // Rate limit: 40 per 5 minutes
    Route::post('/validate-epic', [VanigamController::class, 'validateEpic'])
        ->middleware('throttle:40,5');

    Route::post('/upload-photo', [VanigamController::class, 'uploadPhoto'])
        ->middleware('throttle:40,5');

    Route::post('/validate-photo', [VanigamController::class, 'validatePhotoUpload'])
        ->middleware('throttle:40,5');

    // === Card Generation - Resource Intensive ===
    // Rate limit: 15 per 5 minutes
    Route::post('/generate-card', [VanigamController::class, 'generateCard'])
        ->middleware('throttle:15,5');

    Route::post('/save-details', [VanigamController::class, 'saveAdditionalDetails'])
        ->middleware('throttle:15,5');

    // === Member Read Operations - Lightweight ===
    // Rate limit: 200 per 1 minute
    Route::get('/member/{uniqueId}', [VanigamController::class, 'getMember'])
        ->middleware('throttle:200,1');

    Route::get('/qr/{uniqueId}', [VanigamController::class, 'generateQr'])
        ->middleware('throttle:200,1');

    // === Admin Protected Endpoints - API Key + Secondary Rate Limit ===
    // Rate limit: 10 per 5 minutes
    Route::post('/reset-members', [VanigamController::class, 'resetMembers'])
        ->middleware([
            'validate.admin.api.key',
            'throttle:10,5',
        ]);

    Route::post('/upload-card-images', [VanigamController::class, 'uploadCardImages'])
        ->middleware([
            'validate.admin.api.key',
            'throttle:10,5',
        ]);

    // === PIN Verification - Brute Force Protection ===
    // Rate limit: 10 per 5 minutes
    Route::post('/verify-pin', [VanigamController::class, 'verifyPin'])
        ->middleware('throttle:10,5');

    Route::post('/verify-member-pin', [VanigamController::class, 'verifyMemberPin'])
        ->middleware('throttle:10,5');

    // === Referral & Loan - Standard User Operations ===
    // Rate limit: 30 per 5 minutes
    Route::post('/get-referral', [VanigamController::class, 'getReferral'])
        ->middleware('throttle:30,5');

    Route::post('/increment-referral', [VanigamController::class, 'incrementReferral'])
        ->middleware('throttle:30,5');

    Route::post('/loan-request', [VanigamController::class, 'loanRequest'])
        ->middleware('throttle:30,5');

    Route::post('/check-loan-status', [VanigamController::class, 'checkLoanStatus'])
        ->middleware('throttle:30,5');
});
