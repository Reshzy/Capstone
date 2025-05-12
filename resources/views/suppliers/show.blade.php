<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Supplier Details') }}
            </h2>
            <div class="mt-3 md:mt-0 space-x-2">
                <a href="{{ route('suppliers.edit', $supplier) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    {{ __('Edit Supplier') }}
                </a>
                <a href="{{ route('suppliers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    {{ __('Back to List') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Session Messages -->
            @if (session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
            @endif

            @if (session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
            @endif

            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Supplier Details Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 lg:w-1/2">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Supplier Information') }}</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">{{ __('Supplier Name') }}</h4>
                            <p class="text-base">{{ $supplier->name }}</p>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500">{{ __('Status') }}</h4>
                            <p>
                                @if($supplier->is_active)
                                <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">
                                    {{ __('Active') }}
                                </span>
                                @else
                                <span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded-full">
                                    {{ __('Inactive') }}
                                </span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500">{{ __('Contact Person') }}</h4>
                            <p class="text-base">{{ $supplier->contact_person ?? __('Not specified') }}</p>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500">{{ __('Email') }}</h4>
                            <p class="text-base">{{ $supplier->email ?? __('Not specified') }}</p>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500">{{ __('Phone') }}</h4>
                            <p class="text-base">{{ $supplier->phone ?? __('Not specified') }}</p>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500">{{ __('Tax ID') }}</h4>
                            <p class="text-base">{{ $supplier->tax_id ?? __('Not specified') }}</p>
                        </div>

                        <div class="md:col-span-2">
                            <h4 class="text-sm font-medium text-gray-500">{{ __('Address') }}</h4>
                            <p class="text-base">{{ $supplier->address ?? __('Not specified') }}</p>
                        </div>

                        <div class="md:col-span-2">
                            <h4 class="text-sm font-medium text-gray-500">{{ __('Categories') }}</h4>
                            <div class="flex flex-wrap gap-1 mt-1">
                                @forelse($supplier->procurementCategories as $category)
                                <span class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full">
                                    {{ $category->name }}
                                </span>
                                @empty
                                <span class="text-sm text-gray-500">{{ __('No categories assigned') }}</span>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex space-x-2">
                        <form method="POST" action="{{ route('suppliers.toggle-status', $supplier) }}" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 {{ $supplier->is_active ? 'bg-orange-600 hover:bg-orange-700' : 'bg-green-600 hover:bg-green-700' }} border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150"
                                onclick="return confirm(this.dataset.message);"
                                data-message="{{ $supplier->is_active ? 'Are you sure you want to deactivate this supplier?' : 'Are you sure you want to activate this supplier?' }}">
                                {{ $supplier->is_active ? __('Deactivate Supplier') : __('Activate Supplier') }}
                            </button>
                        </form>

                        <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                onclick="return confirm('Are you sure you want to delete this supplier? This action cannot be undone.')">
                                {{ __('Delete Supplier') }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Performance Metrics -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 lg:w-1/2">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Performance Metrics') }}</h3>

                    <div class="space-y-4">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">{{ __('Delivery Performance') }}</h4>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2 relative">
                                <div class="bg-blue-600 h-2.5 rounded-full absolute top-0 left-0" id="delivery-bar"></div>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">{{ number_format($performanceSummary['delivery'], 1) }} / 5</p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">{{ __('Quality of Goods/Services') }}</h4>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2 relative">
                                <div class="bg-blue-600 h-2.5 rounded-full absolute top-0 left-0" id="quality-bar"></div>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">{{ number_format($performanceSummary['quality'], 1) }} / 5</p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">{{ __('Price Competitiveness') }}</h4>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2 relative">
                                <div class="bg-blue-600 h-2.5 rounded-full absolute top-0 left-0" id="price-bar"></div>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">{{ number_format($performanceSummary['price'], 1) }} / 5</p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">{{ __('Response & Communication') }}</h4>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2 relative">
                                <div class="bg-blue-600 h-2.5 rounded-full absolute top-0 left-0" id="response-bar"></div>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">{{ number_format($performanceSummary['response'], 1) }} / 5</p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">{{ __('Overall Rating') }}</h4>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2 relative">
                                <div class="bg-blue-600 h-2.5 rounded-full absolute top-0 left-0" id="overall-bar"></div>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">{{ number_format($performanceSummary['overall'], 1) }} / 5</p>
                        </div>
                    </div>
                    
                    <script>
                        // Set progress bar widths
                        document.addEventListener('DOMContentLoaded', function() {
                            document.getElementById('delivery-bar').style.width = '{{ round($performanceSummary['delivery'] * 20) }}%';
                            document.getElementById('quality-bar').style.width = '{{ round($performanceSummary['quality'] * 20) }}%';
                            document.getElementById('price-bar').style.width = '{{ round($performanceSummary['price'] * 20) }}%';
                            document.getElementById('response-bar').style.width = '{{ round($performanceSummary['response'] * 20) }}%';
                            document.getElementById('overall-bar').style.width = '{{ round($performanceSummary['overall'] * 20) }}%';
                        });
                    </script>
                    
                    <div class="mt-6">
                        <a href="{{ route('suppliers.performance-summary', $supplier) }}"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('View Full Performance History') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Purchase History -->
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Purchase History') }}</h3>

                @if($purchaseOrders->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('PR/RFQ Reference') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Date Awarded') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Items') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Total Amount') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($purchaseOrders as $quotation)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        RFQ: {{ $quotation->requestForQuotation->rfq_number }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        PR: {{ $quotation->requestForQuotation->purchaseRequest->pr_number }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $quotation->requestForQuotation->abstractOfQuotation->created_at->format('M d, Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        {{ $quotation->items->count() }} items
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        PHP {{ number_format($quotation->items->sum('total_price'), 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <a href="{{ route('rfq.show', $quotation->requestForQuotation) }}" class="text-indigo-600 hover:text-indigo-900">
                                        {{ __('View RFQ') }}
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-gray-500">{{ __('No purchase history found for this supplier.') }}</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>