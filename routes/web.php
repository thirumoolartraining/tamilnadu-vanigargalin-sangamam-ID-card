<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VanigamController;
use App\Http\Controllers\AdminPanelController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Tamil Nadu Vanigargalin Sangamam - WhatsApp-style chat UI for member registration
|
*/

// ── Main Page (Chat UI) ─────────────────────────────────────────────────
Route::get('/', function () {
    return view('chatbot');
})->name('home');

// ── Member Card View ────────────────────────────────────────────────────
Route::get('/member/card/{uniqueId}', [VanigamController::class, 'showCard'])->name('member.card');

// ── QR: Complete Additional Details ─────────────────────────────────────
Route::get('/member/complete/{uniqueId}', [VanigamController::class, 'completeDetails'])->name('member.complete');

// ── QR: Public Verification ─────────────────────────────────────────────
Route::get('/member/verify/{uniqueId}', [VanigamController::class, 'verifyMember'])->name('member.verify');

// ── Referral Landing Page ──────────────────────────────────────────────
Route::get('/refer/{uniqueId}/{referralId}', [VanigamController::class, 'handleReferral'])->name('referral');

// ── Client-side Card View (reads from localStorage, no MongoDB needed) ──
Route::get('/card-view', function () {
    return view('card.view');
})->name('card.view');

// ── Test Card with Dummy Data ───────────────────────────────────────────
Route::get('/test/card', function () {
    $member = (object) [
        'unique_id'      => 'VNG-A3B7C1D',
        'epic_no'        => 'AYR1454636',
        'name'           => 'SENTHIL KUMAR',
        'membership'     => 'Member',
        'assembly'       => 'Gummidipoondi',
        'district'       => 'Tiruvallur',
        'contact_number' => '+91 8106811285',
        'mobile'         => '8106811285',
        'dob'            => '15/06/1990',
        'age'            => '35',
        'blood_group'    => 'O+',
        'address'        => '12, Main Road, Gummidipoondi, Tiruvallur - 601201',
        'photo_url'      => 'https://res.cloudinary.com/dqndhcmu2/image/upload/v1773487575/vanigan/test/test_1773487573.png',
        'qr_url'         => 'http://localhost:8000/member/verify/VNG-A3B7C1D',
        'details_completed' => true,
    ];
    return view('card.vanigam', [
        'member'       => $member,
        'generated_at' => now()->format('d M Y, h:i A'),
        'base_url'     => config('app.url'),
    ]);
});

// ── Test PIN Button ────────────────────────────────────────────────────
Route::get('/test/pin', function () {
    return response()->file(public_path('test-pin-button.html'));
});

// ── Admin Panel ────────────────────────────────────────────────────────
Route::get('/admin/login', [AdminPanelController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminPanelController::class, 'login'])->name('admin.login.submit');

Route::prefix('admin')->middleware('admin.auth')->group(function () {
    Route::get('/dashboard', [AdminPanelController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/users', [AdminPanelController::class, 'users'])->name('admin.users');
    Route::get('/users/{uniqueId}', [AdminPanelController::class, 'userDetail'])->name('admin.user.detail');
    Route::get('/voters', [AdminPanelController::class, 'voters'])->name('admin.voters');
    Route::get('/voters/{epicNo}', [AdminPanelController::class, 'voterDetail'])->name('admin.voter.detail');
    Route::post('/logout', [AdminPanelController::class, 'logout'])->name('admin.logout');
});