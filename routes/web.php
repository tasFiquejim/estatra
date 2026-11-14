<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReceiptController;
use App\Livewire\V1\Dashboard\Dashboard;
use App\Livewire\V1\Expense\ExpenseForm;
use App\Livewire\V1\Expense\ExpenseList;
use App\Livewire\V1\Lease\LeaseForm;
use App\Livewire\V1\Lease\MoveIn;
use App\Livewire\V1\Lease\MoveOut;
use App\Livewire\V1\Payment\PaymentForm;
use App\Livewire\V1\Payment\PaymentList;
use App\Livewire\V1\Property\PropertyForm;
use App\Livewire\V1\Property\PropertyList;
use App\Livewire\V1\Property\PropertyShow;
use App\Livewire\V1\Rent\RentRoll;
use App\Livewire\V1\Tenant\TenantForm;
use App\Livewire\V1\Tenant\TenantList;
use App\Livewire\V1\Unit\UnitForm;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])
    ->prefix('v1')
    ->group(function () {
        Route::get('/dashboard', Dashboard::class)->name('dashboard');

        Route::get('/properties', PropertyList::class)->name('property.index');
        Route::get('/properties/create', PropertyForm::class)->name('property.create');
        Route::get('/properties/{property}', PropertyShow::class)->name('property.show');
        Route::get('/properties/{property}/edit', PropertyForm::class)->name('property.edit');

        Route::get('/properties/{property}/units/create', UnitForm::class)->name('unit.create');
        Route::get('/properties/{property}/units/{unit}/edit', UnitForm::class)->name('unit.edit');

        Route::get('/tenants', TenantList::class)->name('tenant.index');
        Route::get('/tenants/create', TenantForm::class)->name('tenant.create');
        Route::get('/tenants/{tenant}/edit', TenantForm::class)->name('tenant.edit');

        // Tenants move in from a free unit and out from their lease (no separate Leases screen).
        Route::get('/properties/{property}/units/{unit}/move-in', MoveIn::class)->name('unit.move-in');
        Route::get('/leases/{lease}/move-out', MoveOut::class)->name('lease.move-out');
        Route::get('/leases/{lease}/edit', LeaseForm::class)->name('lease.edit');

        Route::get('/rent', RentRoll::class)->name('rent.index');

        Route::get('/payments', PaymentList::class)->name('payment.index');
        Route::get('/payments/create', PaymentForm::class)->name('payment.create');
        Route::get('payments/{payment}/edit', PaymentForm::class)->name('payment.edit');
        Route::get('payments/{payment}/receipt', [ReceiptController::class, 'download'])->name('payment.receipt');

        Route::get('/expenses', ExpenseList::class)->name('expense.index');
        Route::get('/expenses/create', ExpenseForm::class)->name('expense.create');
        Route::get('expenses/{propertyExpense}/edit', ExpenseForm::class)->name('expense.edit');
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
