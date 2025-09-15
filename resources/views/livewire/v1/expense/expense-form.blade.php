<div class="max-w-4xl p-2 mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <nav class="flex mb-2" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 text-sm text-gray-500">
                <li><a href="{{ route('expense.index') }}" class="hover:text-gray-700">Expenses</a></li>
                <li class="flex items-center">
                    <svg class="w-4 h-4 mx-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-gray-700">{{ $isEdit ? 'Edit Expense' : 'Add Expense' }}</span>
                </li>
            </ol>
        </nav>
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">{{ $this->title() }}</h1>
    </div>

    <!-- Form -->
    <form wire:submit="save" class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">

        <!-- Lease Selection -->
        <div class="mb-8">
            <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">Expense Information</h3>

            <div class="grid grid-cols-1 gap-6">
                <!-- Lease Selection -->
                <div>
                    <label for="lease_id" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Select Property <span class="text-red-500">*</span>
                    </label>
                    <select wire:model.live="property_id" id="property_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Choose a Property</option>
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

            </div>
        </div>

        <!-- Payment Details -->
        <div class="mb-8">
            <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">Expense Details</h3>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <!-- Payment Date -->
                <div>
                    <label for="payment_date" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" wire:model="expense_date" id="expense_date"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                    @error('expense_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Rent Period -->
                <div>
                    <label for="category" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Select Category <span class="text-red-500">*</span>
                    </label>
                    <select wire:model.live="category" id="category"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Choose a Category</option>
                        @foreach ($this->expenseCategories as $key => $value)
                            <option value="{{ $key }}">
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                    @error('property_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Amount Paid -->
                <div>
                    <label for="amount" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Amount <span class="text-red-500">*</span>
                    </label>
                    <input type="number" wire:model="amount" id="amount" step="0.01" placeholder="0.00"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                    @error('amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>


            </div>
        </div>

        <!-- Additional Information -->
        <div class="mb-8">
            <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">Additional Information</h3>

            <div>
                <label for="description" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                    Description
                </label>
                <textarea wire:model="description" id="description" rows="4"
                    placeholder="Any additional description about this expense..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-3 mt-8">
            <button type="button" wire:click="cancel"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:bg-gray-600 dark:text-gray-300 dark:border-gray-500 dark:hover:bg-gray-700">
                Cancel
            </button>
            <button type="submit"
                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                wire:loading.attr="disabled">
                <span wire:loading.remove>{{ $isEdit ? 'Update Expense' : 'Add Expense' }}</span>
                <span wire:loading>{{ $isEdit ? 'Updating...' : 'Recording...' }}</span>
            </button>
        </div>
    </form>
</div>
