<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\RequestForQuotation;
use App\Models\SupplierQuotation;
use App\Models\AbstractOfQuotation;
use App\Models\PurchaseOrder;
use App\Models\DisbursementVoucher;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Authorization will be handled in routes
    }

    /**
     * Generate a PDF for Request For Quotation.
     */
    public function generateRFQ(RequestForQuotation $rfq)
    {
        // Load related models
        $rfq->load(['purchaseRequest', 'creator']);
        
        // Generate the PDF
        $pdf = PDF::loadView('documents.rfq', compact('rfq'));
        
        // Set paper size
        $pdf->setPaper('a4', 'portrait');
        
        // Return the PDF for download
        return $pdf->download('RFQ-' . $rfq->rfq_number . '.pdf');
    }
    
    /**
     * Generate a PDF for Abstract of Quotation.
     */
    public function generateAOQ(AbstractOfQuotation $aoq)
    {
        // Load related models
        $aoq->load(['requestForQuotation.purchaseRequest', 'creator', 'awardedSupplier']);
        $aoq->requestForQuotation->load('supplierQuotations.supplier');
        
        // Generate the PDF
        $pdf = PDF::loadView('documents.aoq', compact('aoq'));
        
        // Set paper size
        $pdf->setPaper('a4', 'portrait');
        
        // Return the PDF for download
        return $pdf->download('AOQ-' . $aoq->aoq_number . '.pdf');
    }
    
    /**
     * Generate a PDF for Purchase Order.
     */
    public function generatePO(PurchaseOrder $po)
    {
        // Load related models
        $po->load(['abstractOfQuotation.requestForQuotation.purchaseRequest', 'supplierQuotation.supplier', 'creator']);
        $po->supplierQuotation->load('items');
        
        // Generate the PDF
        $pdf = PDF::loadView('documents.po', compact('po'));
        
        // Set paper size
        $pdf->setPaper('a4', 'portrait');
        
        // Return the PDF for download
        return $pdf->download('PO-' . $po->po_number . '.pdf');
    }
    
    /**
     * Generate a PDF for Disbursement Voucher.
     */
    public function generateDV(DisbursementVoucher $disbursementVoucher)
    {
        // Load related models
        $disbursementVoucher->load([
            'purchaseOrder.abstractOfQuotation.requestForQuotation.purchaseRequest', 
            'purchaseOrder.supplierQuotation.supplier', 
            'purchaseOrder.supplierQuotation.items',
            'creator',
            'approver',
            'paidBy'
        ]);
        
        // Generate the PDF
        $pdf = PDF::loadView('documents.dv', compact('disbursementVoucher'));
        
        // Set paper size
        $pdf->setPaper('a4', 'portrait');
        
        // Return the PDF for download
        return $pdf->download('DV-' . $disbursementVoucher->dv_number . '.pdf');
    }
}
