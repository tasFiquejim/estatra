<x-app-layout>
    @section('title', 'Profile')

    <div class="space-y-5 sm:space-y-6">
        <!-- Page Header -->
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 sm:px-6 sm:py-5">
                <h3 class="text-base font-medium text-gray-800 dark:text-white/90">Profile Settings</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage your account settings and preferences</p>
            </div>
        </div>

        <!-- Profile Information and Password Update Side by Side -->
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2 sm:gap-6">
            <!-- Update Profile Information -->
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="p-5 sm:p-6">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
            </div>

            <!-- Update Password -->
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="p-5 sm:p-6">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Account -->
        {{-- <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 border-b border-gray-100 sm:px-6 sm:py-5 dark:border-gray-800">
                <h4 class="text-base font-medium text-gray-800 dark:text-white/90">Delete Account</h4>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Permanently delete your account and all of its resources</p>
            </div>
            <div class="p-5 sm:p-6">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div> --}}
    </div>
</x-app-layout>