<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\ProcurementCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Supplier::with('procurementCategories');
        
        // Apply filters if any
        if ($request->has('category_id') && $request->category_id) {
            $query->whereHas('procurementCategories', function($q) use ($request) {
                $q->where('procurement_categories.id', $request->category_id);
            });
        }
        
        if ($request->has('status') && $request->status !== '') {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }
        
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('contact_person', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }
        
        $suppliers = $query->orderBy('name')->paginate(10);
        $categories = ProcurementCategory::orderBy('name')->get();
        
        return view('suppliers.index', compact('suppliers', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = ProcurementCategory::orderBy('name')->get();
        return view('suppliers.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'tax_id' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'category_ids' => 'array',
            'category_ids.*' => 'exists:procurement_categories,id',
        ]);
        
        DB::transaction(function() use ($validatedData, $request) {
            // Create supplier
            $supplier = Supplier::create([
                'name' => $validatedData['name'],
                'contact_person' => $validatedData['contact_person'] ?? null,
                'email' => $validatedData['email'] ?? null,
                'phone' => $validatedData['phone'] ?? null,
                'address' => $validatedData['address'] ?? null,
                'tax_id' => $validatedData['tax_id'] ?? null,
                'is_active' => $validatedData['is_active'] ?? true,
                'rating' => 0, // Default rating
            ]);
            
            // Attach categories if specified
            if ($request->has('category_ids')) {
                $supplier->procurementCategories()->attach($request->category_ids);
            }
        });
        
        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        $supplier->load('procurementCategories');
        
        // Get purchase history
        $purchaseOrders = $supplier->quotations()
            ->whereHas('requestForQuotation.abstractOfQuotation', function($q) use ($supplier) {
                $q->where('awarded_supplier_id', $supplier->id);
            })
            ->with([
                'requestForQuotation.abstractOfQuotation',
                'requestForQuotation.purchaseRequest',
                'items'
            ])
            ->get();
        
        // Get performance summary
        $performanceSummary = [
            'delivery' => $supplier->performances()->where('performance_category', 'delivery')->avg('rating') ?? 0,
            'quality' => $supplier->performances()->where('performance_category', 'quality')->avg('rating') ?? 0,
            'price' => $supplier->performances()->where('performance_category', 'price')->avg('rating') ?? 0,
            'response' => $supplier->performances()->where('performance_category', 'response')->avg('rating') ?? 0,
            'overall' => $supplier->performances()->where('performance_category', 'overall')->avg('rating') ?? 0,
        ];
        
        return view('suppliers.show', compact('supplier', 'purchaseOrders', 'performanceSummary'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        $supplier->load('procurementCategories');
        $categories = ProcurementCategory::orderBy('name')->get();
        
        // Get the IDs of the categories this supplier belongs to
        $selectedCategories = $supplier->procurementCategories->pluck('id')->toArray();
        
        return view('suppliers.edit', compact('supplier', 'categories', 'selectedCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'tax_id' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'category_ids' => 'array',
            'category_ids.*' => 'exists:procurement_categories,id',
        ]);
        
        DB::transaction(function() use ($validatedData, $request, $supplier) {
            // Update supplier
            $supplier->update([
                'name' => $validatedData['name'],
                'contact_person' => $validatedData['contact_person'] ?? null,
                'email' => $validatedData['email'] ?? null,
                'phone' => $validatedData['phone'] ?? null,
                'address' => $validatedData['address'] ?? null,
                'tax_id' => $validatedData['tax_id'] ?? null,
                'is_active' => $validatedData['is_active'] ?? true,
            ]);
            
            // Sync categories
            if ($request->has('category_ids')) {
                $supplier->procurementCategories()->sync($request->category_ids);
            } else {
                $supplier->procurementCategories()->detach();
            }
        });
        
        return redirect()->route('suppliers.show', $supplier)
            ->with('success', 'Supplier updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        // Check if the supplier is used in any quotations
        $hasQuotations = $supplier->quotations()->exists();
        
        if ($hasQuotations) {
            return redirect()->route('suppliers.index')
                ->with('error', 'Supplier cannot be deleted because it has quotations associated with it.');
        }
        
        DB::transaction(function() use ($supplier) {
            // Detach categories
            $supplier->procurementCategories()->detach();
            
            // Delete supplier
            $supplier->delete();
        });
        
        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }
    
    /**
     * Toggle the active status of a supplier.
     */
    public function toggleStatus(Supplier $supplier)
    {
        $supplier->update([
            'is_active' => !$supplier->is_active,
        ]);
        
        $status = $supplier->is_active ? 'active' : 'inactive';
        
        return redirect()->route('suppliers.index')
            ->with('success', "Supplier marked as {$status} successfully.");
    }
}
