<?php

namespace App\Http\Controllers;

use App\Models\ProcurementCategory;
use Illuminate\Http\Request;

class ProcurementCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = ProcurementCategory::withCount('suppliers')
            ->orderBy('name')
            ->paginate(10);
            
        return view('procurement-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('procurement-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:procurement_categories',
            'description' => 'nullable|string|max:1000',
        ]);
        
        ProcurementCategory::create($validatedData);
        
        return redirect()->route('procurement-categories.index')
            ->with('success', 'Procurement category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProcurementCategory $procurementCategory)
    {
        $procurementCategory->load('suppliers');
        
        return view('procurement-categories.show', compact('procurementCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProcurementCategory $procurementCategory)
    {
        return view('procurement-categories.edit', compact('procurementCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProcurementCategory $procurementCategory)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:procurement_categories,name,' . $procurementCategory->id,
            'description' => 'nullable|string|max:1000',
        ]);
        
        $procurementCategory->update($validatedData);
        
        return redirect()->route('procurement-categories.show', $procurementCategory)
            ->with('success', 'Procurement category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProcurementCategory $procurementCategory)
    {
        // Check if the category has suppliers
        if ($procurementCategory->suppliers()->exists()) {
            return redirect()->route('procurement-categories.index')
                ->with('error', 'Category cannot be deleted because it has suppliers associated with it.');
        }
        
        $procurementCategory->delete();
        
        return redirect()->route('procurement-categories.index')
            ->with('success', 'Procurement category deleted successfully.');
    }
}
