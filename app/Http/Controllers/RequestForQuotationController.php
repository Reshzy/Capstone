<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\RequestForQuotation;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestForQuotationController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Authorization will be handled in routes
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $requestForQuotations = RequestForQuotation::with(['purchaseRequest', 'creator'])
            ->latest()
            ->paginate(10);
            
        return view('rfq.index', compact('requestForQuotations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $purchaseRequestId = $request->query('pr_id');
        
        if ($purchaseRequestId) {
            $purchaseRequest = PurchaseRequest::findOrFail($purchaseRequestId);
            
            // Check if the purchase request is approved
            if ($purchaseRequest->status !== 'approved') {
                return redirect()->route('purchase-requests.show', $purchaseRequest)
                    ->with('error', 'Only approved purchase requests can have RFQs created.');
            }
            
            // Check if an RFQ already exists for this PR
            if ($purchaseRequest->requestForQuotation) {
                return redirect()->route('rfq.show', $purchaseRequest->requestForQuotation)
                    ->with('info', 'An RFQ already exists for this purchase request.');
            }
        } else {
            $purchaseRequest = null;
        }
        
        // Get eligible purchase requests (approved ones without RFQs)
        $eligiblePurchaseRequests = PurchaseRequest::where('status', 'approved')
            ->whereDoesntHave('requestForQuotation')
            ->get();
            
        return view('rfq.create', compact('purchaseRequest', 'eligiblePurchaseRequests'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'purchase_request_id' => 'required|exists:purchase_requests,id',
            'purpose' => 'nullable|string',
            'rfq_date' => 'required|date',
            'deadline' => 'required|date|after_or_equal:rfq_date',
            'notes' => 'nullable|string',
        ]);
        
        // Check if the purchase request already has an RFQ
        $purchaseRequest = PurchaseRequest::findOrFail($validatedData['purchase_request_id']);
        if ($purchaseRequest->requestForQuotation) {
            return redirect()->back()
                ->with('error', 'An RFQ already exists for this purchase request.')
                ->withInput();
        }
        
        // Generate RFQ number
        $rfqNumber = RequestForQuotation::generateRFQNumber();
        
        $rfq = RequestForQuotation::create([
            'purchase_request_id' => $validatedData['purchase_request_id'],
            'created_by' => Auth::id(),
            'rfq_number' => $rfqNumber,
            'purpose' => $validatedData['purpose'],
            'rfq_date' => $validatedData['rfq_date'],
            'deadline' => $validatedData['deadline'],
            'status' => 'draft',
            'notes' => $validatedData['notes'],
        ]);
        
        return redirect()->route('rfq.show', $rfq)
            ->with('success', 'Request for Quotation created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RequestForQuotation $rfq)
    {
        $rfq->load(['purchaseRequest', 'creator', 'supplierQuotations.supplier']);
        $suppliers = Supplier::all();
        
        return view('rfq.show', compact('rfq', 'suppliers'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RequestForQuotation $rfq)
    {
        if ($rfq->status !== 'draft') {
            return redirect()->route('rfq.show', $rfq)
                ->with('error', 'Only draft RFQs can be edited.');
        }
        
        $eligiblePurchaseRequests = PurchaseRequest::where('status', 'approved')
            ->whereDoesntHave('requestForQuotation')
            ->orWhere('id', $rfq->purchase_request_id)
            ->get();
            
        return view('rfq.edit', compact('rfq', 'eligiblePurchaseRequests'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RequestForQuotation $rfq)
    {
        if ($rfq->status !== 'draft') {
            return redirect()->route('rfq.show', $rfq)
                ->with('error', 'Only draft RFQs can be updated.');
        }
        
        $validatedData = $request->validate([
            'purpose' => 'nullable|string',
            'rfq_date' => 'required|date',
            'deadline' => 'required|date|after_or_equal:rfq_date',
            'notes' => 'nullable|string',
        ]);
        
        $rfq->update([
            'purpose' => $validatedData['purpose'],
            'rfq_date' => $validatedData['rfq_date'],
            'deadline' => $validatedData['deadline'],
            'notes' => $validatedData['notes'],
        ]);
        
        return redirect()->route('rfq.show', $rfq)
            ->with('success', 'Request for Quotation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RequestForQuotation $rfq)
    {
        if ($rfq->status !== 'draft') {
            return redirect()->route('rfq.show', $rfq)
                ->with('error', 'Only draft RFQs can be deleted.');
        }
        
        // Delete the RFQ
        $rfq->delete();
        
        return redirect()->route('rfq.index')
            ->with('success', 'Request for Quotation deleted successfully.');
    }
    
    /**
     * Publish the RFQ to suppliers.
     */
    public function publish(RequestForQuotation $rfq)
    {
        if ($rfq->status !== 'draft') {
            return redirect()->route('rfq.show', $rfq)
                ->with('error', 'Only draft RFQs can be published.');
        }
        
        // Update the status to published
        $rfq->update([
            'status' => 'published',
        ]);
        
        // TODO: Send notifications to suppliers
        
        return redirect()->route('rfq.show', $rfq)
            ->with('success', 'Request for Quotation published successfully.');
    }
    
    /**
     * Close the RFQ to stop accepting quotations.
     */
    public function close(RequestForQuotation $rfq)
    {
        if ($rfq->status !== 'published') {
            return redirect()->route('rfq.show', $rfq)
                ->with('error', 'Only published RFQs can be closed.');
        }
        
        // Update the status to closed
        $rfq->update([
            'status' => 'closed',
        ]);
        
        return redirect()->route('rfq.show', $rfq)
            ->with('success', 'Request for Quotation closed successfully.');
    }
}
