<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Budget Approval Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('budget-approvals.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    &larr; Back to Approvals
                </a>
            </div>
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Budget Approval Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Approval Number</p>
                            <p class="mt-1">{{ $budgetApproval->approval_number }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Status</p>
                            <p class="mt-1">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ ucfirst($budgetApproval->status) }}
                                </span>
                            </p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Approved Amount</p>
                            <p class="mt-1 font-semibold">₱{{ number_format($budgetApproval->approved_amount, 2) }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Approved By</p>
                            <p class="mt-1">{{ $budgetApproval->approver->name }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Approved On</p>
                            <p class="mt-1">{{ $budgetApproval->approved_at->format('F d, Y h:i A') }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Fund Source</p>
                            <p class="mt-1">{{ $budgetApproval->fund_source ?? 'Not specified' }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Budget Code</p>
                            <p class="mt-1">{{ $budgetApproval->budget_code ?? 'Not specified' }}</p>
                        </div>
                        
                        @if($budgetApproval->notes)
                        <div class="md:col-span-2">
                            <p class="text-sm font-medium text-gray-500">Notes</p>
                            <p class="mt-1">{{ $budgetApproval->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Purchase Request Details</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm font-medium text-gray-500">PR Number</p>
                            <p class="mt-1">{{ $budgetApproval->purchaseRequest->pr_number }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Title</p>
                            <p class="mt-1">{{ $budgetApproval->purchaseRequest->title }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Department</p>
                            <p class="mt-1">{{ $budgetApproval->purchaseRequest->department }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Requested By</p>
                            <p class="mt-1">{{ $budgetApproval->purchaseRequest->user->name }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Estimated Amount</p>
                            <p class="mt-1">₱{{ number_format($budgetApproval->purchaseRequest->estimated_amount, 2) }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Date Requested</p>
                            <p class="mt-1">{{ $budgetApproval->purchaseRequest->created_at->format('F d, Y') }}</p>
                        </div>
                        
                        <div class="md:col-span-2">
                            <p class="text-sm font-medium text-gray-500">Description</p>
                            <p class="mt-1">{{ $budgetApproval->purchaseRequest->description ?? 'No description provided.' }}</p>
                        </div>
                        
                        @if($budgetApproval->purchaseRequest->document_path)
                        <div class="md:col-span-2">
                            <p class="text-sm font-medium text-gray-500">Attached Document</p>
                            <p class="mt-1">
                                <a href="{{ asset('storage/' . $budgetApproval->purchaseRequest->document_path) }}" target="_blank" class="text-blue-600 hover:text-blue-900">
                                    View Document
                                </a>
                            </p>
                        </div>
                        @endif
                    </div>
                    
                    <div class="mt-6">
                        <a href="{{ route('purchase-requests.show', $budgetApproval->purchaseRequest) }}" class="text-indigo-600 hover:text-indigo-900">
                            View Complete Purchase Request Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 