<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\ReceiptController;


Route::get('/', function () {
    return redirect()->route('login');
});


//Auth Controller
Route::get('login', [AuthController::class, 'create'])->name('login');
Route::get('forgot-password', [AuthController::class, 'forgotPassword'])->name('login.forgot');
Route::post('forgot-password/send-otp', [AuthController::class, 'sendOtp'])->name('forgot.send');
Route::post('forgot-password/verify-otp', [AuthController::class, 'verifyOtp'])->name('forgot.verify');
Route::post('forgot-password/set-password', [AuthController::class, 'changePassword'])->name('forgot.password');
Route::post('login', [AuthController::class, 'store'])->name('login.store');
Route::delete('logout', [AuthController::class, 'destroy'])->name('login.destroy');



Route::middleware([
    'auth',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Backend/Dashboard');
    })->name('dashboard');
});


Route::group(['middleware'=>'auth','prefix' => 'issues'], function () {
    Route::get('', [IssueController::class, 'index'])->name('issues.index');
    Route::get('json-index', [IssueController::class, 'jsonAll'])->name('issues.json-index');
    Route::get('create', [IssueController::class, 'create'])->name('issues.create');
    Route::post('store', [IssueController::class, 'store'])->name('issues.store');
    Route::get('edit/{model}', [IssueController::class, 'edit'])->name('issues.edit');
    Route::put('update/{model}', [IssueController::class, 'update'])->name('issues.update');
    Route::get('{model}/show', [IssueController::class, 'show'])->name('issues.show');
    Route::delete('{model}', [IssueController::class, 'destroy'])->name('issues.destroy');
});



Route::group(['middleware'=>'auth','prefix' => 'receipts'], function () {
    Route::get('', [ReceiptController::class, 'index'])->name('receipts.index');
    Route::get('json-index', [ReceiptController::class, 'jsonAll'])->name('receipts.json-index');
    Route::get('create', [ReceiptController::class, 'create'])->name('receipts.create');
    Route::post('store', [ReceiptController::class, 'store'])->name('receipts.store');
    Route::get('edit/{model}', [ReceiptController::class, 'edit'])->name('receipts.edit');
    Route::put('update/{model}', [ReceiptController::class, 'update'])->name('receipts.update');
    Route::get('{model}/show', [ReceiptController::class, 'show'])->name('receipts.show');
    Route::delete('{model}', [ReceiptController::class, 'destroy'])->name('receipts.destroy');
});
