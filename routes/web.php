<?php

use App\Livewire\V1\Unit\UnitForm;
use App\Livewire\V1\Lease\LeaseForm;
use App\Livewire\V1\Lease\LeaseList;
use Illuminate\Support\Facades\Route;
use App\Livewire\V1\Tenant\TenantForm;
use App\Livewire\V1\Tenant\TenantList;
use App\Livewire\V1\Dashboard\Dashboard;
use App\Livewire\V1\Expense\ExpenseForm;
use App\Livewire\V1\Expense\ExpenseList;
use App\Livewire\V1\Payment\PaymentForm;
use App\Livewire\V1\Payment\PaymentList;
use App\Livewire\V1\Property\PropertyForm;
use App\Livewire\V1\Property\PropertyList;
use App\Livewire\V1\Property\PropertyShow;
use App\Http\Controllers\ProfileController;
use App\Livewire\V1\Maintenance\MaintenanceForm;
use App\Livewire\V1\Maintenance\MaintenanceList;

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

        Route::get('/leases', LeaseList::class)->name('lease.index');
        Route::get('/leases/create', LeaseForm::class)->name('lease.create');
        Route::get('/leases/{lease}/edit', LeaseForm::class)->name('lease.edit');

        Route::get('/payments', PaymentList::class)->name('payment.index');
        Route::get('/paymets/create', PaymentForm::class)->name('payment.create');
        Route::get('payments/{payment}/edit', PaymentForm::class)->name('payment.edit');

        Route::get('/expenses', ExpenseList::class)->name('expense.index');
        Route::get('/expenses/create', ExpenseForm::class)->name('expense.create');
        Route::get('expenses/{propertyExpense}/edit', ExpenseForm::class)->name('expense.edit');

        Route::get('/maintenances', MaintenanceList::class)->name('maintenance.index');
        Route::get('/maintenances/create', MaintenanceForm::class)->name('maintenance.create');
        Route::get('/maintenances/{maintenanceLog}/edit', MaintenanceForm::class)->name('maintenance.edit');
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
