<div class="max-w-4xl p-2 mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <nav class="flex mb-2" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 text-sm text-gray-500">
                <li><a href="{{ route('property.index') }}" class="hover:text-gray-700">Properties</a></li>
                <li class="flex items-center">
                    <svg class="w-4 h-4 mx-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="{{ route('property.show', $property) }}" class="hover:text-gray-700">{{ $property->property_name }}</a>
                </li>
                <li class="flex items-center">
                    <svg class="w-4 h-4 mx-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-gray-700">{{ $isEdit ? 'Edit Unit' : 'Add Unit' }}</span>
                </li>
            </ol>
        </nav>
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">{{ $this->title() }}</h1>
    </div>

    <!-- Form -->
    <form wire:submit="save" class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- Unit Name -->
            <div>
                <label for="unit_name" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                    Unit Name/Number <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       wire:model="unit_name" 
                       id="unit_name"
                       placeholder="e.g., 101, A-1, Studio 1"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                @error('unit_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Floor Number -->
            <div>
                <label for="floor_number" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                    Floor Number
                </label>
                <input type="text" 
                       wire:model="floor_number" 
                       id="floor_number"
                       placeholder="e.g., 1st, Ground, Basement"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                @error('floor_number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Size -->
            <div>
                <label for="size" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                    Size/Area
                </label>
                <input type="text" 
                       wire:model="size" 
                       id="size"
                       placeholder="e.g., 1200 sqft, 2BR/2BA"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                @error('size') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                    Status <span class="text-red-500">*</span>
                </label>
                <select wire:model="status" 
                        id="status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="available">Available</option>
                    <option value="occupied">Occupied</option>
                    <option value="maintenance">Under Maintenance</option>
                </select>
                @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Notes -->
        <div class="mt-6">
            <label for="notes" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                Notes
            </label>
            <textarea wire:model="notes" 
                      id="notes"
                      rows="3"
                      placeholder="Additional notes about this unit..."
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
            @error('notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-3 mt-8">
            <button type="button" 
                    wire:click="cancel"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:bg-gray-600 dark:text-gray-300 dark:border-gray-500 dark:hover:bg-gray-700">
                Cancel
            </button>
            <button type="submit" 
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    wire:loading.attr="disabled">
                <span wire:loading.remove>{{ $isEdit ? 'Update Unit' : 'Create Unit' }}</span>
                <span wire:loading>{{ $isEdit ? 'Updating...' : 'Creating...' }}</span>
            </button>
        </div>
    </form>
</div>