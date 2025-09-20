@section('title', $property->property_name)

<div class="space-y-5 sm:space-y-6">
    
    <!-- Header Section -->
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="px-5 py-4 sm:px-6 sm:py-5">
            <div class="flex flex-col gap-4">
                <!-- Breadcrumb Navigation -->
                <nav class="flex" aria-label="Breadcrumb">
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
                            <span class="font-medium text-gray-800 dark:text-white/90">{{ $property->property_name }}</span>
                        </li>
                    </ol>
                </nav>

                <!-- Property Header Info -->
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div class="flex items-start gap-4">
                        <!-- Property Image -->
                        @if ($property->property_photo)
                            <img class="object-cover w-16 h-16 rounded-xl shadow-sm"
                                src="{{ asset('storage/' . $property->property_photo) }}"
                                alt="{{ $property->property_name }}">
                        @else
                            <div class="flex items-center justify-center w-16 h-16 bg-gray-100 rounded-xl dark:bg-gray-800">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                        @endif

                        <!-- Property Details -->
                        <div>
                            <h1 class="text-xl sm:text-2xl font-semibold text-gray-800 dark:text-white/90">
                                {{ $property->property_name }}
                            </h1>
                            <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                <div class="font-medium">{{ $property->address }}</div>
                                @if ($property->city)
                                    <div class="text-xs mt-0.5">
                                        {{ $property->city }}@if ($property->state), {{ $property->state }}@endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                        <a href="{{ route('property.edit', $property) }}"
                            class="shadow-theme-xs inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-hidden focus:ring-2 focus:ring-gray-200 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-800 dark:focus:ring-gray-600">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit Property
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Property Details Grid -->
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="px-5 py-4 sm:px-6 sm:py-5">
            <h3 class="text-base font-medium text-gray-800 dark:text-white/90 mb-4">Property Information</h3>
            
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Property Type -->
                <div class="space-y-1">
                    <h4 class="text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400">
                        Property Type
                    </h4>
                    <div class="flex items-center">
                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium 
                            {{ $property->property_type === 'apartment'
                                ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'
                                : ($property->property_type === 'house'
                                    ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'
                                    : 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300') }}">
                            {{ ucfirst($property->property_type) }}
                        </span>
                    </div>
                </div>

                <!-- Status -->
                <div class="space-y-1">
                    <h4 class="text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400">
                        Status
                    </h4>
                    <div class="flex items-center">
                        @if ($property->status === 'active')
                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                <svg class="mr-1.5 h-2 w-2 fill-green-500" viewBox="0 0 6 6">
                                    <circle cx="3" cy="3" r="3" />
                                </svg>
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900/30 dark:text-red-300">
                                <svg class="mr-1.5 h-2 w-2 fill-red-500" viewBox="0 0 6 6">
                                    <circle cx="3" cy="3" r="3" />
                                </svg>
                                Inactive
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Total Units -->
                <div class="space-y-1">
                    <h4 class="text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400">
                        Total Units
                    </h4>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white/90">
                        {{ $property->units->count() }}
                    </p>
                </div>

                <!-- Created Date -->
                <div class="space-y-1">
                    <h4 class="text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400">
                        Created
                    </h4>
                    <div>
                        <div class="text-sm font-medium text-gray-900 dark:text-white/90">
                            {{ $property->created_at->format('M j, Y') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="p-5 sm:p-6">
            @livewire('v1.unit.unit-list', ['property' => $property])
        </div>
    </div>
</div>