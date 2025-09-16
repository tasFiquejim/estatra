@section('title', $isEdit ? 'Edit Lease' : 'Create Lease')

<div class="space-y-5 sm:space-y-6">
    <!-- Breadcrumb Card -->
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="px-5 py-4 sm:px-6 sm:py-5">
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-sm text-gray-500 dark:text-gray-400">
                    <li><a href="{{ route('lease.index') }}"
                            class="hover:text-gray-700 dark:hover:text-gray-300">Leases</a></li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 mx-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span
                            class="text-gray-700 dark:text-gray-300">{{ $isEdit ? 'Edit Lease' : 'Create Lease' }}</span>
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
                <!-- Unit and Tenant Selection -->
                <div class="mb-8">
                    <h4 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">Lease Parties</h4>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- Unit Selection -->
                        <div>
                            <label for="unit_id"
                                class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Select Unit <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="unit_id" id="unit_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Choose a unit</option>
                                @forelse ($this->availableUnits as $propertyName => $units)
                                    <optgroup label="{{ $propertyName }}">
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id }}">
                                                {{ $unit->unit_name }}
                                                @if ($unit->size)
                                                    ({{ $unit->size }})
                                                @endif
                                                @if ($unit->floor_number)
                                                    - Floor {{ $unit->floor_number }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @empty
                                    <option value="" disabled>No available units</option>
                                @endforelse
                            </select>
                            @error('unit_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tenant Selection -->
                        <div>
                            <label for="tenant_id"
                                class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Select Tenant <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="tenant_id" id="tenant_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Choose a tenant</option>
                                @foreach ($this->tenants as $tenant)
                                    <option value="{{ $tenant['id'] }}">
                                        {{ $tenant['name'] }} ({{ $tenant['email'] }})
                                    </option>
                                @endforeach
                            </select>
                            @error('tenant_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Lease Terms -->
                <div class="mb-8">
                    <h4 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">Lease Terms</h4>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <!-- Start Date -->
                        <div>
                            <label for="start_date"
                                class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Lease Start Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" wire:model="start_date" id="start_date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                            @error('start_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- End Date -->
                        <div>
                            <label for="end_date"
                                class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Lease End Date
                            </label>
                            <input type="date" wire:model="end_date" id="end_date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                            @error('end_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leave blank for month-to-month
                                lease</p>
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status"
                                class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Lease Status <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="status" id="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="active">Active</option>
                                <option value="expired">Expired</option>
                                <option value="terminated">Terminated</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Financial Terms -->
                <div class="mb-8">
                    <h4 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">Financial Terms</h4>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <!-- Monthly Rent -->
                        <div>
                            <label for="rent_amount"
                                class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Monthly Rent <span class="text-red-500">*</span>
                            </label>
                            <input type="number" wire:model="rent_amount" id="rent_amount" step="0.01"
                                placeholder="0.00"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                            @error('rent_amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Service Charge -->
                        <div>
                            <label for="service_charge"
                                class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Monthly Service Charge
                            </label>
                            <input type="number" wire:model="service_charge" id="service_charge" step="0.01"
                                placeholder="0.00"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                            @error('service_charge')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Security Deposit -->
                        <div>
                            <label for="security_deposit"
                                class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Security Deposit
                            </label>
                            <input type="number" wire:model="security_deposit" id="security_deposit" step="0.01"
                                placeholder="0.00"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                            @error('security_deposit')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Total Monthly Amount Display -->
                    @if ($rent_amount || $service_charge)
                        <div class="p-4 mt-4 rounded-lg bg-gray-50 dark:bg-gray-700">
                            <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300">Monthly Total</h5>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                ৳{{ number_format((float) $rent_amount + (float) $service_charge, 2) }}
                                <span class="text-sm font-normal text-gray-500">
                                    (Rent: ৳{{ number_format((float) $rent_amount, 2) }}
                                    @if ($service_charge)
                                        + Service: ৳{{ number_format((float) $service_charge, 2) }}
                                    @endif
                                    )
                                </span>
                            </p>
                        </div>
                    @endif
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
                        <span wire:loading.remove>{{ $isEdit ? 'Update Lease' : 'Create Lease' }}</span>
                        <span wire:loading>{{ $isEdit ? 'Updating...' : 'Creating...' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
