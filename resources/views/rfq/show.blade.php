<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Request for Quotation Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <div class="mb-4">
                <a href="{{ route('rfq.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    &larr; Back to RFQs
                </a>
            </div>
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">{{ $rfq->rfq_number }}</h3>
                        <div class="space-x-2">
                            @if($rfq->status === 'draft')
                                <a href="{{ route('rfq.edit', $rfq) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Edit
                                </a>
                                <form action="{{ route('rfq.publish', $rfq) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Publish
                                    </button>
                                </form>
                            @elseif($rfq->status === 'published')
                                <form action="{{ route('rfq.close', $rfq) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-500 focus:bg-yellow-500 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Close
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Status</h4>
                            <p class="mt-1">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($rfq->status === 'draft') bg-yellow-100 text-yellow-800
                                    @elseif($rfq->status === 'published') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($rfq->status) }}
                                </span>
                            </p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Created By</h4>
                            <p class="mt-1">{{ $rfq->creator->name }}</p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Purchase Request</h4>
                            <p class="mt-1">{{ $rfq->purchaseRequest->pr_number }}: {{ $rfq->purchaseRequest->title }}</p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Department</h4>
                            <p class="mt-1">{{ $rfq->purchaseRequest->department }}</p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">RFQ Date</h4>
                            <p class="mt-1">{{ $rfq->rfq_date->format('F d, Y') }}</p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Deadline</h4>
                            <p class="mt-1">{{ $rfq->deadline->format('F d, Y') }}</p>
                        </div>
                        
                        <div class="col-span-2">
                            <h4 class="text-sm font-medium text-gray-500">Purpose</h4>
                            <p class="mt-1">{{ $rfq->purpose ?? 'N/A' }}</p>
                        </div>
                        
                        <div class="col-span-2">
                            <h4 class="text-sm font-medium text-gray-500">Notes</h4>
                            <p class="mt-1">{{ $rfq->notes ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Supplier Quotations Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Supplier Quotations</h3>
                        @if($rfq->status === 'published')
                            <button type="button" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150" onclick="document.getElementById('add-quotation-modal').classList.remove('hidden')">
                                Record Quotation
                            </button>
                        @endif
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Quotation No.</th>
                                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rfq->supplierQuotations as $quotation)
                                <tr>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                        <div class="text-sm leading-5 text-gray-900">{{ $quotation->quotation_number }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                        <div class="text-sm leading-5 text-gray-900">{{ $quotation->supplier->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                        <div class="text-sm leading-5 text-gray-900">{{ $quotation->quotation_date->format('M d, Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                        <div class="text-sm leading-5 text-gray-900">₱ {{ number_format($quotation->total_amount, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                        <div class="text-sm leading-5">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($quotation->is_awarded) bg-green-100 text-green-800
                                                @elseif($quotation->status === 'rejected') bg-red-100 text-red-800
                                                @elseif($quotation->status === 'evaluated') bg-blue-100 text-blue-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                @if($quotation->is_awarded) Awarded @else {{ ucfirst($quotation->status) }} @endif
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('supplier-quotations.show', $quotation) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                            
                                            @if($rfq->status === 'closed' && !$quotation->is_awarded)
                                                <form action="{{ route('supplier-quotations.award', $quotation) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-900">Award</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-center">
                                        <div class="text-sm leading-5 text-gray-900">No supplier quotations recorded yet.</div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Add Quotation Modal -->
            <div id="add-quotation-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden z-50">
                <div class="bg-white rounded-lg overflow-hidden shadow-xl w-full max-w-3xl">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Record Supplier Quotation</h3>
                            <button type="button" class="text-gray-500 hover:text-gray-700" onclick="document.getElementById('add-quotation-modal').classList.add('hidden')">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        
                        <form action="{{ route('supplier-quotations.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="request_for_quotation_id" value="{{ $rfq->id }}">
                            
                            <div class="mb-4">
                                <x-input-label for="supplier_id" :value="__('Supplier')" />
                                <select id="supplier_id" name="supplier_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required>
                                    <option value="">Select a Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="mb-4">
                                <x-input-label for="quotation_date" :value="__('Quotation Date')" />
                                <x-text-input id="quotation_date" class="block mt-1 w-full" type="date" name="quotation_date" :value="old('quotation_date', date('Y-m-d'))" required />
                            </div>
                            
                            <div class="mb-4">
                                <x-input-label for="total_amount" :value="__('Total Amount (₱)')" />
                                <x-text-input id="total_amount" class="block mt-1 w-full" type="number" name="total_amount" :value="old('total_amount')" required step="0.01" min="0" />
                            </div>
                            
                            <div class="mb-4">
                                <x-input-label for="document_path" :value="__('Quotation Document (optional)')" />
                                <input id="document_path" type="file" name="document_path" class="block mt-1 w-full p-2 border border-gray-300 rounded-md">
                            </div>
                            
                            <div class="mb-4">
                                <x-input-label for="remarks" :value="__('Remarks')" />
                                <textarea id="remarks" name="remarks" rows="3" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">{{ old('remarks') }}</textarea>
                            </div>
                            
                            <div class="flex justify-end space-x-2">
                                <button type="button" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150" onclick="document.getElementById('add-quotation-modal').classList.add('hidden')">
                                    Cancel
                                </button>
                                <x-primary-button>
                                    {{ __('Record Quotation') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 