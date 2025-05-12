<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Request for Quotation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('rfq.update', $rfq) }}">
                        @csrf
                        @method('PUT')

                        <!-- Purchase Request - Disabled for editing -->
                        <div class="mb-4">
                            <x-input-label for="purchase_request_id" :value="__('Purchase Request')" />
                            <div class="border border-gray-300 rounded-md p-2 bg-gray-100 block mt-1 w-full">
                                {{ $rfq->purchaseRequest->pr_number }}: {{ $rfq->purchaseRequest->title }} ({{ $rfq->purchaseRequest->department }})
                            </div>
                        </div>

                        <!-- Purpose -->
                        <div class="mb-4">
                            <x-input-label for="purpose" :value="__('Purpose')" />
                            <textarea id="purpose" name="purpose" rows="3" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">{{ old('purpose', $rfq->purpose) }}</textarea>
                            <x-input-error :messages="$errors->get('purpose')" class="mt-2" />
                        </div>

                        <!-- RFQ Date -->
                        <div class="mb-4">
                            <x-input-label for="rfq_date" :value="__('RFQ Date')" />
                            <x-text-input id="rfq_date" class="block mt-1 w-full" type="date" name="rfq_date" :value="old('rfq_date', $rfq->rfq_date->format('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('rfq_date')" class="mt-2" />
                        </div>

                        <!-- Deadline -->
                        <div class="mb-4">
                            <x-input-label for="deadline" :value="__('Deadline')" />
                            <x-text-input id="deadline" class="block mt-1 w-full" type="date" name="deadline" :value="old('deadline', $rfq->deadline->format('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('deadline')" class="mt-2" />
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <x-input-label for="notes" :value="__('Notes')" />
                            <textarea id="notes" name="notes" rows="3" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">{{ old('notes', $rfq->notes) }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('rfq.show', $rfq) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            
                            <x-primary-button>
                                {{ __('Update RFQ') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 