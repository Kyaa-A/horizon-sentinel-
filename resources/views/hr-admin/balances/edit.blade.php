<x-app-layout>
    <div class="py-6">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <h2 class="mb-6 text-2xl font-bold text-gray-800">
                Adjust Leave Balance
            </h2>
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    {{-- Current Balance Info --}}
                    <div class="mb-6 rounded-lg bg-gray-50 p-4">
                        <h3 class="mb-3 text-lg font-medium text-gray-900">Current Balance</h3>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <p class="text-sm text-gray-600">Employee</p>
                                <p class="text-base font-medium text-gray-900">{{ $balance->user->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Leave Type</p>
                                <p class="text-base font-medium text-gray-900">{{ str_replace('_', ' ', ucwords($balance->leave_type, '_')) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Total Allocated</p>
                                <p class="text-base font-medium text-gray-900">{{ number_format($balance->total_allocated_days, 1) }} days</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Available Days</p>
                                <p class="text-base font-medium text-green-600">{{ number_format($balance->available_days, 1) }} days</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Used Days</p>
                                <p class="text-base font-medium text-blue-600">{{ number_format($balance->used_days, 1) }} days</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Pending Days</p>
                                <p class="text-base font-medium text-yellow-600">{{ number_format($balance->pending_days, 1) }} days</p>
                            </div>
                        </div>
                    </div>

                    {{-- Adjustment Form --}}
                    <form method="POST" action="{{ route('hr-admin.balances.update', $balance) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="available_days" class="block text-sm font-medium text-gray-700">
                                New Available Days <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="available_days" id="available_days"
                                   value="{{ old('available_days', $balance->available_days) }}"
                                   step="0.5" min="0" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-navy-500 focus:ring-navy-500 sm:text-sm @error('available_days') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">
                                Current: {{ number_format($balance->available_days, 1) }} days.
                                This will adjust only the available balance.
                            </p>
                            @error('available_days')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="reason" class="block text-sm font-medium text-gray-700">
                                Reason for Adjustment <span class="text-red-500">*</span>
                            </label>
                            <textarea name="reason" id="reason" rows="3" required
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-navy-500 focus:ring-navy-500 sm:text-sm @error('reason') border-red-500 @enderror">{{ old('reason') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">
                                Provide a clear explanation for this manual adjustment (will be recorded in balance history)
                            </p>
                            @error('reason')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Warning Notice --}}
                        <div class="mb-6 rounded-lg border border-yellow-200 bg-yellow-50 p-4">
                            <div class="flex">
                                <div class="shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Important Notice</h3>
                                    <div class="mt-1 text-sm text-yellow-700">
                                        <p>Manual balance adjustments will be recorded in the employee's balance history. Make sure to provide a clear reason for auditing purposes.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('hr-admin.balances') }}" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit" class="rounded-md bg-navy-600 px-4 py-2 text-sm font-medium text-white hover:bg-navy-700">
                                Update Balance
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
