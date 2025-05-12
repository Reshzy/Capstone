<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierPerformance;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierPerformanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Supplier $supplier = null)
    {
        if ($supplier) {
            // If a supplier is specified, show only their performance records
            $performances = SupplierPerformance::with(['supplier', 'purchaseOrder', 'evaluator'])
                ->where('supplier_id', $supplier->id)
                ->latest()
                ->paginate(10);
                
            return view('supplier-performances.index', compact('performances', 'supplier'));
        } else {
            // Show all performance records
            $performances = SupplierPerformance::with(['supplier', 'purchaseOrder', 'evaluator'])
                ->latest()
                ->paginate(10);
                
            return view('supplier-performances.index', compact('performances'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Supplier $supplier = null, PurchaseOrder $purchaseOrder = null)
    {
        $suppliers = Supplier::where('is_active', true)->get();
        $purchaseOrders = null;
        
        if ($supplier) {
            // If a supplier is specified, only show their purchase orders
            $purchaseOrders = PurchaseOrder::whereHas('supplierQuotation', function ($query) use ($supplier) {
                $query->where('supplier_id', $supplier->id);
            })->get();
        } else if ($purchaseOrder) {
            // If a purchase order is specified, get its supplier
            $supplier = $purchaseOrder->supplierQuotation->supplier;
            $purchaseOrders = [$purchaseOrder];
        }
        
        $categories = ['delivery', 'quality', 'price', 'response', 'overall'];
        
        return view('supplier-performances.create', compact('suppliers', 'supplier', 'purchaseOrders', 'purchaseOrder', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'evaluation_date' => 'required|date',
            'performance_category' => 'required|in:delivery,quality,price,response,overall',
            'rating' => 'required|integer|min:1|max:5',
            'comments' => 'nullable|string',
        ]);
        
        $validatedData['evaluated_by'] = Auth::id();
        
        $performance = SupplierPerformance::create($validatedData);
        
        // Update the supplier's overall rating
        $supplier = Supplier::find($validatedData['supplier_id']);
        $supplier->updateRating();
        
        return redirect()->route('supplier-performances.show', $performance)
            ->with('success', 'Supplier performance record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SupplierPerformance $supplierPerformance)
    {
        $supplierPerformance->load(['supplier', 'purchaseOrder', 'evaluator']);
        
        return view('supplier-performances.show', compact('supplierPerformance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SupplierPerformance $supplierPerformance)
    {
        $supplierPerformance->load(['supplier', 'purchaseOrder']);
        
        $suppliers = Supplier::where('is_active', true)->get();
        $purchaseOrders = null;
        
        if ($supplierPerformance->supplier) {
            // Get purchase orders for this supplier
            $purchaseOrders = PurchaseOrder::whereHas('supplierQuotation', function ($query) use ($supplierPerformance) {
                $query->where('supplier_id', $supplierPerformance->supplier_id);
            })->get();
        }
        
        $categories = ['delivery', 'quality', 'price', 'response', 'overall'];
        
        return view('supplier-performances.edit', compact('supplierPerformance', 'suppliers', 'purchaseOrders', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SupplierPerformance $supplierPerformance)
    {
        $validatedData = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'evaluation_date' => 'required|date',
            'performance_category' => 'required|in:delivery,quality,price,response,overall',
            'rating' => 'required|integer|min:1|max:5',
            'comments' => 'nullable|string',
        ]);
        
        $supplierPerformance->update($validatedData);
        
        // Update the supplier's overall rating
        $supplier = Supplier::find($validatedData['supplier_id']);
        $supplier->updateRating();
        
        return redirect()->route('supplier-performances.show', $supplierPerformance)
            ->with('success', 'Supplier performance record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SupplierPerformance $supplierPerformance)
    {
        $supplierId = $supplierPerformance->supplier_id;
        
        $supplierPerformance->delete();
        
        // Update the supplier's overall rating
        $supplier = Supplier::find($supplierId);
        $supplier->updateRating();
        
        return redirect()->route('supplier-performances.index')
            ->with('success', 'Supplier performance record deleted successfully.');
    }
    
    /**
     * Show performance summary for a supplier.
     */
    public function summary(Supplier $supplier)
    {
        // Get average ratings by category
        $averageRatings = [
            'delivery' => $supplier->performances()->where('performance_category', 'delivery')->avg('rating') ?? 0,
            'quality' => $supplier->performances()->where('performance_category', 'quality')->avg('rating') ?? 0,
            'price' => $supplier->performances()->where('performance_category', 'price')->avg('rating') ?? 0,
            'response' => $supplier->performances()->where('performance_category', 'response')->avg('rating') ?? 0,
            'overall' => $supplier->performances()->where('performance_category', 'overall')->avg('rating') ?? 0,
        ];
        
        // Get recent performance records
        $recentPerformances = $supplier->performances()->with(['purchaseOrder', 'evaluator'])
            ->latest('evaluation_date')
            ->take(5)
            ->get();
            
        return view('supplier-performances.summary', compact('supplier', 'averageRatings', 'recentPerformances'));
    }
}
