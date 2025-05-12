<?php

namespace App\Http\Controllers;

use App\Models\BudgetApproval;
use App\Models\PurchaseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BudgetApprovalController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Handle middleware in routes instead
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pendingApprovals = PurchaseRequest::where('status', 'submitted')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        $completedApprovals = BudgetApproval::with('purchaseRequest')
            ->where('approver_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('budget-approvals.index', compact('pendingApprovals', 'completedApprovals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(PurchaseRequest $purchaseRequest)
    {
        if ($purchaseRequest->status !== 'submitted') {
            return redirect()->route('purchase-requests.show', $purchaseRequest)
                ->with('error', 'Only submitted purchase requests can be approved.');
        }
        
        return view('budget-approvals.create', compact('purchaseRequest'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, PurchaseRequest $purchaseRequest)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,revised',
            'approved_amount' => 'required_if:status,approved|numeric|min:0',
            'notes' => 'nullable|string',
            'fund_source' => 'nullable|string|max:255',
            'budget_code' => 'nullable|string|max:255',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Update the purchase request status
            $purchaseRequest->status = $request->status;
            $purchaseRequest->approver_id = Auth::id();
            
            if ($request->status === 'rejected') {
                $purchaseRequest->rejection_reason = $request->notes;
            }
            
            $purchaseRequest->save();
            
            // Create budget approval if approved
            if ($request->status === 'approved') {
                BudgetApproval::create([
                    'purchase_request_id' => $purchaseRequest->id,
                    'approver_id' => Auth::id(),
                    'approved_amount' => $request->approved_amount,
                    'approval_number' => 'BA-' . date('Y') . '-' . Str::padLeft((string) (BudgetApproval::count() + 1), 5, '0'),
                    'notes' => $request->notes,
                    'status' => 'approved',
                    'fund_source' => $request->fund_source,
                    'budget_code' => $request->budget_code,
                    'approved_at' => now(),
                ]);
                
                $purchaseRequest->approved_at = now();
                $purchaseRequest->save();
            }
            
            DB::commit();
            
            return redirect()->route('purchase-requests.show', $purchaseRequest)
                ->with('success', 'Purchase request has been ' . $request->status . ' successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BudgetApproval $budgetApproval)
    {
        return view('budget-approvals.show', compact('budgetApproval'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
