<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Dashboard</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Welcome back, {{ auth()->user()->first_name }}. Here's your property overview for {{ now()->format('F Y') }}.</p>
        </div>
    </div>

    {{-- ===== Row 1: Property & Tenant Stats ===== --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

        {{-- Total Properties --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-500/10">
                    <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-bold text-gray-800 dark:text-white">{{ $this->totalProperties }}</span>
                <p class="mt-1 text-sm font-medium text-gray-500 dark:text-gray-400">Total Properties</p>
            </div>
        </div>

        {{-- Total Units --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-purple-50 dark:bg-purple-500/10">
                    <svg class="h-5 w-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-bold text-gray-800 dark:text-white">{{ $this->totalUnits }}</span>
                <p class="mt-1 text-sm font-medium text-gray-500 dark:text-gray-400">Total Units</p>
            </div>
        </div>

        {{-- Active Tenants --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-green-50 dark:bg-green-500/10">
                    <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-bold text-gray-800 dark:text-white">{{ $this->activeTenantsCount }}</span>
                <p class="mt-1 text-sm font-medium text-gray-500 dark:text-gray-400">Active Tenants</p>
            </div>
        </div>

        {{-- Occupancy Rate --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-50 dark:bg-orange-500/10">
                    <svg class="h-5 w-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-end gap-1">
                    <span class="text-3xl font-bold text-gray-800 dark:text-white">{{ $this->occupancyRate }}</span>
                    <span class="mb-1 text-lg font-semibold text-gray-500 dark:text-gray-400">%</span>
                </div>
                <p class="mt-1 text-sm font-medium text-gray-500 dark:text-gray-400">Occupancy Rate</p>
                {{-- Progress bar --}}
                <div class="mt-3 h-1.5 w-full rounded-full bg-gray-100 dark:bg-gray-800">
                    <div class="h-1.5 rounded-full bg-orange-500 transition-all duration-500"
                        style="width: {{ $this->occupancyRate }}%"></div>
                </div>
                <p class="mt-1.5 text-xs text-gray-400 dark:text-gray-500">{{ $this->occupiedUnits }} of {{ $this->totalUnits }} units occupied</p>
            </div>
        </div>
    </div>

    {{-- ===== Row 2: Financial Summary (Current Month) ===== --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

        {{-- Monthly Income --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-50 dark:bg-green-500/10">
                    <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Monthly Income</p>
            </div>
            <p class="mt-3 text-2xl font-bold text-gray-800 dark:text-white">৳{{ number_format($this->monthlyIncome, 2) }}</p>
            <p class="mt-1 text-xs text-gray-400">Payments received in {{ now()->format('F') }}</p>
        </div>

        {{-- Monthly Expenses --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-50 dark:bg-red-500/10">
                    <svg class="h-5 w-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Monthly Expenses</p>
            </div>
            <p class="mt-3 text-2xl font-bold text-gray-800 dark:text-white">৳{{ number_format($this->monthlyExpenses, 2) }}</p>
            <p class="mt-1 text-xs text-gray-400">Property costs in {{ now()->format('F') }}</p>
        </div>

        {{-- Net Profit --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg {{ $this->netProfit >= 0 ? 'bg-blue-50 dark:bg-blue-500/10' : 'bg-red-50 dark:bg-red-500/10' }}">
                    <svg class="h-5 w-5 {{ $this->netProfit >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-red-600 dark:text-red-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Net Profit</p>
            </div>
            <p class="mt-3 text-2xl font-bold {{ $this->netProfit >= 0 ? 'text-gray-800 dark:text-white' : 'text-red-600 dark:text-red-400' }}">
                ৳{{ number_format(abs($this->netProfit), 2) }}
                @if($this->netProfit < 0)
                    <span class="text-sm font-normal text-red-500">(loss)</span>
                @endif
            </p>
            <p class="mt-1 text-xs text-gray-400">Income minus expenses</p>
        </div>
    </div>

    {{-- ===== Row 3: Expiring Leases + Recent Payments ===== --}}
    <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">

        {{-- Expiring Leases --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4 dark:border-gray-800">
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-white/90">Leases Expiring Soon</h3>
                    @if($this->expiringSoonLeases->count() > 0)
                        <span class="inline-flex items-center rounded-full bg-orange-100 px-2 py-0.5 text-xs font-medium text-orange-700 dark:bg-orange-500/10 dark:text-orange-400">
                            {{ $this->expiringSoonLeases->count() }}
                        </span>
                    @endif
                </div>
                <a href="{{ route('lease.index') }}" class="text-xs text-blue-600 hover:underline dark:text-blue-400">View all</a>
            </div>
            <div class="p-5">
                @forelse($this->expiringSoonLeases as $lease)
                    @php
                        $daysLeft = now()->diffInDays($lease->end_date, false);
                        $isUrgent = $daysLeft <= 7;
                    @endphp
                    <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-800' : '' }}">
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-white">
                                {{ $lease->tenant->first_name }} {{ $lease->tenant->last_name }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $lease->unit->property->property_name }} — Unit {{ $lease->unit->unit_name }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $isUrgent ? 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400' : 'bg-orange-100 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400' }}">
                                {{ $daysLeft }}d left
                            </span>
                            <p class="mt-1 text-xs text-gray-400">{{ $lease->end_date->format('M j, Y') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center py-8 text-center">
                        <svg class="h-10 w-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No leases expiring in the next 30 days</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Recent Payments --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4 dark:border-gray-800">
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v2a2 2 0 002 2z" />
                    </svg>
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-white/90">Recent Payments</h3>
                </div>
                <a href="{{ route('payment.index') }}" class="text-xs text-blue-600 hover:underline dark:text-blue-400">View all</a>
            </div>
            <div class="p-5">
                @forelse($this->recentPayments as $payment)
                    <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-800' : '' }}">
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-white">
                                {{ $payment->lease->tenant->first_name }} {{ $payment->lease->tenant->last_name }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $payment->lease->unit->property->property_name }} · {{ $payment->payment_date->format('M j, Y') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-800 dark:text-white">৳{{ number_format($payment->amount_paid, 2) }}</p>
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                {{ $payment->status === \App\Enums\PaymentStatus::Paid ? 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400' }}">
                                {{ ucfirst($payment->status->value) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center py-8 text-center">
                        <svg class="h-10 w-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v2a2 2 0 002 2z" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No payments recorded yet</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
