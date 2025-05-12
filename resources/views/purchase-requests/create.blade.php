<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Purchase Request') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('purchase-requests.store') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        
                        <div>
                            <x-input-label for="title" :value="__('Title')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>
                        
                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>
                        
                        <div>
                            <x-input-label for="department" :value="__('Department')" />
                            <x-text-input id="department" class="block mt-1 w-full" type="text" name="department" :value="old('department', auth()->user()->department)" required />
                            <x-input-error :messages="$errors->get('department')" class="mt-2" />
                        </div>
                        
                        <div>
                            <x-input-label for="estimated_amount" :value="__('Estimated Amount (PHP)')" />
                            <x-text-input id="estimated_amount" class="block mt-1 w-full" type="number" step="0.01" min="1" name="estimated_amount" :value="old('estimated_amount')" required />
                            <x-input-error :messages="$errors->get('estimated_amount')" class="mt-2" />
                        </div>
                        
                        <div>
                            <x-input-label for="document_path" :value="__('Attachment (PPMP Document)')" />
                            <input id="document_path" type="file" name="document_path" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" />
                            <p class="mt-1 text-sm text-gray-500">Upload a PDF, DOC, or DOCX file (max 2MB)</p>
                            <x-input-error :messages="$errors->get('document_path')" class="mt-2" />
                        </div>
                        
                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('purchase-requests.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 mr-3">
                                Cancel
                            </a>
                            <x-primary-button>
                                {{ __('Create Purchase Request') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 