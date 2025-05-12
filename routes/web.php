<?php

use App\Http\Controllers\PurchaseRequestController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BudgetApprovalController;
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
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // User Management Routes - Only accessible to admins
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', \App\Http\Controllers\UserController::class);
    });
    
    // Purchase Request Routes with permissions
    Route::middleware('permission:view purchase requests')->group(function () {
        Route::get('purchase-requests', [PurchaseRequestController::class, 'index'])->name('purchase-requests.index');
    });
    
    Route::middleware('permission:create purchase requests')->group(function () {
        Route::get('purchase-requests/create', [PurchaseRequestController::class, 'create'])->name('purchase-requests.create');
        Route::post('purchase-requests', [PurchaseRequestController::class, 'store'])->name('purchase-requests.store');
        Route::post('purchase-requests/{purchaseRequest}/submit', [PurchaseRequestController::class, 'submit'])
             ->name('purchase-requests.submit');
    });
    
    Route::middleware('permission:view purchase requests')->group(function () {
        Route::get('purchase-requests/{purchaseRequest}', [PurchaseRequestController::class, 'show'])->name('purchase-requests.show');
    });
    
    Route::middleware('permission:edit purchase requests')->group(function () {
        Route::get('purchase-requests/{purchaseRequest}/edit', [PurchaseRequestController::class, 'edit'])->name('purchase-requests.edit');
        Route::put('purchase-requests/{purchaseRequest}', [PurchaseRequestController::class, 'update'])->name('purchase-requests.update');
        Route::patch('purchase-requests/{purchaseRequest}', [PurchaseRequestController::class, 'update']);
    });
    
    Route::middleware('permission:delete purchase requests')->group(function () {
        Route::delete('purchase-requests/{purchaseRequest}', [PurchaseRequestController::class, 'destroy'])->name('purchase-requests.destroy');
    });
    
    Route::middleware('permission:approve purchase requests')->group(function () {
        Route::post('purchase-requests/{purchaseRequest}/approval', [PurchaseRequestController::class, 'processApproval'])
             ->name('purchase-requests.process-approval');
    });
    
    // Budget Approval Routes - Only accessible to approvers and admins
    Route::middleware('role:approver|admin')->group(function () {
        Route::get('budget-approvals', [BudgetApprovalController::class, 'index'])
             ->name('budget-approvals.index');
        Route::get('budget-approvals/create/{purchaseRequest}', [BudgetApprovalController::class, 'create'])
             ->name('budget-approvals.create');
        Route::post('budget-approvals/{purchaseRequest}', [BudgetApprovalController::class, 'store'])
             ->name('budget-approvals.store');
        Route::get('budget-approvals/{budgetApproval}', [BudgetApprovalController::class, 'show'])
             ->name('budget-approvals.show');
    });
    
    // Supplier Routes - Only accessible to procurement officers and admins
    Route::middleware('role:procurement_officer|admin')->group(function () {
        Route::resource('suppliers', SupplierController::class);
    });
});

require __DIR__.'/auth.php';
