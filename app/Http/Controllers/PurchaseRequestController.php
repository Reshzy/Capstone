<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class PurchaseRequestController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;
    
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view purchase requests')->only(['index', 'show']);
        $this->middleware('permission:create purchase requests')->only(['create', 'store']);
        $this->middleware('permission:edit purchase requests')->only(['edit', 'update']);
        $this->middleware('permission:delete purchase requests')->only('destroy');
        $this->middleware('permission:approve purchase requests|reject purchase requests')->only('processApproval');
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('procurement_officer')) {
            $purchaseRequests = PurchaseRequest::with('user', 'approver')->latest()->paginate(10);
        } elseif (Auth::user()->hasRole('approver')) {
            $purchaseRequests = PurchaseRequest::with('user')
                ->where('status', 'submitted')
                ->latest()
                ->paginate(10);
        } else {
            $purchaseRequests = PurchaseRequest::where('user_id', Auth::id())
                ->with('approver')
                ->latest()
                ->paginate(10);
        }
        
        return view('purchase-requests.index', compact('purchaseRequests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('purchase-requests.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department' => 'required|string|max:100',
            'estimated_amount' => 'required|numeric|min:1',
            'document_path' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);
        
        $purchaseRequest = new PurchaseRequest();
        $purchaseRequest->user_id = Auth::id();
        $purchaseRequest->pr_number = PurchaseRequest::generatePRNumber();
        $purchaseRequest->title = $validatedData['title'];
        $purchaseRequest->description = $validatedData['description'] ?? null;
        $purchaseRequest->department = $validatedData['department'];
        $purchaseRequest->estimated_amount = $validatedData['estimated_amount'];
        $purchaseRequest->status = 'draft';
        
        if ($request->hasFile('document_path')) {
            $path = $request->file('document_path')->store('purchase_requests', 'public');
            $purchaseRequest->document_path = $path;
        }
        
        $purchaseRequest->save();
        
        return redirect()
            ->route('purchase-requests.show', $purchaseRequest)
            ->with('success', 'Purchase request created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->load('user', 'approver');
        return view('purchase-requests.show', compact('purchaseRequest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchaseRequest $purchaseRequest)
    {
        if (Auth::id() !== $purchaseRequest->user_id && !Auth::user()->hasRole(['admin', 'procurement_officer'])) {
            return redirect()
                ->route('purchase-requests.index')
                ->with('error', 'You are not authorized to edit this purchase request.');
        }
        
        if ($purchaseRequest->status !== 'draft' && !Auth::user()->hasRole(['admin', 'procurement_officer'])) {
            return redirect()
                ->route('purchase-requests.show', $purchaseRequest)
                ->with('error', 'You cannot edit a purchase request that has been submitted.');
        }
        
        return view('purchase-requests.edit', compact('purchaseRequest'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseRequest $purchaseRequest)
    {
        if (Auth::id() !== $purchaseRequest->user_id && !Auth::user()->hasRole(['admin', 'procurement_officer'])) {
            return redirect()
                ->route('purchase-requests.index')
                ->with('error', 'You are not authorized to update this purchase request.');
        }
        
        if ($purchaseRequest->status !== 'draft' && !Auth::user()->hasRole(['admin', 'procurement_officer'])) {
            return redirect()
                ->route('purchase-requests.show', $purchaseRequest)
                ->with('error', 'You cannot update a purchase request that has been submitted.');
        }
        
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department' => 'required|string|max:100',
            'estimated_amount' => 'required|numeric|min:1',
            'document_path' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);
        
        $purchaseRequest->title = $validatedData['title'];
        $purchaseRequest->description = $validatedData['description'] ?? null;
        $purchaseRequest->department = $validatedData['department'];
        $purchaseRequest->estimated_amount = $validatedData['estimated_amount'];
        
        if ($request->hasFile('document_path')) {
            // Delete old file if exists
            if ($purchaseRequest->document_path) {
                Storage::disk('public')->delete($purchaseRequest->document_path);
            }
            
            // Store new file
            $path = $request->file('document_path')->store('purchase_requests', 'public');
            $purchaseRequest->document_path = $path;
        }
        
        $purchaseRequest->save();
        
        return redirect()
            ->route('purchase-requests.show', $purchaseRequest)
            ->with('success', 'Purchase request updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseRequest $purchaseRequest)
    {
        if (Auth::id() !== $purchaseRequest->user_id && !Auth::user()->hasRole('admin')) {
            return redirect()
                ->route('purchase-requests.index')
                ->with('error', 'You are not authorized to delete this purchase request.');
        }
        
        if ($purchaseRequest->status !== 'draft') {
            return redirect()
                ->route('purchase-requests.show', $purchaseRequest)
                ->with('error', 'You cannot delete a purchase request that has been submitted.');
        }
        
        // Delete document if exists
        if ($purchaseRequest->document_path) {
            Storage::disk('public')->delete($purchaseRequest->document_path);
        }
        
        $purchaseRequest->delete();
        
        return redirect()
            ->route('purchase-requests.index')
            ->with('success', 'Purchase request deleted successfully.');
    }
    
    /**
     * Submit the purchase request for approval.
     */
    public function submit(PurchaseRequest $purchaseRequest)
    {
        if (Auth::id() !== $purchaseRequest->user_id && !Auth::user()->hasRole('admin')) {
            return redirect()
                ->route('purchase-requests.index')
                ->with('error', 'You are not authorized to submit this purchase request.');
        }
        
        if ($purchaseRequest->status !== 'draft') {
            return redirect()
                ->route('purchase-requests.show', $purchaseRequest)
                ->with('error', 'This purchase request has already been submitted.');
        }
        
        $purchaseRequest->status = 'submitted';
        $purchaseRequest->save();
        
        // TODO: Send notification to approvers
        
        return redirect()
            ->route('purchase-requests.show', $purchaseRequest)
            ->with('success', 'Purchase request submitted successfully and is pending approval.');
    }
    
    /**
     * Process the approval or rejection of a purchase request.
     */
    public function processApproval(Request $request, PurchaseRequest $purchaseRequest)
    {
        if (!Auth::user()->hasPermissionTo('approve purchase requests') && 
            !Auth::user()->hasPermissionTo('reject purchase requests')) {
            return redirect()
                ->route('purchase-requests.index')
                ->with('error', 'You are not authorized to approve or reject purchase requests.');
        }
        
        if ($purchaseRequest->status !== 'submitted') {
            return redirect()
                ->route('purchase-requests.show', $purchaseRequest)
                ->with('error', 'This purchase request is not pending approval.');
        }
        
        $validatedData = $request->validate([
            'action' => 'required|in:approve,reject',
            'rejection_reason' => 'required_if:action,reject|nullable|string',
        ]);
        
        $purchaseRequest->approver_id = Auth::id();
        
        if ($validatedData['action'] === 'approve') {
            $purchaseRequest->status = 'approved';
            $purchaseRequest->approved_at = now();
            $message = 'Purchase request approved successfully.';
        } else {
            $purchaseRequest->status = 'rejected';
            $purchaseRequest->rejection_reason = $validatedData['rejection_reason'];
            $message = 'Purchase request rejected successfully.';
        }
        
        $purchaseRequest->save();
        
        // TODO: Send notification to the requestor
        
        return redirect()
            ->route('purchase-requests.show', $purchaseRequest)
            ->with('success', $message);
    }
}
