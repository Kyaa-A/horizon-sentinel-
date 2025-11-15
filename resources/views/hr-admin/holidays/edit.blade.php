<x-app-layout>
    <div class="py-6">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <h2 class="mb-6 text-2xl font-bold text-gray-800">
                Edit Holiday: {{ $holiday->name }}
            </h2>
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('hr-admin.holidays.update', $holiday) }}">
                        @csrf
                        @method('PUT')

                        {{-- Holiday Name --}}
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Holiday Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $holiday->name) }}" required
                                   placeholder="e.g., Christmas Day, New Year's Day"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-navy-500 focus:ring-navy-500 sm:text-sm @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Date --}}
                        <div class="mb-4">
                            <label for="date" class="block text-sm font-medium text-gray-700">
                                Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="date" id="date" value="{{ old('date', $holiday->date->format('Y-m-d')) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-navy-500 focus:ring-navy-500 sm:text-sm @error('date') border-red-500 @enderror">
                            @error('date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Recurring --}}
                        <div class="mb-6">
                            <div class="flex items-center">
                                <input type="checkbox" name="is_recurring" id="is_recurring" value="1" {{ old('is_recurring', $holiday->is_recurring) ? 'checked' : '' }}
                                       class="h-4 w-4 rounded border-gray-300 text-navy-600 focus:ring-navy-500">
                                <label for="is_recurring" class="ml-2 block text-sm text-gray-700">
                                    This is a recurring holiday (appears every year)
                                </label>
                            </div>
                            <p class="ml-6 mt-1 text-xs text-gray-500">
                                Check this for holidays like Christmas, New Year's Day, Independence Day, etc.
                            </p>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('hr-admin.holidays') }}" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit" class="rounded-md bg-navy-600 px-4 py-2 text-sm font-medium text-white hover:bg-navy-700">
                                Update Holiday
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
