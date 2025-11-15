<x-app-layout>
    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            {{-- Statistics Cards --}}
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                {{-- Total Users --}}
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Users</p>
                                <p class="text-3xl font-bold text-gray-900">{{ $totalUsers }}</p>
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ $totalEmployees }} employees, {{ $totalManagers }} managers
                                </p>
                            </div>
                            <div class="rounded-full bg-navy-100 p-3">
                                <svg class="h-8 w-8 text-navy-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pending Requests --}}
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Pending Requests</p>
                                <p class="text-3xl font-bold text-yellow-600">{{ $pendingRequests }}</p>
                                <p class="mt-1 text-xs text-gray-500">Awaiting manager review</p>
                            </div>
                            <div class="rounded-full bg-yellow-100 p-3">
                                <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Currently on Leave --}}
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Currently on Leave</p>
                                <p class="text-3xl font-bold text-blue-600">{{ $currentlyOnLeave }}</p>
                                <p class="mt-1 text-xs text-gray-500">Employees out today</p>
                            </div>
                            <div class="rounded-full bg-blue-100 p-3">
                                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Approved This Month --}}
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Approved This Month</p>
                                <p class="text-3xl font-bold text-green-600">{{ $approvedThisMonth }}</p>
                                <p class="mt-1 text-xs text-gray-500">{{ now()->format('F Y') }}</p>
                            </div>
                            <div class="rounded-full bg-green-100 p-3">
                                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Leave Type Breakdown and Balance Summary --}}
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                {{-- Leave Type Breakdown --}}
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h3 class="text-lg font-medium text-gray-900">Leave Type Breakdown ({{ now()->year }})</h3>
                    </div>
                    <div class="p-6">
                        @if($leaveTypeBreakdown->count() > 0)
                            <div class="space-y-4">
                                @foreach($leaveTypeBreakdown as $breakdown)
                                    <div>
                                        <div class="mb-1 flex items-center justify-between">
                                            <span class="text-sm font-medium text-gray-700">
                                                {{ str_replace('_', ' ', ucwords($breakdown->leave_type, '_')) }}
                                            </span>
                                            <span class="text-sm text-gray-600">
                                                {{ $breakdown->count }} requests ({{ $breakdown->total_days }} days)
                                            </span>
                                        </div>
                                        <div class="h-2 w-full rounded-full bg-gray-200">
                                            <div class="h-2 rounded-full bg-navy-600" style="width: {{ ($breakdown->total_days / $leaveTypeBreakdown->sum('total_days')) * 100 }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500">No leave requests approved this year yet.</p>
                        @endif
                    </div>
                </div>

                {{-- Balance Summary --}}
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h3 class="text-lg font-medium text-gray-900">Company-wide Balance Summary</h3>
                    </div>
                    <div class="p-6">
                        @if($balanceSummary->count() > 0)
                            <div class="space-y-4">
                                @foreach($balanceSummary as $summary)
                                    <div class="rounded-lg border border-gray-200 p-4">
                                        <div class="mb-2 text-sm font-medium text-gray-900">
                                            {{ str_replace('_', ' ', ucwords($summary->leave_type, '_')) }}
                                        </div>
                                        <div class="grid grid-cols-3 gap-2 text-xs">
                                            <div>
                                                <span class="text-gray-500">Available:</span>
                                                <span class="ml-1 font-medium text-green-600">{{ number_format($summary->total_available, 1) }}</span>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Used:</span>
                                                <span class="ml-1 font-medium text-blue-600">{{ number_format($summary->total_used, 1) }}</span>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Pending:</span>
                                                <span class="ml-1 font-medium text-yellow-600">{{ number_format($summary->total_pending, 1) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500">No balance data available.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Upcoming Holidays --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200 bg-white px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Upcoming Holidays</h3>
                        <a href="{{ route('hr-admin.holidays') }}" class="text-sm font-medium text-navy-600 hover:text-navy-800">
                            View All →
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    @if($upcomingHolidays->count() > 0)
                        <div class="space-y-3">
                            @foreach($upcomingHolidays as $holiday)
                                <div class="flex items-center justify-between rounded-lg border border-gray-200 p-3">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-12 w-12 flex-col items-center justify-center rounded-lg bg-navy-100 text-navy-600">
                                            <span class="text-xs font-medium">{{ $holiday->date->format('M') }}</span>
                                            <span class="text-lg font-bold">{{ $holiday->date->format('d') }}</span>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $holiday->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $holiday->date->format('l, F j, Y') }}</div>
                                        </div>
                                    </div>
                                    @if($holiday->is_recurring)
                                        <span class="rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-800">Recurring</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No upcoming holidays.</p>
                    @endif
                </div>
            </div>

            {{-- Recent Leave Requests --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200 bg-white px-6 py-4">
                    <h3 class="text-lg font-medium text-gray-900">Recent Leave Requests</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Employee
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Type
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Dates
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Days
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Submitted
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach($recentRequests as $request)
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $request->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $request->user->email }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        {{ str_replace('_', ' ', ucwords($request->leave_type, '_')) }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        {{ $request->start_date->format('M d, Y') }} - {{ $request->end_date->format('M d, Y') }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        {{ $request->total_days }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        @if($request->status === 'pending')
                                            <span class="inline-flex rounded-full bg-yellow-100 px-2 py-1 text-xs font-semibold leading-5 text-yellow-800">
                                                Pending
                                            </span>
                                        @elseif($request->status === 'approved')
                                            <span class="inline-flex rounded-full bg-green-100 px-2 py-1 text-xs font-semibold leading-5 text-green-800">
                                                Approved
                                            </span>
                                        @elseif($request->status === 'denied')
                                            <span class="inline-flex rounded-full bg-red-100 px-2 py-1 text-xs font-semibold leading-5 text-red-800">
                                                Denied
                                            </span>
                                        @else
                                            <span class="inline-flex rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold leading-5 text-gray-800">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ $request->submitted_at->diffForHumans() }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                <a href="{{ route('hr-admin.users') }}" class="block rounded-lg border-2 border-dashed border-gray-300 p-6 text-center hover:border-navy-500">
                    <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span class="mt-2 block text-sm font-medium text-gray-900">Manage Users</span>
                </a>

                <a href="{{ route('hr-admin.balances') }}" class="block rounded-lg border-2 border-dashed border-gray-300 p-6 text-center hover:border-navy-500">
                    <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <span class="mt-2 block text-sm font-medium text-gray-900">Manage Balances</span>
                </a>

                <a href="{{ route('hr-admin.holidays') }}" class="block rounded-lg border-2 border-dashed border-gray-300 p-6 text-center hover:border-navy-500">
                    <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="mt-2 block text-sm font-medium text-gray-900">Manage Holidays</span>
                </a>

                <a href="{{ route('hr-admin.reports') }}" class="block rounded-lg border-2 border-dashed border-gray-300 p-6 text-center hover:border-navy-500">
                    <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span class="mt-2 block text-sm font-medium text-gray-900">View Reports</span>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
