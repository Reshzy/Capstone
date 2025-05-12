<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\AbstractOfQuotation;
use App\Models\SupplierQuotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with(['abstractOfQuotation', 'supplierQuotation.supplier', 'creator', 'approver'])
            ->latest()
            ->paginate(10);
            
        return view('purchase-orders.index', compact('purchaseOrders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(AbstractOfQuotation $abstractOfQuotation)
    {
        // Check if the AOQ is approved
        if (!$abstractOfQuotation->is_approved) {
            return redirect()->route('abstract-of-quotations.show', $abstractOfQuotation)
                ->with('error', 'The Abstract of Quotation must be approved before creating a Purchase Order.');
        }
        
        // Get the awarded supplier quotation
        $supplierQuotation = SupplierQuotation::where('supplier_id', $abstractOfQuotation->awarded_supplier_id)
            ->where('request_for_quotation_id', $abstractOfQuotation->request_for_quotation_id)
            ->first();
            
        if (!$supplierQuotation) {
            return redirect()->route('abstract-of-quotations.show', $abstractOfQuotation)
                ->with('error', 'Cannot find the awarded supplier quotation.');
        }
        
        return view('purchase-orders.create', compact('abstractOfQuotation', 'supplierQuotation'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'abstract_of_quotation_id' => 'required|exists:abstract_of_quotations,id',
            'supplier_quotation_id' => 'required|exists:supplier_quotations,id',
            'po_date' => 'required|date',
            'delivery_location' => 'required|string|max:255',
            'delivery_days' => 'required|integer|min:1',
            'total_amount' => 'required|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);
        
        // Generate a unique PO number
        $validated['po_number'] = PurchaseOrder::generatePONumber();
        $validated['created_by'] = Auth::id();
        $validated['status'] = 'pending';
        
        // Create the purchase order
        $purchaseOrder = PurchaseOrder::create($validated);
        
        // Generate and store the PO document
        $pdf = PDF::loadView('documents.po', compact('purchaseOrder'));
        $filename = 'po_' . $purchaseOrder->po_number . '.pdf';
        $path = 'purchase_orders/' . $filename;
        
        Storage::put('public/' . $path, $pdf->output());
        
        // Update the document path
        $purchaseOrder->update(['document_path' => $path]);
        
        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Purchase Order created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['abstractOfQuotation.requestForQuotation.purchaseRequest', 
                             'supplierQuotation.supplier', 
                             'creator', 
                             'approver']);
                             
        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchaseOrder $purchaseOrder)
    {
        // Only allow editing if the PO is pending
        if ($purchaseOrder->status !== 'pending') {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'Cannot edit a Purchase Order that is not in pending status.');
        }
        
        $purchaseOrder->load(['abstractOfQuotation', 'supplierQuotation.supplier']);
        
        return view('purchase-orders.edit', compact('purchaseOrder'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        // Only allow updating if the PO is pending
        if ($purchaseOrder->status !== 'pending') {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'Cannot update a Purchase Order that is not in pending status.');
        }
        
        $validated = $request->validate([
            'po_date' => 'required|date',
            'delivery_location' => 'required|string|max:255',
            'delivery_days' => 'required|integer|min:1',
            'total_amount' => 'required|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);
        
        // Update the purchase order
        $purchaseOrder->update($validated);
        
        // Regenerate the PDF document
        $pdf = PDF::loadView('documents.po', ['po' => $purchaseOrder->fresh()]);
        $path = $purchaseOrder->document_path ?? 'purchase_orders/po_' . $purchaseOrder->po_number . '.pdf';
        
        Storage::put('public/' . $path, $pdf->output());
        
        // Update the document path if it was not set before
        if (!$purchaseOrder->document_path) {
            $purchaseOrder->update(['document_path' => $path]);
        }
        
        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Purchase Order updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        // Only allow deletion if the PO is pending
        if ($purchaseOrder->status !== 'pending') {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'Cannot delete a Purchase Order that is not in pending status.');
        }
        
        // Delete the document if it exists
        if ($purchaseOrder->document_path) {
            Storage::delete('public/' . $purchaseOrder->document_path);
        }
        
        // Delete the purchase order
        $purchaseOrder->delete();
        
        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase Order deleted successfully.');
    }
    
    /**
     * Approve the purchase order.
     */
    public function approve(Request $request, PurchaseOrder $purchaseOrder)
    {
        // Only allow approving if the PO is pending
        if ($purchaseOrder->status !== 'pending') {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'Cannot approve a Purchase Order that is not in pending status.');
        }
        
        // Update the purchase order approval details
        $purchaseOrder->update([
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
        
        // Regenerate the PDF document to include approval details
        $pdf = PDF::loadView('documents.po', ['po' => $purchaseOrder->fresh()]);
        $path = $purchaseOrder->document_path;
        
        Storage::put('public/' . $path, $pdf->output());
        
        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Purchase Order approved successfully.');
    }
    
    /**
     * Mark the purchase order as delivered.
     */
    public function markDelivered(Request $request, PurchaseOrder $purchaseOrder)
    {
        // Only allow marking as delivered if the PO is pending and approved
        if ($purchaseOrder->status !== 'pending' || !$purchaseOrder->approved_by) {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'Cannot mark as delivered a Purchase Order that is not approved or in pending status.');
        }
        
        // Update the purchase order status
        $purchaseOrder->update([
            'status' => 'delivered',
        ]);
        
        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Purchase Order marked as delivered successfully.');
    }
} 