<?php

use App\Http\Controllers\PurchaseRequestController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BudgetApprovalController;
use App\Http\Controllers\RequestForQuotationController;
use App\Http\Controllers\SupplierQuotationController;
use App\Http\Controllers\AbstractOfQuotationController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\DisbursementVoucherController;
use App\Http\Controllers\ProcurementCategoryController;
use App\Http\Controllers\SupplierPerformanceController;
use App\Http\Controllers\ReportController;
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
        Route::post('suppliers/{supplier}/toggle-status', [SupplierController::class, 'toggleStatus'])
            ->name('suppliers.toggle-status');
            
        // Procurement Category Routes
        Route::resource('procurement-categories', ProcurementCategoryController::class);
        
        // Supplier Performance Routes
        Route::resource('supplier-performances', SupplierPerformanceController::class);
        Route::get('suppliers/{supplier}/performances', [SupplierPerformanceController::class, 'index'])
            ->name('suppliers.performances');
        Route::get('suppliers/{supplier}/performances/create', [SupplierPerformanceController::class, 'create'])
            ->name('suppliers.performances.create');
        Route::get('suppliers/{supplier}/performance-summary', [SupplierPerformanceController::class, 'summary'])
            ->name('suppliers.performance-summary');
    });
    
    // RFQ Routes - Only accessible to procurement officers and admins
    Route::middleware('role:procurement_officer|admin')->group(function () {
        Route::get('rfq', [RequestForQuotationController::class, 'index'])->name('rfq.index');
        Route::get('rfq/create', [RequestForQuotationController::class, 'create'])->name('rfq.create');
        Route::post('rfq', [RequestForQuotationController::class, 'store'])->name('rfq.store');
        Route::get('rfq/{rfq}', [RequestForQuotationController::class, 'show'])->name('rfq.show');
        Route::get('rfq/{rfq}/edit', [RequestForQuotationController::class, 'edit'])->name('rfq.edit');
        Route::put('rfq/{rfq}', [RequestForQuotationController::class, 'update'])->name('rfq.update');
        Route::delete('rfq/{rfq}', [RequestForQuotationController::class, 'destroy'])->name('rfq.destroy');
        Route::post('rfq/{rfq}/publish', [RequestForQuotationController::class, 'publish'])->name('rfq.publish');
        Route::post('rfq/{rfq}/close', [RequestForQuotationController::class, 'close'])->name('rfq.close');
    });
    
    // Supplier Quotation Routes
    Route::middleware('role:procurement_officer|admin')->group(function () {
        Route::resource('supplier-quotations', SupplierQuotationController::class);
        Route::post('supplier-quotations/{supplierQuotation}/award', [SupplierQuotationController::class, 'award'])->name('supplier-quotations.award');
    });
    
    // Abstract of Quotation Routes
    Route::middleware('role:procurement_officer|admin')->group(function () {
        Route::resource('abstract-of-quotations', AbstractOfQuotationController::class);
        Route::post('abstract-of-quotations/{abstractOfQuotation}/approve', [AbstractOfQuotationController::class, 'approve'])->name('abstract-of-quotations.approve');
    });
    
    // Purchase Order Routes
    Route::middleware('role:procurement_officer|admin')->group(function () {
        Route::resource('purchase-orders', PurchaseOrderController::class);
        Route::get('purchase-orders/create/{abstractOfQuotation}', [PurchaseOrderController::class, 'create'])->name('purchase-orders.create');
        Route::post('purchase-orders/{purchaseOrder}/approve', [PurchaseOrderController::class, 'approve'])->name('purchase-orders.approve');
        Route::post('purchase-orders/{purchaseOrder}/deliver', [PurchaseOrderController::class, 'markDelivered'])->name('purchase-orders.deliver');
    });
    
    // Disbursement Voucher Routes
    Route::middleware('role:approver|admin')->group(function () {
        Route::resource('disbursement-vouchers', DisbursementVoucherController::class);
        Route::get('disbursement-vouchers/create/{purchaseOrder}', [DisbursementVoucherController::class, 'create'])->name('disbursement-vouchers.create');
        Route::post('disbursement-vouchers/{disbursementVoucher}/approve', [DisbursementVoucherController::class, 'approve'])->name('disbursement-vouchers.approve');
        Route::post('disbursement-vouchers/{disbursementVoucher}/pay', [DisbursementVoucherController::class, 'markPaid'])->name('disbursement-vouchers.pay');
    });
    
    // Document Generation Routes
    Route::middleware('role:procurement_officer|admin')->group(function () {
        // PDF generation routes
        Route::get('documents/rfq/{rfq}', [DocumentController::class, 'generateRFQ'])->name('documents.rfq');
        Route::get('documents/aoq/{aoq}', [DocumentController::class, 'generateAOQ'])->name('documents.aoq');
        Route::get('documents/po/{po}', [DocumentController::class, 'generatePO'])->name('documents.po');
        Route::get('documents/dv/{disbursementVoucher}', [DocumentController::class, 'generateDV'])->name('documents.dv');
    });
    
    // Reporting Routes - Only accessible to admin and procurement officers
    Route::middleware('role:admin|procurement_officer')->group(function () {
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/purchase-requests', [ReportController::class, 'purchaseRequests'])->name('reports.purchase-requests');
        Route::get('reports/budget-approvals', [ReportController::class, 'budgetApprovals'])->name('reports.budget-approvals');
        Route::get('reports/suppliers', [ReportController::class, 'suppliers'])->name('reports.suppliers');
        Route::post('reports/generate-pdf', [ReportController::class, 'generatePdf'])->name('reports.generate-pdf');
        
        // Activity Log
        Route::get('reports/activity-log', [ReportController::class, 'activityLog'])->name('reports.activity-log');
    });
});

require __DIR__.'/auth.php';
