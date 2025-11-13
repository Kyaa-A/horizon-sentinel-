<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Welcome Message -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-2">Welcome back, {{ Auth::user()->name }}! 👋</h3>
                    <p class="text-gray-600 dark:text-gray-400">Here's an overview of your leave requests.</p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Total Requests -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Requests</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $totalRequests }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Requests -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $pendingRequests }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Approved Requests -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Approved</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $approvedRequests }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Denied Requests -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Denied</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $deniedRequests }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Quick Actions</h3>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('leave-requests.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Submit Leave Request
                        </a>
                        <a href="{{ route('leave-requests.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            View All Requests
                        </a>
                    </div>
                </div>
            </div>

            <!-- Two Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Upcoming Approved Leaves -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Upcoming Time Off</h3>

                        @if($upcomingLeaves->count() > 0)
                            <div class="space-y-3">
                                @foreach($upcomingLeaves as $leave)
                                    <div class="border-l-4 border-green-500 bg-gray-50 dark:bg-gray-700 p-4 rounded">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ ucfirst(str_replace('_', ' ', $leave->leave_type)) }}
                                                </p>
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                    {{ $leave->start_date->format('M d, Y') }} - {{ $leave->end_date->format('M d, Y') }}
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                                    ({{ $leave->start_date->diffInDays($leave->end_date) + 1 }} days)
                                                </p>
                                            </div>
                                            <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-200 rounded-full">
                                                Approved
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="mt-2">No upcoming time off scheduled</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recent Requests -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Recent Requests</h3>

                        @if($recentRequests->count() > 0)
                            <div class="space-y-3">
                                @foreach($recentRequests as $request)
                                    <div class="border-l-4 @if($request->status === 'pending') border-yellow-500 @elseif($request->status === 'approved') border-green-500 @else border-red-500 @endif bg-gray-50 dark:bg-gray-700 p-4 rounded">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <p class="font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ ucfirst(str_replace('_', ' ', $request->leave_type)) }}
                                                </p>
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                    {{ $request->start_date->format('M d') }} - {{ $request->end_date->format('M d, Y') }}
                                                </p>
                                                @if($request->manager)
                                                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                                        Manager: {{ $request->manager->name }}
                                                    </p>
                                                @endif
                                            </div>
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                @if($request->status === 'pending') text-yellow-800 bg-yellow-200
                                                @elseif($request->status === 'approved') text-green-800 bg-green-200
                                                @else text-red-800 bg-red-200 @endif">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('leave-requests.index') }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    View all requests →
                                </a>
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="mt-2">No leave requests yet</p>
                                <a href="{{ route('leave-requests.create') }}" class="mt-4 inline-flex items-center text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                    Submit your first request →
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
