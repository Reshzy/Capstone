<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notifications\QuotationReceived;
use App\Models\User;

class SupplierQuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Existing validation and creation code...
        
        // After creating the quotation, send notifications
        $procurementOfficers = User::role('procurement_officer')->get();
        foreach ($procurementOfficers as $officer) {
            $officer->notify(new QuotationReceived($supplierQuotation));
        }
        
        // Notify admin users as well
        $admins = User::role('admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new QuotationReceived($supplierQuotation));
        }
        
        // Existing return/redirect code...
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
