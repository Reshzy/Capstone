<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Supplier') }}
            </h2>
            <div class="mt-3 md:mt-0 space-x-2">
                <a href="{{ route('suppliers.show', $supplier) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                    {{ __('View Details') }}
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
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('suppliers.update', $supplier) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Supplier Name -->
                        <div>
                            <x-input-label for="name" :value="__('Supplier Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $supplier->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        
                        <!-- Contact Person -->
                        <div>
                            <x-input-label for="contact_person" :value="__('Contact Person')" />
                            <x-text-input id="contact_person" class="block mt-1 w-full" type="text" name="contact_person" :value="old('contact_person', $supplier->contact_person)" />
                            <x-input-error :messages="$errors->get('contact_person')" class="mt-2" />
                        </div>
                        
                        <!-- Email -->
                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $supplier->email)" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                        
                        <!-- Phone -->
                        <div>
                            <x-input-label for="phone" :value="__('Phone')" />
                            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone', $supplier->phone)" />
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>
                        
                        <!-- Tax ID -->
                        <div>
                            <x-input-label for="tax_id" :value="__('Tax ID')" />
                            <x-text-input id="tax_id" class="block mt-1 w-full" type="text" name="tax_id" :value="old('tax_id', $supplier->tax_id)" />
                            <x-input-error :messages="$errors->get('tax_id')" class="mt-2" />
                        </div>
                        
                        <!-- Status -->
                        <div>
                            <x-input-label for="is_active" :value="__('Status')" />
                            <select id="is_active" name="is_active" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="1" {{ old('is_active', $supplier->is_active) ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active', $supplier->is_active) ? '' : 'selected' }}>Inactive</option>
                            </select>
                            <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                        </div>
                        
                        <!-- Address -->
                        <div class="md:col-span-2">
                            <x-input-label for="address" :value="__('Address')" />
                            <textarea id="address" name="address" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('address', $supplier->address) }}</textarea>
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>
                        
                        <!-- Categories -->
                        <div class="md:col-span-2">
                            <x-input-label :value="__('Procurement Categories')" />
                            <div class="mt-2 grid grid-cols-1 md:grid-cols-3 gap-2">
                                @foreach($categories as $category)
                                    <div class="flex items-center">
                                        <input id="category_{{ $category->id }}" type="checkbox" name="category_ids[]" value="{{ $category->id }}" 
                                            {{ in_array($category->id, old('category_ids', $selectedCategories)) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <label for="category_{{ $category->id }}" class="ml-2 text-sm text-gray-600">{{ $category->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('category_ids')" class="mt-2" />
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <x-primary-button>
                            {{ __('Update Supplier') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout> 