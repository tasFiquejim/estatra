@section('title', 'Rent')

@php
    $summary = $this->summary;
    $percent = $summary['expected'] > 0 ? min(100, round(($summary['collected'] / $summary['expected']) * 100)) : 0;
    $monthLabel = $this->period()->format('F Y');
@endphp

<div class="space-y-5 sm:space-y-6">

    {{-- Header: title + month switcher --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-white/90">Rent</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Who has paid, and who still owes you.</p>
        </div>

        <div class="inline-flex items-center rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <button type="button" wire:click="previousMonth" aria-label="Previous month"
                class="px-3 py-2 text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <span class="min-w-[9rem] px-2 text-center text-sm font-semibold text-gray-800 dark:text-white">{{ $monthLabel }}</span>
            <button type="button" wire:click="nextMonth" aria-label="Next month" @disabled(! $this->canGoForward())
                class="px-3 py-2 text-gray-500 hover:text-gray-800 disabled:opacity-30 dark:text-gray-400 dark:hover:text-white">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Summary --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Collected</p>
                <p class="mt-1 text-2xl font-bold text-gray-800 dark:text-white">৳{{ number_format($summary['collected'], 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Still owed</p>
                <p class="mt-1 text-2xl font-bold {{ $summary['outstanding'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-800 dark:text-white' }}">
                    ৳{{ number_format($summary['outstanding'], 2) }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Expected</p>
                <p class="mt-1 text-2xl font-bold text-gray-800 dark:text-white">৳{{ number_format($summary['expected'], 2) }}</p>
            </div>
        </div>

        <div class="mt-5 h-2 w-full rounded-full bg-gray-100 dark:bg-gray-800">
            <div class="h-2 rounded-full bg-green-500 transition-all duration-500" style="width: {{ $percent }}%"></div>
        </div>
        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
            @if ($summary['lease_count'] === 0)
                No tenants were renting in {{ $monthLabel }}.
            @elseif ($summary['due_count'] === 0)
                Everyone has paid for {{ $monthLabel }}.
            @else
                {{ $summary['due_count'] }} of {{ $summary['lease_count'] }}
                {{ Str::plural('tenant', $summary['lease_count']) }} still {{ $summary['due_count'] === 1 ? 'owes' : 'owe' }} rent for {{ $monthLabel }}.
            @endif
        </p>
    </div>

    @if ($notice)
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-500/20 dark:bg-green-500/10 dark:text-green-400">
            {{ $notice }}
        </div>
    @endif

    {{-- One row per tenant --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <ul class="divide-y divide-gray-100 dark:divide-gray-800">
            @forelse ($this->lines as $line)
                @php
                    $lease = $line->lease;
                    $status = $line->status();
                    $badge = match ($status) {
                        \App\Enums\PaymentStatus::Paid => 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400',
                        \App\Enums\PaymentStatus::Partial => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400',
                        \App\Enums\PaymentStatus::Unpaid => 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400',
                    };
                @endphp
                <li wire:key="lease-{{ $lease->id }}" class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-medium text-gray-800 dark:text-white">
                            {{ $lease->tenant?->first_name }} {{ $lease->tenant?->last_name }}
                        </p>
                        <p class="truncate text-xs text-gray-500 dark:text-gray-400">
                            {{ $lease->unit->property->property_name }} · Unit {{ $lease->unit->unit_name }}
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 sm:flex-nowrap sm:justify-end">
                        <div class="text-left sm:text-right">
                            <p class="text-sm font-semibold text-gray-800 dark:text-white">
                                @if ($status === \App\Enums\PaymentStatus::Partial)
                                    ৳{{ number_format($line->paid, 2) }}
                                    <span class="font-normal text-gray-400">of ৳{{ number_format($line->expected, 2) }}</span>
                                @else
                                    ৳{{ number_format($line->expected, 2) }}
                                @endif
                            </p>
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $badge }}">
                                {{ $status->label() }}
                                @if ($status === \App\Enums\PaymentStatus::Partial)
                                    · ৳{{ number_format($line->balance(), 2) }} left
                                @endif
                            </span>
                        </div>

                        @unless ($line->isSettled())
                            <button type="button" wire:click="openPayment({{ $lease->id }})"
                                wire:loading.attr="disabled" wire:target="openPayment({{ $lease->id }})"
                                class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-medium text-white hover:bg-blue-700 disabled:opacity-50">
                                Mark paid
                            </button>
                        @endunless
                    </div>
                </li>
            @empty
                <li class="flex flex-col items-center px-5 py-12 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">No tenants were renting in {{ $monthLabel }}.</p>
                    <a href="{{ route('property.index') }}" class="mt-2 text-sm text-blue-600 hover:underline dark:text-blue-400">Open a property to move a tenant in</a>
                </li>
            @endforelse
        </ul>
    </div>
    {{-- "Mark paid" pop-up --}}
    @php
        $field = 'h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90';
        $paying = $this->paying;
        $leftAfter = $paying ? max(0, round($paying->balance() - (float) ($payAmount ?: 0), 2)) : 0;
    @endphp
    {{-- Shown while $payingLeaseId is set, so Livewire alone decides when it's open. --}}
    @if ($paying)
        <div class="fixed inset-0 z-999999 flex items-end justify-center overflow-y-auto p-4 sm:items-center"
            role="dialog" aria-modal="true" aria-labelledby="record-rent-title"
            x-data x-init="$nextTick(() => $refs.amount.focus())"
            x-on:keydown.escape.window="$wire.closePayment()">
            <div class="fixed inset-0 bg-gray-900/60" wire:click="closePayment"></div>

            <form wire:submit="confirmPayment"
                class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900">
                <h3 id="record-rent-title" class="text-lg font-semibold text-gray-800 dark:text-white">Record rent</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $paying->lease->tenant?->first_name }} {{ $paying->lease->tenant?->last_name }}
                    · {{ $paying->lease->unit->property->property_name }}, Unit {{ $paying->lease->unit->unit_name }}
                    · {{ $monthLabel }}
                </p>
                <p class="mt-3 rounded-lg bg-gray-50 px-3 py-2 text-sm text-gray-700 dark:bg-white/5 dark:text-gray-300">
                    Owed ৳{{ number_format($paying->balance(), 2) }}
                    @if ($paying->paid > 0)
                        <span class="text-gray-400">(৳{{ number_format($paying->paid, 2) }} of ৳{{ number_format($paying->expected, 2) }} already paid)</span>
                    @endif
                </p>

                <div class="mt-5 space-y-4">
                    <div>
                        <label for="payAmount" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Amount received (৳)</label>
                        <input type="number" step="0.01" id="payAmount" x-ref="amount" wire:model.live.debounce.300ms="payAmount" class="{{ $field }}">
                        @error('payAmount') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
                        @if ($leftAfter > 0 && (float) $payAmount > 0)
                            <p class="mt-1.5 text-xs text-yellow-700 dark:text-yellow-400">Part payment: ৳{{ number_format($leftAfter, 2) }} will still be owed.</p>
                        @endif
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="payMethod" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Paid by</label>
                            <select id="payMethod" wire:model="payMethod" class="{{ $field }}">
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank transfer</option>
                                <option value="mobile_payment">bKash / Nagad</option>
                                <option value="cheque">Cheque</option>
                            </select>
                            @error('payMethod') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="payDate" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Date</label>
                            <input type="date" id="payDate" wire:model="payDate" max="{{ now()->toDateString() }}" class="{{ $field }}">
                            @error('payDate') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" wire:click="closePayment"
                        class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">
                        Cancel
                    </button>
                    <button type="submit" wire:loading.attr="disabled" wire:target="confirmPayment"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50">
                        Record payment
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
