<x-app-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Error Messages -->
            @if ($errors->any())
                <div class="p-4 bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 rounded-lg shadow-sm">
                    <div class="flex">
                        <svg class="h-6 w-6 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <ul class="list-disc list-inside text-sm text-red-800 dark:text-red-200">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Conflict Warnings (if any) -->
            @if (!empty($conflicts))
                @foreach ($conflicts as $conflict)
                    @php
                        $severityConfig = [
                            'critical' => ['bg' => 'bg-red-50 dark:bg-red-900/30', 'border' => 'border-red-500', 'icon' => 'text-red-500', 'text' => 'text-red-900 dark:text-red-200', 'detail' => 'text-red-800 dark:text-red-300'],
                            'high' => ['bg' => 'bg-orange-50 dark:bg-orange-900/30', 'border' => 'border-orange-500', 'icon' => 'text-orange-500', 'text' => 'text-orange-900 dark:text-orange-200', 'detail' => 'text-orange-800 dark:text-orange-300'],
                            'medium' => ['bg' => 'bg-yellow-50 dark:bg-yellow-900/30', 'border' => 'border-yellow-500', 'icon' => 'text-yellow-500', 'text' => 'text-yellow-900 dark:text-yellow-200', 'detail' => 'text-yellow-800 dark:text-yellow-300'],
                            'low' => ['bg' => 'bg-blue-50 dark:bg-blue-900/30', 'border' => 'border-blue-500', 'icon' => 'text-blue-500', 'text' => 'text-blue-900 dark:text-blue-200', 'detail' => 'text-blue-800 dark:text-blue-300'],
                        ];
                        $config = $severityConfig[$conflict['severity']] ?? $severityConfig['medium'];
                    @endphp
                    <div class="p-5 {{ $config['bg'] }} border-l-4 {{ $config['border'] }} rounded-r-xl shadow-sm">
                        <div class="flex">
                            <svg class="h-7 w-7 {{ $config['icon'] }} flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            <div class="ml-4 flex-1">
                                <h3 class="text-base font-bold {{ $config['text'] }}">
                                    <span class="uppercase text-xs font-bold">{{ $conflict['severity'] }}:</span> Staffing Conflict Detected
                                </h3>
                                <p class="mt-2 text-sm {{ $config['detail'] }}">
                                    {{ $conflict['message'] }}
                                </p>
                                @if (!empty($conflict['details']) && is_array($conflict['details']) && isset($conflict['details'][0]))
                                    <div class="mt-3 text-sm {{ $config['detail'] }}">
                                        <ul class="list-disc list-inside space-y-1">
                                            @foreach ($conflict['details'] as $detail)
                                                @if (is_array($detail) && isset($detail['employee']))
                                                    <li>{{ $detail['employee'] }}: {{ $detail['dates'] }}</li>
                                                @elseif (is_array($detail) && isset($detail['dates']))
                                                    <li>{{ $detail['dates'] }} ({{ $detail['status'] ?? 'N/A' }})</li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif

            <!-- Request Details Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <!-- Employee Info Header -->
                    <div class="flex items-center mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="h-20 w-20 rounded-full bg-gradient-horizon flex items-center justify-center text-white text-3xl font-bold shadow-lg">
                            {{ substr($leaveRequest->user->name, 0, 1) }}
                        </div>
                        <div class="ml-6">
                            <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                                {{ $leaveRequest->user->name }}
                            </h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                {{ $leaveRequest->user->email }}
                            </p>
                        </div>
                    </div>

                    <!-- Request Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-900 dark:text-gray-100">
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <h3 class="text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Leave Type</h3>
                            <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ ucwords(str_replace('_', ' ', $leaveRequest->leave_type)) }}</p>
                        </div>

                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <h3 class="text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Duration</h3>
                            <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $leaveRequest->start_date->diffInDays($leaveRequest->end_date) + 1 }} days</p>
                        </div>

                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <h3 class="text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Start Date</h3>
                            <p class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $leaveRequest->start_date->format('l, F j, Y') }}</p>
                        </div>

                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <h3 class="text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">End Date</h3>
                            <p class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $leaveRequest->end_date->format('l, F j, Y') }}</p>
                        </div>

                        <div class="md:col-span-2 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <h3 class="text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Submitted</h3>
                            <p class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $leaveRequest->submitted_at->format('M d, Y h:i A') }} <span class="text-gray-600 dark:text-gray-400">({{ $leaveRequest->submitted_at->diffForHumans() }})</span></p>
                        </div>

                        @if ($leaveRequest->employee_notes)
                            <div class="md:col-span-2">
                                <h3 class="text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-3">Employee's Notes</h3>
                                <div class="p-5 bg-primary-50 dark:bg-primary-900/20 rounded-xl border border-primary-200 dark:border-primary-800">
                                    <p class="text-base whitespace-pre-wrap text-gray-900 dark:text-gray-100">{{ $leaveRequest->employee_notes }}</p>
                                </div>
                            </div>
                        @endif

                        @if ($leaveRequest->hasAttachment())
                            <div class="md:col-span-2">
                                <h3 class="text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-3">Supporting Document</h3>
                                <a href="{{ route('leave-requests.download-attachment', $leaveRequest) }}"
                                   class="inline-flex items-center px-4 py-2 bg-primary-600 dark:bg-primary-500 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-primary-700 dark:hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 active:scale-[0.98] transition-all duration-200 shadow-sm">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Download Attachment
                                </a>
                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                    {{ basename($leaveRequest->attachment_path) }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Action Forms -->
            @if ($leaveRequest->isPending())
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Approve Form -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl border-2 border-green-200 dark:border-green-800">
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-green-700 dark:text-green-400 mb-4 flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Approve Request
                            </h3>
                            <form method="POST" action="{{ route('manager.approve', $leaveRequest) }}">
                                @csrf
                                <div class="mb-5">
                                    <label for="approve_notes" class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">
                                        Notes (Optional)
                                    </label>
                                    <textarea id="approve_notes" name="manager_notes" rows="4"
                                              class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-gray-900 focus:border-green-500 dark:focus:border-green-500 focus:ring-2 focus:ring-green-500 dark:focus:ring-green-500 focus:ring-opacity-20 transition-all duration-200"
                                              placeholder="Add any comments for the employee..."></textarea>
                                </div>
                                <button type="submit" class="w-full inline-flex justify-center items-center px-6 py-3 bg-green-600 dark:bg-green-500 border border-transparent rounded-lg font-bold text-sm text-white uppercase tracking-wide hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 active:scale-[0.98] transition-all duration-200 shadow-md">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Approve Request
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Deny Form -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl border-2 border-red-200 dark:border-red-800">
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-red-700 dark:text-red-400 mb-4 flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Deny Request
                            </h3>
                            <form method="POST" action="{{ route('manager.deny', $leaveRequest) }}" onsubmit="return confirm('Are you sure you want to deny this request?');">
                                @csrf
                                <div class="mb-5">
                                    <label for="deny_notes" class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">
                                        Reason for Denial <span class="text-red-500 font-bold">*</span>
                                    </label>
                                    <textarea id="deny_notes" name="manager_notes" rows="4" required
                                              class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-gray-900 focus:border-red-500 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500 dark:focus:ring-red-500 focus:ring-opacity-20 transition-all duration-200"
                                              placeholder="Please explain why this request is being denied..."></textarea>
                                    <p class="mt-2 text-xs text-gray-600 dark:text-gray-400 font-medium">Required: A reason must be provided to the employee</p>
                                </div>
                                <button type="submit" class="w-full inline-flex justify-center items-center px-6 py-3 bg-red-600 dark:bg-red-500 border border-transparent rounded-lg font-bold text-sm text-white uppercase tracking-wide hover:bg-red-700 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 active:scale-[0.98] transition-all duration-200 shadow-md">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Deny Request
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <!-- Request Already Reviewed -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="p-8 text-center">
                        <div class="mx-auto h-16 w-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
                            <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <p class="text-lg font-semibold text-gray-700 dark:text-gray-300">
                            This request has already been <span class="font-bold">{{ $leaveRequest->status }}</span>.
                        </p>
                        @if ($leaveRequest->manager_notes)
                            <div class="mt-6 p-5 bg-gray-50 dark:bg-gray-700/50 rounded-xl text-left">
                                <p class="text-sm font-bold text-gray-800 dark:text-gray-200 mb-2">Manager's Notes:</p>
                                <p class="text-base text-gray-900 dark:text-gray-100">{{ $leaveRequest->manager_notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- History Timeline -->
            @if ($leaveRequest->history->isNotEmpty())
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-6 flex items-center">
                            <svg class="h-5 w-5 mr-2 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Request History
                        </h3>
                        <div class="flow-root">
                            <ul class="-mb-8">
                                @foreach ($leaveRequest->history as $historyItem)
                                    <li>
                                        <div class="relative pb-8">
                                            @if (!$loop->last)
                                                <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-300 dark:bg-gray-600" aria-hidden="true"></span>
                                            @endif
                                            <div class="relative flex space-x-4">
                                                <div>
                                                    <span class="h-10 w-10 rounded-full bg-gradient-horizon flex items-center justify-center ring-4 ring-white dark:ring-gray-800 shadow-md">
                                                        <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="flex-1 min-w-0 pt-1">
                                                    <div>
                                                        <div class="text-sm text-gray-900 dark:text-gray-100">
                                                            <span class="font-bold">{{ $historyItem->performedBy->name }}</span>
                                                            <span class="text-gray-600 dark:text-gray-400"> {{ $historyItem->action }}</span>
                                                            <span class="text-gray-900 dark:text-gray-100"> this request</span>
                                                        </div>
                                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 font-medium">
                                                            {{ $historyItem->created_at->format('M d, Y h:i A') }}
                                                        </p>
                                                    </div>
                                                    @if ($historyItem->notes)
                                                        <div class="mt-3 p-3 text-sm text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                                            {{ $historyItem->notes }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
