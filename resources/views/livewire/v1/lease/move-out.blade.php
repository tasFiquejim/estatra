@section('title', 'Move out')

@php
    $input = 'h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90';
    $label = 'mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400';
    $tenant = $lease->tenant;
    $unpaidTotal = $this->unpaidTotal();
@endphp

<div class="space-y-5 sm:space-y-6">
    <div>
        <a href="{{ route('property.show', $lease->unit->property) }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">
            ← {{ $lease->unit->property->property_name }}
        </a>
        <h2 class="mt-1 text-2xl font-semibold text-gray-800 dark:text-white/90">
            {{ $tenant?->first_name }} {{ $tenant?->last_name }} is moving out of Unit {{ $lease->unit->unit_name }}
        </h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Living here since {{ $lease->start_date->format('j M Y') }} · ৳{{ number_format($lease->monthlyCharge(), 2) }} a month
        </p>
    </div>

    {{-- Money to settle --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-sm text-gray-500 dark:text-gray-400">Deposit held</p>
            <p class="mt-1 text-2xl font-bold text-gray-800 dark:text-white">৳{{ number_format($this->deposit(), 2) }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-sm text-gray-500 dark:text-gray-400">Rent still owed</p>
            <p class="mt-1 text-2xl font-bold {{ $unpaidTotal > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-800 dark:text-white' }}">
                ৳{{ number_format($unpaidTotal, 2) }}
            </p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-sm text-gray-500 dark:text-gray-400">Suggested deposit to return</p>
            <p class="mt-1 text-2xl font-bold text-gray-800 dark:text-white">৳{{ number_format($this->suggestedReturn(), 2) }}</p>
        </div>
    </div>

    @if ($this->unpaid->isNotEmpty())
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="border-b border-gray-100 px-5 py-3 text-sm font-medium text-gray-800 dark:border-gray-800 dark:text-white/90">Unpaid months</p>
            <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach ($this->unpaid as $line)
                    <li class="flex items-center justify-between px-5 py-3 text-sm">
                        <span class="text-gray-700 dark:text-gray-300">{{ $line->month->format('F Y') }}</span>
                        <span class="font-medium text-red-600 dark:text-red-400">
                            ৳{{ number_format($line->balance(), 2) }}
                            @if ($line->paid > 0)
                                <span class="font-normal text-gray-400">(৳{{ number_format($line->paid, 2) }} paid)</span>
                            @endif
                        </span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <form wire:submit="save" class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <div>
                <label for="move_out_date" class="{{ $label }}">Move-out date <span class="text-red-500">*</span></label>
                <input type="date" wire:model.live="move_out_date" id="move_out_date" class="{{ $input }}">
                @error('move_out_date') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="deposit_returned" class="{{ $label }}">Deposit returned (৳)</label>
                <input type="number" step="0.01" wire:model="deposit_returned" id="deposit_returned" class="{{ $input }}">
                @error('deposit_returned') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('property.show', $lease->unit->property) }}"
                class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">Cancel</a>
            <button type="submit" wire:loading.attr="disabled"
                wire:confirm="Move {{ $tenant?->first_name }} out? Unit {{ $lease->unit->unit_name }} will become free."
                class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 disabled:opacity-50">
                Move out
            </button>
        </div>
    </form>
</div>
