<div class="max-w-4xl p-2 mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <nav class="flex mb-2" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 text-sm text-gray-500">
                <li><a href="{{ route('tenant.index') }}" class="hover:text-gray-700">Tenants</a></li>
                <li class="flex items-center">
                    <svg class="w-4 h-4 mx-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-gray-700">{{ $isEdit ? 'Edit Tenant' : 'Add Tenant' }}</span>
                </li>
            </ol>
        </nav>
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">{{ $this->title() }}</h1>
    </div>

    <!-- Form -->
    <form wire:submit="save" class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
        <!-- Personal Information Section -->
        <div class="mb-8">
            <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">Personal Information</h3>
            
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <!-- First Name -->
                <div>
                    <label for="first_name" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           wire:model="first_name" 
                           id="first_name"
                           placeholder="Enter first name"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                    @error('first_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Last Name -->
                <div>
                    <label for="last_name" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           wire:model="last_name" 
                           id="last_name"
                           placeholder="Enter last name"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                    @error('last_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           wire:model="email" 
                           id="email"
                           placeholder="tenant@example.com"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                    @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Phone Number <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" 
                           wire:model="phone" 
                           id="phone"
                           placeholder="+1 (555) 123-4567"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                    @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Occupation -->
                <div>
                    <label for="occupation" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Occupation
                    </label>
                    <input type="text" 
                           wire:model="occupation" 
                           id="occupation"
                           placeholder="e.g., Software Engineer, Teacher"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                    @error('occupation') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- National ID -->
                <div>
                    <label for="national_id" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        National ID / SSN
                    </label>
                    <input type="text" 
                           wire:model="national_id" 
                           id="national_id"
                           placeholder="Enter ID number"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                    @error('national_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Contact Information Section -->
        <div class="mb-8">
            <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">Contact Information</h3>
            
            <div class="grid grid-cols-1 gap-6">
                <!-- Address -->
                <div>
                    <label for="address" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Current Address
                    </label>
                    <textarea wire:model="address" 
                              id="address"
                              rows="3"
                              placeholder="Enter current residential address"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                    @error('address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Emergency Contact -->
                <div>
                    <label for="emergency_contact" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Emergency Contact
                    </label>
                    <input type="text" 
                           wire:model="emergency_contact" 
                           id="emergency_contact"
                           placeholder="Name and phone number of emergency contact"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                    @error('emergency_contact') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Family Information Section -->
        <div class="mb-8">
            <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">Family Information</h3>
            
            <div>
                <label for="family_members" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                    Family Members
                </label>
                <textarea wire:model="family_members" 
                          id="family_members"
                          rows="4"
                          placeholder="List family members who will be living in the property (names, ages, relationship)"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                @error('family_members') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Optional: Include spouse, children, or other family members
                </p>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-3 mt-8">
            <button type="button" 
                    wire:click="cancel"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:bg-gray-600 dark:text-gray-300 dark:border-gray-500 dark:hover:bg-gray-700">
                Cancel
            </button>
            <button type="submit" 
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    wire:loading.attr="disabled">
                <span wire:loading.remove>{{ $isEdit ? 'Update Tenant' : 'Create Tenant' }}</span>
                <span wire:loading>{{ $isEdit ? 'Updating...' : 'Creating...' }}</span>
            </button>
        </div>
    </form>
</div>