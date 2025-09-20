@section('title', $isEdit ? 'Edit Unit' : 'Add Unit')

<div class="space-y-5 sm:space-y-6">
    <!-- Breadcrumb Card -->
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="px-5 py-4 sm:px-6 sm:py-5">
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-sm text-gray-500 dark:text-gray-400">
                    <li>
                        <a href="{{ route('property.index') }}"
                            class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                            Properties
                        </a>
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 mx-1.5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('property.show', $property) }}"
                            class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                            {{ $property->property_name }}
                        </a>
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 mx-1.5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="font-medium text-gray-800 dark:text-white/90">
                            {{ $isEdit ? 'Edit Unit' : 'Add Unit' }}
                        </span>
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
                <!-- Unit Information Section -->
                <div class="mb-8">
                    <h4 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">Unit Information</h4>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- Unit Name -->
                        <div>
                            <label for="unit_name"
                                class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Unit Name/Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="unit_name" id="unit_name"
                                placeholder="e.g., 101, A-1, Studio 1"
                                class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-3 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                            @error('unit_name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Floor Number -->
                        <div>
                            <label for="floor_number"
                                class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Floor Number
                            </label>
                            <input type="text" wire:model="floor_number" id="floor_number"
                                placeholder="e.g., 1st, Ground, Basement"
                                class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-3 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                            @error('floor_number')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Size -->
                        <div>
                            <label for="size"
                                class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Size/Area
                            </label>
                            <input type="text" wire:model="size" id="size"
                                placeholder="e.g., 1200 sqft, 2BR/2BA"
                                class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-3 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                            @error('size')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status"
                                class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="status" id="status"
                                class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-3 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                <option value="">Select status</option>
                                <option value="available">Available</option>
                                <option value="occupied">Occupied</option>
                                <option value="maintenance">Under Maintenance</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Additional Information Section -->
                <div class="mb-8">
                    <h4 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">Additional Information</h4>

                    <div class="grid grid-cols-1 gap-6">
                        <!-- Notes -->
                        <div>
                            <label for="notes"
                                class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Notes
                            </label>
                            <textarea wire:model="notes" id="notes" rows="4" placeholder="Additional notes about this unit..."
                                class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-3 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"></textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div
                    class="flex flex-col gap-3 pt-6 border-t border-gray-200 dark:border-gray-700 sm:flex-row sm:justify-end">
                    <button type="button" wire:click="cancel"
                        class="shadow-theme-xs inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-hidden focus:ring-2 focus:ring-gray-200 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-800 dark:focus:ring-gray-600">
                        Cancel
                    </button>
                    <button type="submit"
                        class="shadow-theme-xs inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700 focus:outline-hidden focus:ring-2 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove>{{ $isEdit ? 'Update Unit' : 'Create Unit' }}</span>
                        <span wire:loading>{{ $isEdit ? 'Updating...' : 'Creating...' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
