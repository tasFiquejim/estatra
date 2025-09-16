<div class="p-2 mx-auto max-w-7xl">
    <!-- Property Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-sm text-gray-500">
                    <li><a href="{{ route('property.index') }}" class="hover:text-gray-700">Properties</a></li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 mx-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">{{ $property->property_name }}</span>
                    </li>
                </ol>
            </nav>
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">{{ $property->property_name }}</h1>
            <p class="text-gray-600 dark:text-gray-400">{{ $property->address }}</p>
        </div>

        {{-- <div class="flex gap-2">
            <a href="{{ route('property.edit', $property) }}"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                Edit Property
            </a>
        </div> --}}
    </div>

    <!-- Property Details Card -->
    <div class="p-6 mb-6 bg-white rounded-lg shadow dark:bg-gray-800">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Property Type</h3>
                <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ ucfirst($property->property_type) }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</h3>
                <p class="mt-1">
                    <span
                        class="px-2 py-1 text-xs font-semibold rounded-full 
                        {{ $property->status === 'active'
                            ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'
                            : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' }}">
                        {{ ucfirst($property->status ?? 'active') }}
                    </span>
                </p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Units</h3>
                <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ $property->units->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Units Section -->
    <div class="bg-white rounded-lg shadow dark:bg-gray-800">
        {{-- <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Units</h2>
                <a href="{{ route('unit.create', $property) }}" 
                   class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    Add Unit
                </a>
            </div>
        </div> --}}

        <div class="p-6">
            @livewire('v1.unit.unit-list', ['property' => $property])
        </div>
    </div>
</div>
