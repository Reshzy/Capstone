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
                                <form action="{{ route('purchase-requests.process-approval', $purchaseRequest) }}" method="POST" class="inline-block">
                                    @csrf
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150" onclick="return confirm('Are you sure you want to approve this purchase request?')">
                                        Approve
                                    </button>
                                </form>
                                
                                <button type="button" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150" onclick="document.getElementById('rejection-modal').classList.remove('hidden')">
                                    Reject
                                </button>
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
    
    <!-- Rejection Modal -->
    <div id="rejection-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Reject Purchase Request</h3>
                <div class="mt-2 px-7 py-3">
                    <form action="{{ route('purchase-requests.process-approval', $purchaseRequest) }}" method="POST">
                        @csrf
                        <input type="hidden" name="action" value="reject">
                        <div class="mb-4">
                            <label for="rejection_reason" class="block text-left text-sm font-medium text-gray-700 mb-2">Reason for Rejection</label>
                            <textarea id="rejection_reason" name="rejection_reason" rows="4" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required></textarea>
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150" onclick="document.getElementById('rejection-modal').classList.add('hidden')">
                                Cancel
                            </button>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Confirm Rejection
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 