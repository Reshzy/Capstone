<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\Department;
use App\Models\Supplier;
use App\Models\ProcurementCategory;
use App\Models\BudgetApproval;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\Facades\DataTables;

class ReportController extends Controller
{
    /**
     * Display the reporting dashboard.
     */
    public function index()
    {
        $departments = Department::orderBy('name')->get();
        $categories = ProcurementCategory::orderBy('name')->get();
        
        return view('reports.index', compact('departments', 'categories'));
    }
    
    /**
     * Generate a report of purchase requests.
     */
    public function purchaseRequests(Request $request)
    {
        if ($request->ajax()) {
            $query = PurchaseRequest::with(['user', 'department', 'items']);
            
            // Apply filters
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [
                    Carbon::parse($request->start_date)->startOfDay(),
                    Carbon::parse($request->end_date)->endOfDay()
                ]);
            }
            
            if ($request->filled('department_id')) {
                $query->where('department_id', $request->department_id);
            }
            
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            return DataTables::of($query)
                ->addColumn('total_amount', function ($purchaseRequest) {
                    return '₱ ' . number_format($purchaseRequest->items->sum('estimated_cost'), 2);
                })
                ->addColumn('created_date', function ($purchaseRequest) {
                    return $purchaseRequest->created_at->format('M d, Y');
                })
                ->addColumn('actions', function ($purchaseRequest) {
                    return '<a href="' . route('purchase-requests.show', $purchaseRequest) . '" class="btn btn-sm btn-info">View</a>';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }
        
        $departments = Department::orderBy('name')->get();
        return view('reports.purchase-requests', compact('departments'));
    }
    
    /**
     * Generate a report of budget approvals.
     */
    public function budgetApprovals(Request $request)
    {
        if ($request->ajax()) {
            $query = BudgetApproval::with(['purchaseRequest.user', 'purchaseRequest.department', 'approver']);
            
            // Apply filters
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [
                    Carbon::parse($request->start_date)->startOfDay(),
                    Carbon::parse($request->end_date)->endOfDay()
                ]);
            }
            
            if ($request->filled('department_id')) {
                $query->whereHas('purchaseRequest', function ($q) use ($request) {
                    $q->where('department_id', $request->department_id);
                });
            }
            
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            return DataTables::of($query)
                ->addColumn('pr_number', function ($approval) {
                    return $approval->purchaseRequest->pr_number;
                })
                ->addColumn('requestor', function ($approval) {
                    return $approval->purchaseRequest->user->name;
                })
                ->addColumn('department', function ($approval) {
                    return $approval->purchaseRequest->department->name;
                })
                ->addColumn('approved_amount', function ($approval) {
                    return '₱ ' . number_format($approval->approved_amount, 2);
                })
                ->addColumn('approval_date', function ($approval) {
                    return $approval->updated_at->format('M d, Y');
                })
                ->addColumn('actions', function ($approval) {
                    return '<a href="' . route('budget-approvals.show', $approval) . '" class="btn btn-sm btn-info">View</a>';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }
        
        $departments = Department::orderBy('name')->get();
        return view('reports.budget-approvals', compact('departments'));
    }
    
    /**
     * Generate a report of suppliers.
     */
    public function suppliers(Request $request)
    {
        if ($request->ajax()) {
            $query = Supplier::with('procurementCategories');
            
            // Apply filters
            if ($request->filled('category_id')) {
                $query->whereHas('procurementCategories', function ($q) use ($request) {
                    $q->where('procurement_categories.id', $request->category_id);
                });
            }
            
            if ($request->filled('status') && $request->status !== '') {
                $isActive = $request->status === 'active';
                $query->where('is_active', $isActive);
            }
            
            if ($request->filled('rating')) {
                $query->where('rating', '>=', $request->rating);
            }
            
            return DataTables::of($query)
                ->addColumn('categories', function ($supplier) {
                    return $supplier->procurementCategories->pluck('name')->implode(', ');
                })
                ->addColumn('status', function ($supplier) {
                    return $supplier->is_active ? 'Active' : 'Inactive';
                })
                ->addColumn('rating_display', function ($supplier) {
                    return number_format($supplier->rating, 1) . ' / 5.0';
                })
                ->addColumn('actions', function ($supplier) {
                    return '<a href="' . route('suppliers.show', $supplier) . '" class="btn btn-sm btn-info">View</a>';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }
        
        $categories = ProcurementCategory::orderBy('name')->get();
        return view('reports.suppliers', compact('categories'));
    }
    
    /**
     * Generate a PDF report.
     */
    public function generatePdf(Request $request)
    {
        $validatedData = $request->validate([
            'report_type' => 'required|in:purchase_requests,budget_approvals,suppliers',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'department_id' => 'nullable|exists:departments,id',
            'category_id' => 'nullable|exists:procurement_categories,id',
            'status' => 'nullable|string',
        ]);
        
        $data = [];
        $title = '';
        $view = '';
        
        switch ($validatedData['report_type']) {
            case 'purchase_requests':
                $query = PurchaseRequest::with(['user', 'department', 'items']);
                $title = 'Purchase Requests Report';
                $view = 'reports.pdf.purchase-requests';
                break;
                
            case 'budget_approvals':
                $query = BudgetApproval::with(['purchaseRequest.user', 'purchaseRequest.department', 'approver']);
                $title = 'Budget Approvals Report';
                $view = 'reports.pdf.budget-approvals';
                break;
                
            case 'suppliers':
                $query = Supplier::with('procurementCategories');
                $title = 'Suppliers Report';
                $view = 'reports.pdf.suppliers';
                break;
        }
        
        // Apply filters
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
            $title .= ' (' . Carbon::parse($request->start_date)->format('M d, Y') . ' - ' . Carbon::parse($request->end_date)->format('M d, Y') . ')';
        }
        
        if ($validatedData['report_type'] === 'purchase_requests' || $validatedData['report_type'] === 'budget_approvals') {
            if ($request->filled('department_id')) {
                if ($validatedData['report_type'] === 'purchase_requests') {
                    $query->where('department_id', $request->department_id);
                } else {
                    $query->whereHas('purchaseRequest', function ($q) use ($request) {
                        $q->where('department_id', $request->department_id);
                    });
                }
                $department = Department::find($request->department_id);
                $title .= ' - ' . $department->name . ' Department';
            }
            
            if ($request->filled('status')) {
                $query->where('status', $request->status);
                $title .= ' - ' . ucfirst($request->status) . ' Status';
            }
        }
        
        if ($validatedData['report_type'] === 'suppliers') {
            if ($request->filled('category_id')) {
                $query->whereHas('procurementCategories', function ($q) use ($request) {
                    $q->where('procurement_categories.id', $request->category_id);
                });
                $category = ProcurementCategory::find($request->category_id);
                $title .= ' - ' . $category->name . ' Category';
            }
            
            if ($request->filled('status') && $request->status !== '') {
                $isActive = $request->status === 'active';
                $query->where('is_active', $isActive);
                $title .= ' - ' . ucfirst($request->status) . ' Status';
            }
        }
        
        $data['items'] = $query->get();
        $data['title'] = $title;
        $data['generated_at'] = Carbon::now()->format('M d, Y h:i A');
        
        $pdf = Pdf::loadView($view, $data);
        
        return $pdf->download($validatedData['report_type'] . '_report_' . Carbon::now()->format('Ymd_His') . '.pdf');
    }
    
    /**
     * Show the activity log.
     */
    public function activityLog(Request $request)
    {
        if ($request->ajax()) {
            $query = \Spatie\Activitylog\Models\Activity::with(['causer'])
                ->orderBy('created_at', 'desc');
            
            // Apply filters
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [
                    Carbon::parse($request->start_date)->startOfDay(),
                    Carbon::parse($request->end_date)->endOfDay()
                ]);
            }
            
            if ($request->filled('causer_id')) {
                $query->where('causer_id', $request->causer_id);
            }
            
            if ($request->filled('log_name')) {
                $query->where('log_name', $request->log_name);
            }
            
            return DataTables::of($query)
                ->addColumn('user', function ($activity) {
                    return $activity->causer ? $activity->causer->name : 'System';
                })
                ->addColumn('description', function ($activity) {
                    return $activity->description;
                })
                ->addColumn('date', function ($activity) {
                    return $activity->created_at->format('M d, Y h:i A');
                })
                ->make(true);
        }
        
        return view('reports.activity-log');
    }
}
