@section('title', $isEdit ? 'Edit Payment' : 'Record Payment')

<div class="space-y-5 sm:space-y-6">
    <!-- Breadcrumb Card -->
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="px-5 py-4 sm:px-6 sm:py-5">
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-sm text-gray-500 dark:text-gray-400">
                    <li><a href="{{ route('payment.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300">Payments</a></li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 mx-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700 dark:text-gray-300">{{ $isEdit ? 'Edit Payment' : 'Record Payment' }}</span>
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
                <!-- Lease Selection -->
                <div class="mb-8">
                    <h4 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">Lease Information</h4>

                    <div class="grid grid-cols-1 gap-6">
                        <!-- Lease Selection -->
                        <div>
                            <label for="lease_id" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Select Lease Agreement <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="lease_id" id="lease_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Choose a lease agreement</option>
                                @foreach ($this->activeLeases as $lease)
                                    <option value="{{ $lease['id'] }}">
                                        {{ $lease['display'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('lease_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Lease Details Display -->
                        @if ($this->selectedLease)
                            <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700">
                                <h5 class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Lease Details</h5>
                                <div class="grid grid-cols-1 gap-4 text-sm md:grid-cols-3">
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Tenant:</span>
                                        <p class="font-medium text-gray-900 dark:text-white">
                                            {{ $this->selectedLease->tenant->first_name }}
                                            {{ $this->selectedLease->tenant->last_name }}
                                        </p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Monthly Rent:</span>
                                        <p class="font-medium text-gray-900 dark:text-white">
                                            ৳{{ number_format($this->selectedLease->rent_amount, 2) }}
                                        </p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Service Charge:</span>
                                        <p class="font-medium text-gray-900 dark:text-white">
                                            ৳{{ number_format($this->selectedLease->service_charge ?? 0, 2) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="pt-3 mt-3 border-t border-gray-200 dark:border-gray-600">
                                    <span class="text-gray-500 dark:text-gray-400">Total Monthly Amount:</span>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                        ৳{{ number_format($this->selectedLease->rent_amount + ($this->selectedLease->service_charge ?? 0), 2) }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="mb-8">
                    <h4 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">Payment Details</h4>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- Payment Date -->
                        <div>
                            <label for="payment_date" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Payment Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" wire:model="payment_date" id="payment_date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                            @error('payment_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Rent Period -->
                        <div>
                            <label for="rent_period" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Rent Period (Month) <span class="text-red-500">*</span>
                            </label>
                            <input type="month" wire:model="rent_period" id="rent_period"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                            @error('rent_period')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Select the month for which rent is being paid</p>
                        </div>

                        <!-- Amount Paid -->
                        <div>
                            <label for="amount_paid" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Amount Paid <span class="text-red-500">*</span>
                            </label>
                            <input type="number" wire:model="amount_paid" id="amount_paid" step="0.01" placeholder="0.00"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                            @error('amount_paid')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Payment Method -->
                        <div>
                            <label for="payment_method" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Payment Method <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="payment_method" id="payment_method"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="mobile_payment">Mobile Payment (bKash/Nagad)</option>
                                <option value="cheque">Cheque</option>
                            </select>
                            @error('payment_method')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Payment Status -->
                        <div>
                            <label for="status" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Payment Status <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="status" id="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="paid">Paid in Full</option>
                                <option value="partial">Partial Payment</option>
                                <option value="unpaid">Unpaid</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="mb-8">
                    <h4 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">Additional Information</h4>

                    <div>
                        <label for="notes" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                            Notes
                        </label>
                        <textarea wire:model="notes" id="notes" rows="4" placeholder="Any additional notes about this payment..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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
                        <span wire:loading.remove>{{ $isEdit ? 'Update Payment' : 'Record Payment' }}</span>
                        <span wire:loading>{{ $isEdit ? 'Updating...' : 'Recording...' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>