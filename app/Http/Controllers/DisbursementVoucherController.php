<?php

namespace App\Http\Controllers;

use App\Models\DisbursementVoucher;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class DisbursementVoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $disbursementVouchers = DisbursementVoucher::with(['purchaseOrder', 'creator', 'approver', 'paidBy'])
            ->latest()
            ->paginate(10);
            
        return view('disbursement-vouchers.index', compact('disbursementVouchers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(PurchaseOrder $purchaseOrder)
    {
        // Check if the PO is approved
        if (!$purchaseOrder->approved_by) {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'The Purchase Order must be approved before creating a Disbursement Voucher.');
        }
        
        // Check if a DV already exists for this PO
        $existingDV = DisbursementVoucher::where('purchase_order_id', $purchaseOrder->id)->first();
        if ($existingDV) {
            return redirect()->route('disbursement-vouchers.show', $existingDV)
                ->with('error', 'A Disbursement Voucher already exists for this Purchase Order.');
        }
        
        // Load the purchase order relationships
        $purchaseOrder->load(['supplierQuotation.supplier', 'abstractOfQuotation.requestForQuotation.purchaseRequest']);
        
        return view('disbursement-vouchers.create', compact('purchaseOrder'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'dv_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0',
            'payee' => 'required|string|max:255',
            'particulars' => 'required|string',
            'payment_method' => 'required|string|in:check,bank_transfer,cash',
            'check_number' => 'nullable|required_if:payment_method,check|string|max:50',
            'check_date' => 'nullable|required_if:payment_method,check|date',
            'remarks' => 'nullable|string',
        ]);
        
        // Generate a unique DV number
        $validated['dv_number'] = DisbursementVoucher::generateDVNumber();
        $validated['created_by'] = Auth::id();
        $validated['status'] = 'pending';
        
        // Create the disbursement voucher
        $disbursementVoucher = DisbursementVoucher::create($validated);
        
        // Generate and store the DV document
        $pdf = PDF::loadView('documents.dv', compact('disbursementVoucher'));
        $filename = 'dv_' . $disbursementVoucher->dv_number . '.pdf';
        $path = 'disbursement_vouchers/' . $filename;
        
        Storage::put('public/' . $path, $pdf->output());
        
        // Update the document path
        $disbursementVoucher->update(['document_path' => $path]);
        
        return redirect()->route('disbursement-vouchers.show', $disbursementVoucher)
            ->with('success', 'Disbursement Voucher created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DisbursementVoucher $disbursementVoucher)
    {
        $disbursementVoucher->load(['purchaseOrder.supplierQuotation.supplier', 
                                   'purchaseOrder.abstractOfQuotation.requestForQuotation.purchaseRequest', 
                                   'creator', 
                                   'approver',
                                   'paidBy']);
                             
        return view('disbursement-vouchers.show', compact('disbursementVoucher'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DisbursementVoucher $disbursementVoucher)
    {
        // Only allow editing if the DV is pending
        if ($disbursementVoucher->status !== 'pending') {
            return redirect()->route('disbursement-vouchers.show', $disbursementVoucher)
                ->with('error', 'Cannot edit a Disbursement Voucher that is not in pending status.');
        }
        
        $disbursementVoucher->load(['purchaseOrder.supplierQuotation.supplier']);
        
        return view('disbursement-vouchers.edit', compact('disbursementVoucher'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DisbursementVoucher $disbursementVoucher)
    {
        // Only allow updating if the DV is pending
        if ($disbursementVoucher->status !== 'pending') {
            return redirect()->route('disbursement-vouchers.show', $disbursementVoucher)
                ->with('error', 'Cannot update a Disbursement Voucher that is not in pending status.');
        }
        
        $validated = $request->validate([
            'dv_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0',
            'payee' => 'required|string|max:255',
            'particulars' => 'required|string',
            'payment_method' => 'required|string|in:check,bank_transfer,cash',
            'check_number' => 'nullable|required_if:payment_method,check|string|max:50',
            'check_date' => 'nullable|required_if:payment_method,check|date',
            'remarks' => 'nullable|string',
        ]);
        
        // Update the disbursement voucher
        $disbursementVoucher->update($validated);
        
        // Regenerate the PDF document
        $pdf = PDF::loadView('documents.dv', ['disbursementVoucher' => $disbursementVoucher->fresh()]);
        $path = $disbursementVoucher->document_path ?? 'disbursement_vouchers/dv_' . $disbursementVoucher->dv_number . '.pdf';
        
        Storage::put('public/' . $path, $pdf->output());
        
        // Update the document path if it was not set before
        if (!$disbursementVoucher->document_path) {
            $disbursementVoucher->update(['document_path' => $path]);
        }
        
        return redirect()->route('disbursement-vouchers.show', $disbursementVoucher)
            ->with('success', 'Disbursement Voucher updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DisbursementVoucher $disbursementVoucher)
    {
        // Only allow deletion if the DV is pending
        if ($disbursementVoucher->status !== 'pending') {
            return redirect()->route('disbursement-vouchers.show', $disbursementVoucher)
                ->with('error', 'Cannot delete a Disbursement Voucher that is not in pending status.');
        }
        
        // Delete the document if it exists
        if ($disbursementVoucher->document_path) {
            Storage::delete('public/' . $disbursementVoucher->document_path);
        }
        
        // Delete the disbursement voucher
        $disbursementVoucher->delete();
        
        return redirect()->route('disbursement-vouchers.index')
            ->with('success', 'Disbursement Voucher deleted successfully.');
    }
    
    /**
     * Approve the disbursement voucher.
     */
    public function approve(Request $request, DisbursementVoucher $disbursementVoucher)
    {
        // Only allow approving if the DV is pending
        if ($disbursementVoucher->status !== 'pending') {
            return redirect()->route('disbursement-vouchers.show', $disbursementVoucher)
                ->with('error', 'Cannot approve a Disbursement Voucher that is not in pending status.');
        }
        
        // Update the disbursement voucher approval details
        $disbursementVoucher->update([
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'status' => 'approved'
        ]);
        
        // Regenerate the PDF document to include approval details
        $pdf = PDF::loadView('documents.dv', ['disbursementVoucher' => $disbursementVoucher->fresh()]);
        $path = $disbursementVoucher->document_path;
        
        Storage::put('public/' . $path, $pdf->output());
        
        return redirect()->route('disbursement-vouchers.show', $disbursementVoucher)
            ->with('success', 'Disbursement Voucher approved successfully.');
    }
    
    /**
     * Mark the disbursement voucher as paid.
     */
    public function markPaid(Request $request, DisbursementVoucher $disbursementVoucher)
    {
        // Only allow marking as paid if the DV is approved
        if ($disbursementVoucher->status !== 'approved') {
            return redirect()->route('disbursement-vouchers.show', $disbursementVoucher)
                ->with('error', 'Cannot mark as paid a Disbursement Voucher that is not approved.');
        }
        
        // Update the disbursement voucher payment details
        $disbursementVoucher->update([
            'paid_by' => Auth::id(),
            'paid_at' => now(),
            'status' => 'paid'
        ]);
        
        // Regenerate the PDF document to include payment details
        $pdf = PDF::loadView('documents.dv', ['disbursementVoucher' => $disbursementVoucher->fresh()]);
        $path = $disbursementVoucher->document_path;
        
        Storage::put('public/' . $path, $pdf->output());
        
        return redirect()->route('disbursement-vouchers.show', $disbursementVoucher)
            ->with('success', 'Disbursement Voucher marked as paid successfully.');
    }
} 