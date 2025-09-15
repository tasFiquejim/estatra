<?php

use App\Livewire\V1\Unit\UnitForm;
use App\Livewire\V1\Lease\LeaseForm;
use App\Livewire\V1\Lease\LeaseList;
use Illuminate\Support\Facades\Route;
use App\Livewire\V1\Tenant\TenantForm;
use App\Livewire\V1\Tenant\TenantList;
use App\Livewire\V1\Expense\ExpenseForm;
use App\Livewire\V1\Expense\ExpenseList;
use App\Livewire\V1\Payment\PaymentForm;
use App\Livewire\V1\Payment\PaymentList;
use App\Livewire\V1\Property\PropertyForm;
use App\Livewire\V1\Property\PropertyList;
use App\Livewire\V1\Property\PropertyShow;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])
    ->prefix('v1')
    ->group(function () {
        Route::get('/property-add', PropertyForm::class)->name('property.create');
        Route::get('/property/{property}', PropertyShow::class)->name('property.show');
        Route::get('/property', PropertyList::class)->name('property.index');
        Route::get('/property/{property}/edit', PropertyForm::class)->name('property.edit');

        Route::get('/property/{property}/unit-add', UnitForm::class)->name('unit.create');
        Route::get('/property/{property}/unit/{unit}/edit', UnitForm::class)->name('unit.edit');

        Route::get('/tenant-add', TenantForm::class)->name('tenant.create');
        Route::get('/tenant/{tenant}/edit', TenantForm::class)->name('tenant.edit');
        Route::get('/tenants', TenantList::class)->name('tenant.index');

        Route::get('/lease-add', LeaseForm::class)->name('lease.create');
        Route::get('/lease/{lease}/edit', LeaseForm::class)->name('lease.edit');
        Route::get('/leases', LeaseList::class)->name('lease.index');

        Route::get('/add-payment', PaymentForm::class)->name('payment.create');
        Route::get('payment/{payment}/edit', PaymentForm::class)->name('payment.edit');
        Route::get('/payments', PaymentList::class)->name('payment.index');

        Route::get('/add-expense', ExpenseForm::class)->name('expense.create');
        Route::get('expense/{expense}/edit', ExpenseForm::class)->name('expense.edit');
        Route::get('/expenses', ExpenseList::class)->name('expense.index');
    });
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
