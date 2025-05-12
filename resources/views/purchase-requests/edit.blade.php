<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Purchase Request') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('purchase-requests.update', $purchaseRequest) }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <x-input-label for="title" :value="__('Title')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $purchaseRequest->title)" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>
                        
                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $purchaseRequest->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>
                        
                        <div>
                            <x-input-label for="department" :value="__('Department')" />
                            <x-text-input id="department" class="block mt-1 w-full" type="text" name="department" :value="old('department', $purchaseRequest->department)" required />
                            <x-input-error :messages="$errors->get('department')" class="mt-2" />
                        </div>
                        
                        <div>
                            <x-input-label for="estimated_amount" :value="__('Estimated Amount (PHP)')" />
                            <x-text-input id="estimated_amount" class="block mt-1 w-full" type="number" step="0.01" min="1" name="estimated_amount" :value="old('estimated_amount', $purchaseRequest->estimated_amount)" required />
                            <x-input-error :messages="$errors->get('estimated_amount')" class="mt-2" />
                        </div>
                        
                        <div>
                            <x-input-label for="document_path" :value="__('Attachment (PPMP Document)')" />
                            @if ($purchaseRequest->document_path)
                                <div class="mt-1 mb-3">
                                    <p class="text-sm">Current document: 
                                        <a href="{{ Storage::url($purchaseRequest->document_path) }}" target="_blank" class="text-blue-600 hover:text-blue-900">View Document</a>
                                    </p>
                                </div>
                            @endif
                            <input id="document_path" type="file" name="document_path" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" />
                            <p class="mt-1 text-sm text-gray-500">Upload a new PDF, DOC, or DOCX file (max 2MB) to replace the current one</p>
                            <x-input-error :messages="$errors->get('document_path')" class="mt-2" />
                        </div>
                        
                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('purchase-requests.show', $purchaseRequest) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 mr-3">
                                Cancel
                            </a>
                            <x-primary-button>
                                {{ __('Update Purchase Request') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 