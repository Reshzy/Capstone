<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Purchase Request Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif
                    
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">{{ $purchaseRequest->title }}</h3>
                        <div>
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $purchaseRequest->status === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                            {{ $purchaseRequest->status === 'submitted' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $purchaseRequest->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $purchaseRequest->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ ucfirst($purchaseRequest->status) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">PR Number</h4>
                            <p>{{ $purchaseRequest->pr_number }}</p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Department</h4>
                            <p>{{ $purchaseRequest->department }}</p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Estimated Amount</h4>
                            <p>PHP {{ number_format($purchaseRequest->estimated_amount, 2) }}</p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Requested By</h4>
                            <p>{{ $purchaseRequest->user->name }}</p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Created Date</h4>
                            <p>{{ $purchaseRequest->created_at->format('F d, Y h:i A') }}</p>
                        </div>
                        
                        @if($purchaseRequest->status === 'approved' || $purchaseRequest->status === 'rejected')
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Processed By</h4>
                            <p>{{ $purchaseRequest->approver->name }}</p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Processed Date</h4>
                            <p>{{ $purchaseRequest->approved_at ? $purchaseRequest->approved_at->format('F d, Y h:i A') : 'N/A' }}</p>
                        </div>
                        @endif
                    </div>
                    
                    @if($purchaseRequest->description)
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Description</h4>
                        <div class="bg-gray-50 p-4 rounded">
                            <p class="whitespace-pre-line">{{ $purchaseRequest->description }}</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($purchaseRequest->document_path)
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Attached Document</h4>
                        <div class="bg-gray-50 p-4 rounded">
                            <a href="{{ Storage::url($purchaseRequest->document_path) }}" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-900">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"></path>
                                </svg>
                                View Document
                            </a>
                        </div>
                    </div>
                    @endif
                    
                    @if($purchaseRequest->status === 'rejected' && $purchaseRequest->rejection_reason)
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-red-500 mb-2">Rejection Reason</h4>
                        <div class="bg-red-50 p-4 rounded border-l-4 border-red-500">
                            <p class="whitespace-pre-line">{{ $purchaseRequest->rejection_reason }}</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($purchaseRequest->status === 'approved' && $purchaseRequest->budgetApproval)
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-green-500 mb-2">Budget Approval Details</h4>
                        <div class="bg-green-50 p-4 rounded border-l-4 border-green-500">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Approval Number</p>
                                    <p>{{ $purchaseRequest->budgetApproval->approval_number }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Approved Amount</p>
                                    <p class="font-semibold">₱{{ number_format($purchaseRequest->budgetApproval->approved_amount, 2) }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Approved By</p>
                                    <p>{{ $purchaseRequest->budgetApproval->approver->name }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Approved On</p>
                                    <p>{{ $purchaseRequest->budgetApproval->approved_at->format('F d, Y') }}</p>
                                </div>
                                
                                @if($purchaseRequest->budgetApproval->fund_source)
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Fund Source</p>
                                    <p>{{ $purchaseRequest->budgetApproval->fund_source }}</p>
                                </div>
                                @endif
                                
                                @if($purchaseRequest->budgetApproval->budget_code)
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Budget Code</p>
                                    <p>{{ $purchaseRequest->budgetApproval->budget_code }}</p>
                                </div>
                                @endif
                            </div>
                            
                            @if($purchaseRequest->budgetApproval->notes)
                            <div class="mt-4 pt-4 border-t border-green-200">
                                <p class="text-sm font-medium text-gray-500">Notes</p>
                                <p class="whitespace-pre-line">{{ $purchaseRequest->budgetApproval->notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                    
                    <!-- Action Buttons -->
                    <div class="flex justify-between items-center border-t pt-4 mt-6">
                        <div>
                            <a href="{{ route('purchase-requests.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                Back to List
                            </a>
                        </div>
                        
                        <div class="flex space-x-2">
                            @if($purchaseRequest->status === 'draft' && (auth()->id() === $purchaseRequest->user_id || auth()->user()->can('edit purchase requests')))
                                <a href="{{ route('purchase-requests.edit', $purchaseRequest) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-500 focus:bg-yellow-500 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Edit
                                </a>
                            @endif
                            
                            @if($purchaseRequest->status === 'draft' && auth()->id() === $purchaseRequest->user_id)
                                <form action="{{ route('purchase-requests.submit', $purchaseRequest) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150" onclick="return confirm('Are you sure you want to submit this request for approval?')">
                                        Submit for Approval
                                    </button>
                                </form>
                            @endif
                            
                            @if($purchaseRequest->status === 'submitted' && auth()->user()->can('approve purchase requests'))
                                <a href="{{ route('budget-approvals.create', $purchaseRequest) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Review for Budget Approval
                                </a>
                            @endif
                            
                            @if($purchaseRequest->status === 'draft' && (auth()->id() === $purchaseRequest->user_id || auth()->user()->can('delete purchase requests')))
                                <form action="{{ route('purchase-requests.destroy', $purchaseRequest) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150" onclick="return confirm('Are you sure you want to delete this purchase request?')">
                                        Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 