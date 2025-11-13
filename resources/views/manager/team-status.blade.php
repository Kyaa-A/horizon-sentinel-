<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Team Status') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Date Selector -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="GET" action="{{ route('manager.team-status') }}" class="flex flex-wrap items-end gap-4">
                        <div class="flex-1 min-w-[200px]">
                            <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Select Date
                            </label>
                            <input type="date"
                                   name="date"
                                   id="date"
                                   value="{{ $selectedDate->format('Y-m-d') }}"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Update
                            </button>
                            <a href="{{ route('manager.team-status') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                Today
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Total Team -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Team</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $totalTeam }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Available -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Available</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $availableCount }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-500">{{ $availabilityPercentage }}%</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- On Leave -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-orange-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">On Leave</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $onLeaveCount }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Team Members List -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Team Members - {{ $selectedDate->format('l, F j, Y') }}
                    </h3>

                    @if(count($teamStatus) > 0)
                        <div class="space-y-3">
                            @foreach($teamStatus as $item)
                                <div class="border-l-4 @if($item['status'] === 'on_leave') border-orange-500 bg-orange-50 dark:bg-orange-900/20 @else border-green-500 bg-green-50 dark:bg-green-900/20 @endif p-4 rounded-r-lg">
                                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                                        <!-- Employee Info -->
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3">
                                                <div class="flex-shrink-0">
                                                    <div class="h-10 w-10 rounded-full @if($item['status'] === 'on_leave') bg-orange-500 @else bg-green-500 @endif flex items-center justify-center">
                                                        <span class="text-white font-semibold text-sm">
                                                            {{ strtoupper(substr($item['member']->name, 0, 2)) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-gray-900 dark:text-gray-100">
                                                        {{ $item['member']->name }}
                                                    </p>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                                        {{ $item['member']->email }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Status -->
                                        <div class="flex flex-col md:items-end gap-2">
                                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full w-fit
                                                @if($item['status'] === 'on_leave')
                                                    text-orange-800 bg-orange-200 dark:bg-orange-800 dark:text-orange-200
                                                @else
                                                    text-green-800 bg-green-200 dark:bg-green-800 dark:text-green-200
                                                @endif">
                                                @if($item['status'] === 'on_leave')
                                                    On Leave
                                                @else
                                                    Available
                                                @endif
                                            </span>

                                            @if($item['current_leave'])
                                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                                    <p class="font-medium">{{ ucfirst(str_replace('_', ' ', $item['current_leave']->leave_type)) }}</p>
                                                    <p>{{ $item['current_leave']->start_date->format('M d') }} - {{ $item['current_leave']->end_date->format('M d, Y') }}</p>
                                                    <p class="text-xs">({{ $item['current_leave']->start_date->diffInDays($item['current_leave']->end_date) + 1 }} days)</p>
                                                </div>
                                            @endif

                                            @if($item['upcoming_leave'] && !$item['current_leave'])
                                                <div class="text-xs text-blue-600 dark:text-blue-400">
                                                    <p>Upcoming: {{ $item['upcoming_leave']->start_date->format('M d') }}</p>
                                                    <p>{{ ucfirst(str_replace('_', ' ', $item['upcoming_leave']->leave_type)) }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <p class="mt-2">No team members found</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Navigation -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Quick Actions</h3>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('manager.team-calendar') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            View Team Calendar
                        </a>
                        <a href="{{ route('manager.pending-requests') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            View Pending Requests
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
