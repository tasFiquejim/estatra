<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Property\PropertyForm;
use App\Livewire\Property\PropertyList;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return redirect()->route('register');
});

Route::middleware(['auth'])
    ->prefix('v1')
    ->group(function () {
        Route::get('/property-add', PropertyForm::class)->name('property.create');
        Route::get('/property', PropertyList::class)->name('property.index');
        Route::get('/property/{property}/edit', PropertyForm::class)->name('property.edit');
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
