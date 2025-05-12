<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Review Purchase Request') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Purchase Request Details</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <p class="text-sm font-medium text-gray-500">PR Number</p>
                            <p class="mt-1">{{ $purchaseRequest->pr_number }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Status</p>
                            <p class="mt-1">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $purchaseRequest->status === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                                {{ $purchaseRequest->status === 'submitted' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $purchaseRequest->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $purchaseRequest->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($purchaseRequest->status) }}
                                </span>
                            </p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Title</p>
                            <p class="mt-1">{{ $purchaseRequest->title }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Department</p>
                            <p class="mt-1">{{ $purchaseRequest->department }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Requested By</p>
                            <p class="mt-1">{{ $purchaseRequest->user->name }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Estimated Amount</p>
                            <p class="mt-1">₱{{ number_format($purchaseRequest->estimated_amount, 2) }}</p>
                        </div>
                        
                        <div class="md:col-span-2">
                            <p class="text-sm font-medium text-gray-500">Description</p>
                            <p class="mt-1">{{ $purchaseRequest->description ?? 'No description provided.' }}</p>
                        </div>
                        
                        @if($purchaseRequest->document_path)
                        <div class="md:col-span-2">
                            <p class="text-sm font-medium text-gray-500">Attached Document</p>
                            <p class="mt-1">
                                <a href="{{ asset('storage/' . $purchaseRequest->document_path) }}" target="_blank" class="text-blue-600 hover:text-blue-900">
                                    View Document
                                </a>
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Budget Approval</h3>
                    
                    <form action="{{ route('budget-approvals.store', $purchaseRequest) }}" method="POST">
                        @csrf
                        
                        <div class="mb-6">
                            <label for="status" class="block text-sm font-medium text-gray-700">Decision</label>
                            <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required onchange="showHideFields()">
                                <option value="">Select a decision</option>
                                <option value="approved">Approve</option>
                                <option value="rejected">Reject</option>
                                <option value="revised">Request Revision</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div id="approve-fields" class="hidden">
                            <div class="mb-6">
                                <label for="approved_amount" class="block text-sm font-medium text-gray-700">Approved Amount</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">₱</span>
                                    </div>
                                    <input type="number" step="0.01" min="0" id="approved_amount" name="approved_amount" class="block w-full pl-7 pr-12 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('approved_amount', $purchaseRequest->estimated_amount) }}">
                                </div>
                                @error('approved_amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="mb-6">
                                <label for="fund_source" class="block text-sm font-medium text-gray-700">Fund Source</label>
                                <input type="text" id="fund_source" name="fund_source" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('fund_source') }}">
                                @error('fund_source')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="mb-6">
                                <label for="budget_code" class="block text-sm font-medium text-gray-700">Budget Code</label>
                                <input type="text" id="budget_code" name="budget_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('budget_code') }}">
                                @error('budget_code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes/Reason</label>
                            <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('budget-approvals.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Submit Decision
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function showHideFields() {
            const status = document.getElementById('status').value;
            const approveFields = document.getElementById('approve-fields');
            
            if (status === 'approved') {
                approveFields.classList.remove('hidden');
            } else {
                approveFields.classList.add('hidden');
            }
        }
    </script>
</x-app-layout> 