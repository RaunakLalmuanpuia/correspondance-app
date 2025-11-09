<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StatisticsController;




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
    Route::get('', [IssueController::class, 'index'])->middleware('can:view-issue')->name('issues.index');
    Route::get('json-index', [IssueController::class, 'jsonAll'])->middleware('can:view-issue')->name('issues.json-index');
    Route::get('create', [IssueController::class, 'create'])->middleware('can:create-issue')->name('issues.create');
    Route::post('store', [IssueController::class, 'store'])->middleware('can:create-issue')->name('issues.store');
    Route::get('edit/{model}', [IssueController::class, 'edit'])->middleware('can:edit-issue')->name('issues.edit');
    Route::put('update/{model}', [IssueController::class, 'update'])->middleware('can:edit-issue')->name('issues.update');
    Route::get('{model}/show', [IssueController::class, 'show'])->middleware('can:view-issue')->name('issues.show');
    Route::delete('{model}', [IssueController::class, 'destroy'])->middleware('can:delete-issue')->name('issues.destroy');

    Route::get('/import', [IssueController::class, 'import'])->name('issues.import');
    Route::post('/import-issue', [IssueController::class, 'importIssue'])->name('issues.import-issue');

    Route::get('export', [IssueController::class, 'export'])->name('issues.export');
    Route::get('/export-issue', [IssueController::class, 'exportIssue'])->name('issues.export-issue');

});



Route::group(['middleware'=>'auth','prefix' => 'receipts'], function () {
    Route::get('', [ReceiptController::class, 'index'])->middleware('can:view-receipt')->name('receipts.index');
    Route::get('json-index', [ReceiptController::class, 'jsonAll'])->middleware('can:view-receipt')->name('receipts.json-index');
    Route::get('create', [ReceiptController::class, 'create'])->middleware('can:create-receipt')->name('receipts.create');
    Route::post('store', [ReceiptController::class, 'store'])->middleware('can:create-receipt')->name('receipts.store');
    Route::get('edit/{model}', [ReceiptController::class, 'edit'])->middleware('can:edit-receipt')->name('receipts.edit');
    Route::put('update/{model}', [ReceiptController::class, 'update'])->middleware('can:edit-receipt')->name('receipts.update');
    Route::get('{model}/show', [ReceiptController::class, 'show'])->middleware('can:view-receipt')->name('receipts.show');
    Route::delete('{model}', [ReceiptController::class, 'destroy'])->middleware('can:delete-receipt')->name('receipts.destroy');

    Route::get('/import', [ReceiptController::class, 'import'])->name('receipts.import');
    Route::post('/import-receipt', [ReceiptController::class, 'importReceipt'])->name('receipts.import-receipt');

    Route::get('export', [ReceiptController::class, 'export'])->name('receipts.export');
    Route::get('/export-receipt', [ReceiptController::class, 'exportReceipt'])->name('receipts.export-receipt');
});

Route::group(['middleware'=>'auth','prefix' => 'user'], function () {
    Route::get('', [UserController::class, 'index'])->middleware('can:view-user')->name('user.index');
    Route::get('json-index', [UserController::class, 'jsonAll'])->middleware('can:view-user')->name('user.json-index');
    Route::get('create', [UserController::class, 'create'])->middleware('can:create-user')->name('user.create');
    Route::post('store', [UserController::class, 'store'])->middleware('can:create-user')->name('user.store');
    Route::get('edit/{model}', [UserController::class, 'edit'])->middleware('can:edit-user')->name('user.edit');
    Route::put('update/{model}', [UserController::class, 'update'])->middleware('can:edit-user')->name('user.update');
    Route::get('{model}/show', [UserController::class, 'show'])->middleware('can:view-user')->name('user.show');
    Route::delete('{model}', [UserController::class, 'destroy'])->middleware('can:delete-user')->name('user.destroy');
});


Route::group(['middleware'=>'auth','prefix'=>'profile'], function () {
    Route::get('edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('edit-password', [ProfileController::class, 'editPassword'])->name('profile.edit-password');
    Route::put('update-password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
});


Route::group(['middleware'=>'auth','prefix'=>'statistics'], function () {
    Route::get('stat-cards', [StatisticsController::class, 'statCards'])->name('statCards');
    Route::get('bar-chart', [StatisticsController::class, 'barChart'])->name('barChart');
    Route::get('timeline', [StatisticsController::class, 'timeline'])->name('timeline');
});
