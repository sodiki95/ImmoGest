<?php

use App\Http\Controllers\{
    ProfileController,
    PropertyController,
    OwnerController,
    TenantController,
    ContractController,
    RentCallController,
    ReceiptController,
    PaymentController,
    ChargeController,
    DashboardController,
    UserController
};
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => auth()->check() ? redirect()->route('dashboard') : view('auth.login'));

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    Route::resource('properties', PropertyController::class);
    Route::resource('owners', OwnerController::class)->except(['create', 'edit']);
    Route::resource('tenants', TenantController::class)->except(['create', 'edit']);
    Route::resource('contracts', ContractController::class)->except(['create', 'edit']);
    Route::resource('rent-calls', RentCallController::class)->except(['create', 'edit']);
    Route::resource('receipts', ReceiptController::class)->except(['create', 'edit', 'update']);
    Route::resource('payments', PaymentController::class)->except(['create', 'edit']);
    Route::resource('charges', ChargeController::class)->except(['create', 'edit']);

    Route::post('payments/{payment}/pay', [PaymentController::class, 'pay'])->name('payments.pay');
    Route::post('payments/generer-echeances', [PaymentController::class, 'genererEcheances'])->name('payments.generer');
    Route::post('charges/{charge}/payer', [ChargeController::class, 'payer'])->name('charges.payer');

    Route::middleware('permission:users.view')
         ->get('users', [UserController::class, 'index'])->name('users.index');
    Route::middleware('permission:users.create')
         ->post('users', [UserController::class, 'store'])->name('users.store');
    Route::middleware('permission:users.edit')
         ->put('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::middleware('permission:users.delete')
         ->delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

require __DIR__.'/auth.php';
