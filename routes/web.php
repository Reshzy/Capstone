<?php

use App\Http\Controllers\PurchaseRequestController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Purchase Request Routes
    Route::resource('purchase-requests', PurchaseRequestController::class);
    Route::post('purchase-requests/{purchaseRequest}/submit', [PurchaseRequestController::class, 'submit'])->name('purchase-requests.submit');
    Route::post('purchase-requests/{purchaseRequest}/approval', [PurchaseRequestController::class, 'processApproval'])->name('purchase-requests.process-approval');
    
    // Supplier Routes
    Route::resource('suppliers', SupplierController::class);
});

require __DIR__.'/auth.php';
