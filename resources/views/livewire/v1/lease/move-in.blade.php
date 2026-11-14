@section('title', 'Move in')

@php
    $input = 'h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90';
    $label = 'mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400';
@endphp

<div class="space-y-5 sm:space-y-6">
    <div>
        <a href="{{ route('property.show', $property) }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">
            ← {{ $property->property_name }}
        </a>
        <h2 class="mt-1 text-2xl font-semibold text-gray-800 dark:text-white/90">Move a tenant into Unit {{ $unit->unit_name }}</h2>
    </div>

    <form wire:submit="save" class="space-y-5 sm:space-y-6">
        {{-- Who --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
            <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-base font-medium text-gray-800 dark:text-white/90">Who is moving in?</h3>
                <div class="inline-flex rounded-lg border border-gray-200 p-1 dark:border-gray-800">
                    @foreach (['new' => 'New tenant', 'existing' => 'Existing tenant'] as $mode => $text)
                        <button type="button" wire:click="$set('tenantMode', '{{ $mode }}')"
                            class="rounded-md px-3 py-1.5 text-sm font-medium {{ $tenantMode === $mode ? 'bg-blue-600 text-white' : 'text-gray-600 dark:text-gray-300' }}">
                            {{ $text }}
                        </button>
                    @endforeach
                </div>
            </div>

            @if ($tenantMode === 'existing')
                <label for="tenant_id" class="{{ $label }}">Tenant <span class="text-red-500">*</span></label>
                <select wire:model="tenant_id" id="tenant_id" class="{{ $input }}">
                    <option value="">Choose a tenant</option>
                    @foreach ($this->tenants as $tenant)
                        <option value="{{ $tenant->id }}">{{ $tenant->first_name }} {{ $tenant->last_name }} · {{ $tenant->phone }}</option>
                    @endforeach
                </select>
                @error('tenant_id') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
            @else
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label for="first_name" class="{{ $label }}">First name <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="first_name" id="first_name" class="{{ $input }}">
                        @error('first_name') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="last_name" class="{{ $label }}">Last name</label>
                        <input type="text" wire:model="last_name" id="last_name" class="{{ $input }}">
                        @error('last_name') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="phone" class="{{ $label }}">Phone <span class="text-red-500">*</span></label>
                        <input type="tel" wire:model="phone" id="phone" class="{{ $input }}">
                        @error('phone') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="email" class="{{ $label }}">Email <span class="text-xs font-normal text-gray-400">(for receipts)</span></label>
                        <input type="email" wire:model="email" id="email" class="{{ $input }}">
                        @error('email') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="national_id" class="{{ $label }}">National ID</label>
                        <input type="text" wire:model="national_id" id="national_id" class="{{ $input }}">
                        @error('national_id') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
                        <p class="mt-1 text-xs text-gray-400">You can add more details later from Tenants.</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Rent --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
            <h3 class="mb-5 text-base font-medium text-gray-800 dark:text-white/90">Rent</h3>
            <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                <div>
                    <label for="rent_amount" class="{{ $label }}">Monthly rent (৳) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" wire:model="rent_amount" id="rent_amount" class="{{ $input }}">
                    @error('rent_amount') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="service_charge" class="{{ $label }}">Service charge (৳)</label>
                    <input type="number" step="0.01" wire:model="service_charge" id="service_charge" class="{{ $input }}">
                    @error('service_charge') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="security_deposit" class="{{ $label }}">Deposit received (৳)</label>
                    <input type="number" step="0.01" wire:model="security_deposit" id="security_deposit" class="{{ $input }}">
                    @error('security_deposit') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="start_date" class="{{ $label }}">Move-in date <span class="text-red-500">*</span></label>
                    <input type="date" wire:model="start_date" id="start_date" class="{{ $input }}">
                    @error('start_date') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="end_date" class="{{ $label }}">Agreement ends</label>
                    <input type="date" wire:model="end_date" id="end_date" class="{{ $input }}">
                    @error('end_date') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
                    <p class="mt-1 text-xs text-gray-400">Leave empty for month-to-month.</p>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('property.show', $property) }}"
                class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">Cancel</a>
            <button type="submit" wire:loading.attr="disabled"
                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50">
                Move in
            </button>
        </div>
    </form>
</div>
