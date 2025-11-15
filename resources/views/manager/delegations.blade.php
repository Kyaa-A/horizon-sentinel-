<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Success Message -->
            @if (session('success'))
                <div class="p-4 bg-green-50 dark:bg-green-900/30 border-l-4 border-green-500 text-green-800 dark:text-green-200 rounded-r-lg shadow-sm">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="font-semibold">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Create Delegation Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                        <svg class="h-6 w-6 mr-2 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create New Delegation
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                        Assign another manager to approve leave requests on your behalf during a specific period.
                    </p>

                    <form method="POST" action="{{ route('manager.delegations.store') }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Delegate Manager -->
                            <div>
                                <label for="delegate_manager_id" class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">
                                    Delegate Manager <span class="text-red-500">*</span>
                                </label>
                                <select name="delegate_manager_id" id="delegate_manager_id" required
                                        class="w-full px-4 py-2.5 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-gray-900 focus:border-primary-500 dark:focus:border-primary-500 focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-500 focus:ring-opacity-20 transition-all duration-200">
                                    <option value="">Select a manager...</option>
                                    @foreach ($availableDelegates as $delegate)
                                        <option value="{{ $delegate->id }}" {{ old('delegate_manager_id') == $delegate->id ? 'selected' : '' }}>
                                            {{ $delegate->name }} ({{ $delegate->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('delegate_manager_id')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Start Date -->
                            <div>
                                <label for="start_date" class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">
                                    Start Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" required
                                       min="{{ now()->format('Y-m-d') }}"
                                       class="w-full px-4 py-2.5 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-gray-900 focus:border-primary-500 dark:focus:border-primary-500 focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-500 focus:ring-opacity-20 transition-all duration-200">
                                @error('start_date')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- End Date -->
                            <div>
                                <label for="end_date" class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">
                                    End Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" required
                                       min="{{ now()->format('Y-m-d') }}"
                                       class="w-full px-4 py-2.5 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-gray-900 focus:border-primary-500 dark:focus:border-primary-500 focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-500 focus:ring-opacity-20 transition-all duration-200">
                                @error('end_date')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-gradient-horizon border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-wide hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 active:scale-[0.98] transition-all duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Create Delegation
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Delegations List -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                        <svg class="h-6 w-6 mr-2 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Your Delegations
                    </h3>

                    @if ($delegations->isEmpty())
                        <div class="text-center py-12">
                            <div class="mx-auto h-16 w-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
                                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">No delegations yet</h3>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Create your first delegation to assign backup approvers.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach ($delegations as $delegation)
                                @php
                                    $isActive = $delegation->isCurrentlyActive();
                                    $isPast = $delegation->end_date->isPast();
                                    $isFuture = $delegation->start_date->isFuture();
                                @endphp

                                <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5 hover:bg-gray-50 dark:hover:bg-gray-750 transition-all duration-200">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <!-- Delegate Info -->
                                            <div class="flex items-center mb-3">
                                                <div class="h-12 w-12 rounded-full bg-gradient-horizon flex items-center justify-center text-white font-bold text-lg">
                                                    {{ substr($delegation->delegate->name, 0, 1) }}
                                                </div>
                                                <div class="ml-4">
                                                    <h4 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                                        {{ $delegation->delegate->name }}
                                                    </h4>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                                        {{ $delegation->delegate->email }}
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Date Range -->
                                            <div class="ml-16 space-y-2">
                                                <div class="flex items-center text-sm text-gray-900 dark:text-gray-100">
                                                    <svg class="h-5 w-5 text-primary-600 dark:text-primary-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    <span class="font-semibold">{{ $delegation->date_range }}</span>
                                                </div>

                                                <!-- Status Badge -->
                                                <div class="flex items-center">
                                                    @if ($isActive)
                                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                                            Active Now
                                                        </span>
                                                    @elseif ($isFuture)
                                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                                                            Upcoming
                                                        </span>
                                                    @elseif ($isPast)
                                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                            Past
                                                        </span>
                                                    @endif

                                                    @if (!$delegation->is_active)
                                                        <span class="ml-2 px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                                                            Deactivated
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Actions -->
                                        @if ($delegation->is_active && !$isPast)
                                            <div class="ml-4 flex gap-2">
                                                <form method="POST" action="{{ route('manager.delegations.deactivate', $delegation) }}" onsubmit="return confirm('Are you sure you want to deactivate this delegation?');">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-2 text-sm font-medium text-orange-700 dark:text-orange-300 hover:bg-orange-50 dark:hover:bg-orange-900/20 rounded-lg transition-colors">
                                                        Deactivate
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('manager.delegations.destroy', $delegation) }}" onsubmit="return confirm('Are you sure you want to delete this delegation?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-3 py-2 text-sm font-medium text-red-700 dark:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        @elseif (!$delegation->is_active || $isPast)
                                            <form method="POST" action="{{ route('manager.delegations.destroy', $delegation) }}" onsubmit="return confirm('Are you sure you want to delete this delegation?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-2 text-sm font-medium text-red-700 dark:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if ($delegations->hasPages())
                            <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-4">
                                {{ $delegations->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
