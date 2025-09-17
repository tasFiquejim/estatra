@section('title', $isEdit ? 'Edit Maintenance Record' : 'Add Maintenance Record')

<div class="space-y-5 sm:space-y-6">
    <!-- Breadcrumb Card -->
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="px-5 py-4 sm:px-6 sm:py-5">
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-sm text-gray-500 dark:text-gray-400">
                    <li><a href="{{ route('maintenance.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300">Maintenance</a></li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 mx-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700 dark:text-gray-300">{{ $isEdit ? 'Edit Record' : 'Add Record' }}</span>
                    </li>
                </ol>
            </nav>
            <h3 class="text-base font-medium text-gray-800 dark:text-white/90">{{ $this->title() }}</h3>
        </div>
    </div>

    <!-- Form Card -->
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="p-5 sm:p-6">
            <form wire:submit="save">
                <!-- Property and Unit Selection -->
                <div class="mb-8">
                    <h4 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">Location Information</h4>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- Property Selection -->
                        <div>
                            <label for="property_id" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Property <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="property_id" id="property_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Choose a property</option>
                                @foreach ($this->properties as $property)
                                    <option value="{{ $property['id'] }}">
                                        {{ $property['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('property_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Unit Selection -->
                        <div>
                            <label for="unit_id" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Unit (Optional)
                            </label>
                            <select wire:model="unit_id" id="unit_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                @if (!$this->property_id) disabled @endif>
                                <option value="">General property maintenance</option>
                                @foreach ($this->units as $unit)
                                    <option value="{{ $unit['id'] }}">
                                        Unit {{ $unit['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('unit_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leave blank for general property maintenance</p>
                        </div>
                    </div>
                </div>

                <!-- Maintenance Details -->
                <div class="mb-8">
                    <h4 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">Maintenance Details</h4>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- Maintenance Date -->
                        <div>
                            <label for="maintenance_date" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Maintenance Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" wire:model="maintenance_date" id="maintenance_date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                            @error('maintenance_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="status" id="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @foreach ($this->statuses as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Issue Description -->
                    <div class="mt-6">
                        <label for="issue_description" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                            Issue Description <span class="text-red-500">*</span>
                        </label>
                        <textarea wire:model="issue_description" id="issue_description" rows="4"
                            placeholder="Describe the maintenance issue in detail..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                        @error('issue_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Cost Information -->
                <div class="mb-8">
                    <h4 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">Cost Breakdown</h4>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <!-- Product Cost -->
                        <div>
                            <label for="product_cost" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Product/Materials Cost
                            </label>
                            <input type="number" wire:model.live="product_cost" id="product_cost" step="0.01"
                                placeholder="0.00"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                            @error('product_cost')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Service Cost -->
                        <div>
                            <label for="service_cost" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Service/Labor Cost
                            </label>
                            <input type="number" wire:model.live="service_cost" id="service_cost" step="0.01"
                                placeholder="0.00"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                            @error('service_cost')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Total Cost -->
                        <div>
                            <label for="total_cost" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Total Cost
                            </label>
                            <input type="number" wire:model="total_cost" id="total_cost" step="0.01" placeholder="0.00"
                                readonly
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 dark:bg-gray-600 dark:border-gray-600 dark:text-white" />
                            @error('total_cost')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Photo Upload -->
                <div class="mb-8">
                    <h4 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">Photo Documentation</h4>

                    <div>
                        <label for="photo" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                            Upload Photo
                        </label>
                        <input type="file" wire:model="photo" id="photo" accept="image/*"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                        @error('photo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <!-- Photo Preview -->
                        @if ($photo)
                            <div class="mt-4">
                                <img src="{{ $photo->temporaryUrl() }}" alt="Preview" class="max-w-xs rounded-lg shadow">
                            </div>
                        @elseif($isEdit && $maintenanceLog->photo)
                            <div class="mt-4">
                                <img src="{{ asset('storage/' . $maintenanceLog->photo) }}" alt="Current photo"
                                    class="max-w-xs rounded-lg shadow">
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" wire:click="cancel"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:bg-gray-600 dark:text-gray-300 dark:border-gray-500 dark:hover:bg-gray-700">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove>{{ $isEdit ? 'Update Record' : 'Create Record' }}</span>
                        <span wire:loading>{{ $isEdit ? 'Updating...' : 'Creating...' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>