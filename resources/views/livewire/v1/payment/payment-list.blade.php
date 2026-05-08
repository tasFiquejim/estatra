@section('title', 'Payment Records')

<div class="space-y-5 sm:space-y-6">
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

        <!-- Header Section -->
        <div class="px-5 py-4 sm:px-6 sm:py-5">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-base font-medium text-gray-800 dark:text-white/90">Payment Records</h3>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <!-- Search -->
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.500ms="search" placeholder="Search payments..."
                            class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-64 rounded-lg border border-gray-300 bg-transparent py-2.5 pl-10 pr-4 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                        <svg class="absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-500 dark:text-gray-400"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 105.29 5.29a7.5 7.5 0 0011.36 11.36z" />
                        </svg>
                    </div>

                    <!-- Record Payment Button -->
                    <a href="{{ route('payment.create') }}"
                        class="shadow-theme-xs inline-flex items-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700 focus:outline-hidden focus:ring-2 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Record Payment
                    </a>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="p-5 border-t border-gray-100 dark:border-gray-800 sm:p-6">
            <div class="overflow-x-auto">
                <div class="min-w-full">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                    Property & Tenant
                                </th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase cursor-pointer hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                                    wire:click="sortByField('rent_period')">
                                    <div class="flex items-center gap-1">
                                        Rent Period
                                        @if ($sortBy === 'rent_period')
                                            <svg class="h-4 w-4 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 15l7-7 7 7" />
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 opacity-30" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                            </svg>
                                        @endif
                                    </div>
                                </th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase cursor-pointer hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                                    wire:click="sortByField('amount_paid')">
                                    <div class="flex items-center gap-1">
                                        Amount
                                        @if ($sortBy === 'amount_paid')
                                            <svg class="h-4 w-4 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 15l7-7 7 7" />
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 opacity-30" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                            </svg>
                                        @endif
                                    </div>
                                </th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase cursor-pointer hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                                    wire:click="sortByField('payment_date')">
                                    <div class="flex items-center gap-1">
                                        Payment Date
                                        @if ($sortBy === 'payment_date')
                                            <svg class="h-4 w-4 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 15l7-7 7 7" />
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 opacity-30" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                            </svg>
                                        @endif
                                    </div>
                                </th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                    Method & Status
                                </th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-800 dark:bg-white/[0.03]">
                            @forelse ($this->payments as $payment)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="flex items-center justify-center w-10 h-10 bg-gray-100 rounded-lg dark:bg-gray-800">
                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v2a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $payment->lease->unit->property->property_name }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    Unit {{ $payment->lease->unit->unit_name }} -
                                                    {{ $payment->lease->tenant->first_name }}
                                                    {{ $payment->lease->tenant->last_name }}
                                                </div>
                                                @if ($payment->receipt_number)
                                                    <div class="text-xs text-gray-400">
                                                        Receipt: {{ $payment->receipt_number }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-400">
                                        <div class="font-medium text-gray-900 dark:text-white">
                                            {{ $payment->rent_period->format('F Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $payment->rent_period->format('M y') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-400">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            ৳{{ number_format($payment->amount_paid, 2) }}
                                        </div>
                                        @php
                                            $totalDue =
                                                $payment->lease->rent_amount + ($payment->lease->service_charge ?? 0);
                                            $balance = $totalDue - $payment->amount_paid;
                                        @endphp
                                        @if ($balance > 0)
                                            <div class="text-xs text-red-600 dark:text-red-400">
                                                Due: ৳{{ number_format($balance, 2) }}
                                            </div>
                                        @else
                                            <div class="text-xs text-green-600 dark:text-green-400">
                                                Paid in full
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-400">
                                        <div class="font-medium text-gray-900 dark:text-white">
                                            {{ $payment->payment_date->format('M j, Y') }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ $payment->payment_date->format('D') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-400">
                                        <div class="space-y-2">
                                            <span
                                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium 
                                                {{ $payment->payment_method === \App\Enums\PaymentMethod::Cash
                                                    ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'
                                                    : ($payment->payment_method === \App\Enums\PaymentMethod::BankTransfer
                                                        ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'
                                                        : ($payment->payment_method === \App\Enums\PaymentMethod::MobilePayment
                                                            ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300'
                                                            : 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300')) }}">
                                                {{ str_replace('_', ' ', ucfirst($payment->payment_method->value)) }}
                                            </span>
                                            <div>
                                                <span
                                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                                    {{ $payment->status === \App\Enums\PaymentStatus::Paid
                                                        ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'
                                                        : ($payment->status === \App\Enums\PaymentStatus::Partial
                                                            ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'
                                                            : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300') }}">
                                                    @if ($payment->status === \App\Enums\PaymentStatus::Paid)
                                                        <svg class="w-2 h-2 mr-1 fill-green-500" viewBox="0 0 6 6">
                                                            <circle cx="3" cy="3" r="3" />
                                                        </svg>
                                                        Paid
                                                    @elseif($payment->status === \App\Enums\PaymentStatus::Partial)
                                                        <svg class="w-2 h-2 mr-1 fill-yellow-500" viewBox="0 0 6 6">
                                                            <circle cx="3" cy="3" r="3" />
                                                        </svg>
                                                        Partial
                                                    @else
                                                        <svg class="w-2 h-2 mr-1 fill-red-500" viewBox="0 0 6 6">
                                                            <circle cx="3" cy="3" r="3" />
                                                        </svg>
                                                        Unpaid
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td
                                        class="relative py-4 pl-3 pr-4 text-sm font-medium text-right whitespace-nowrap sm:pr-6">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('payment.edit', $payment) }}"
                                                class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-hidden focus:ring-2 focus:ring-gray-200 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-800 dark:focus:ring-gray-600">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Edit
                                            </a>
                                            @if ($payment->receipt_number)
                                                <a href="{{ route('payment.receipt', $payment) }}" target="_blank"
                                                    class="inline-flex items-center rounded-lg border border-blue-300 bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-700 hover:bg-blue-100 focus:outline-hidden focus:ring-2 focus:ring-blue-200 dark:border-blue-800 dark:bg-blue-500/10 dark:text-blue-400 dark:hover:bg-blue-500/20">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    Receipt
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v2a2 2 0 002 2z" />
                                            </svg>
                                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No payment records
                                                found.</p>
                                            <p class="mt-1 text-xs text-gray-400">Record your first payment to get
                                                started.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if ($this->payments->hasPages())
                <div class="px-4 py-3 mt-4 border-t border-gray-200 dark:border-gray-800">
                    {{ $this->payments->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
