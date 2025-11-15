<x-app-layout>
    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <h2 class="mb-6 text-2xl font-bold text-gray-800">
                Company-wide Reports
            </h2>

            {{-- Year Filter --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="GET" action="{{ route('hr-admin.reports') }}" class="flex items-end gap-4">
                        <div>
                            <label for="year" class="block text-sm font-medium text-gray-700">Report Year</label>
                            <select name="year" id="year" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-navy-500 focus:ring-navy-500 sm:text-sm">
                                @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                                    <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <button type="submit" class="rounded-md bg-navy-600 px-4 py-2 text-sm font-medium text-white hover:bg-navy-700">
                            Generate Report
                        </button>
                    </form>
                </div>
            </div>

            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="mb-2 text-sm font-medium text-gray-600">Total Requests ({{ $selectedYear }})</h3>
                        <p class="text-3xl font-bold text-gray-900">{{ $totalRequests }}</p>
                        <p class="mt-1 text-sm text-gray-500">All leave requests submitted</p>
                    </div>
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="mb-2 text-sm font-medium text-gray-600">Approval Rate</h3>
                        <p class="text-3xl font-bold text-green-600">{{ $approvalRate }}%</p>
                        <p class="mt-1 text-sm text-gray-500">Requests approved vs total submitted</p>
                    </div>
                </div>
            </div>

            {{-- Monthly Trend --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200 bg-white px-6 py-4">
                    <h3 class="text-lg font-medium text-gray-900">Monthly Leave Trend ({{ $selectedYear }})</h3>
                </div>
                <div class="p-6">
                    @if($monthlyTrend->count() > 0)
                        <div class="space-y-3">
                            @php
                                $maxDays = $monthlyTrend->max('total_days') ?: 1;
                            @endphp
                            @foreach($monthlyTrend as $trend)
                                @php
                                    $monthName = DateTime::createFromFormat('!m', $trend->month)->format('F');
                                    $percentage = ($trend->total_days / $maxDays) * 100;
                                @endphp
                                <div>
                                    <div class="mb-1 flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-700">{{ $monthName }}</span>
                                        <span class="text-sm text-gray-600">
                                            {{ $trend->count }} requests ({{ number_format($trend->total_days, 1) }} days)
                                        </span>
                                    </div>
                                    <div class="h-4 w-full rounded-full bg-gray-200">
                                        <div class="h-4 rounded-full bg-navy-600" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No leave data available for {{ $selectedYear }}.</p>
                    @endif
                </div>
            </div>

            {{-- Leave Type Distribution and Department Breakdown --}}
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                {{-- Leave Type Distribution --}}
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h3 class="text-lg font-medium text-gray-900">Leave Type Distribution</h3>
                    </div>
                    <div class="p-6">
                        @if($leaveTypeDistribution->count() > 0)
                            <div class="space-y-4">
                                @php
                                    $totalTypeRequests = $leaveTypeDistribution->sum('count');
                                @endphp
                                @foreach($leaveTypeDistribution as $type)
                                    @php
                                        $percentage = $totalTypeRequests > 0 ? ($type->count / $totalTypeRequests) * 100 : 0;
                                    @endphp
                                    <div>
                                        <div class="mb-1 flex items-center justify-between">
                                            <span class="text-sm font-medium text-gray-700">
                                                {{ str_replace('_', ' ', ucwords($type->leave_type, '_')) }}
                                            </span>
                                            <span class="text-sm text-gray-600">
                                                {{ $type->count }} ({{ number_format($percentage, 1) }}%)
                                            </span>
                                        </div>
                                        <div class="h-3 w-full rounded-full bg-gray-200">
                                            <div class="h-3 rounded-full bg-blue-600" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500">{{ number_format($type->total_days, 1) }} total days</p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500">No leave type data available.</p>
                        @endif
                    </div>
                </div>

                {{-- Department Breakdown --}}
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h3 class="text-lg font-medium text-gray-900">Department Breakdown</h3>
                    </div>
                    <div class="p-6">
                        @if($departmentBreakdown->count() > 0)
                            <div class="space-y-4">
                                @php
                                    $totalDeptRequests = $departmentBreakdown->sum('count');
                                @endphp
                                @foreach($departmentBreakdown as $dept)
                                    @php
                                        $percentage = $totalDeptRequests > 0 ? ($dept->count / $totalDeptRequests) * 100 : 0;
                                    @endphp
                                    <div>
                                        <div class="mb-1 flex items-center justify-between">
                                            <span class="text-sm font-medium text-gray-700">{{ $dept->department }}</span>
                                            <span class="text-sm text-gray-600">
                                                {{ $dept->count }} ({{ number_format($percentage, 1) }}%)
                                            </span>
                                        </div>
                                        <div class="h-3 w-full rounded-full bg-gray-200">
                                            <div class="h-3 rounded-full bg-purple-600" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500">{{ number_format($dept->total_days, 1) }} total days</p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500">No department data available (departments may not be assigned).</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Info Box --}}
            <div class="rounded-lg bg-blue-50 p-4">
                <h4 class="mb-2 text-sm font-medium text-gray-900">About These Reports:</h4>
                <ul class="list-inside list-disc space-y-1 text-sm text-gray-700">
                    <li>Data shown is for approved leave requests only</li>
                    <li>Monthly trend shows when leave starts (not the entire duration)</li>
                    <li>Approval rate includes all request statuses (pending, approved, denied, cancelled)</li>
                    <li>Department breakdown only shows employees with assigned departments</li>
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
