<x-app-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <a href="{{ route('leave-requests.index') }}" class="inline-flex items-center text-primary-800 dark:text-primary-400 hover:text-primary-900 dark:hover:text-primary-300 font-semibold transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Leave Request Details
                    </a>
                </div>

                @can('cancel', $leaveRequest)
                    @if (!$leaveRequest->isCancelled() && !$leaveRequest->isDenied())
                        <form method="POST" action="{{ route('leave-requests.cancel', $leaveRequest) }}" onsubmit="return confirm('Are you sure you want to cancel this leave request?');">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-red-600 dark:bg-red-500 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-wide hover:bg-red-700 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 active:scale-[0.98] transition-all duration-200 shadow-sm">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Cancel Request
                            </button>
                        </form>
                    @endif
                @endcan
            </div>
            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 border-l-4 border-green-500 text-green-800 dark:text-green-200 rounded-r-lg shadow-sm">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="font-semibold">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 text-red-800 dark:text-red-200 rounded-r-lg shadow-sm">
                    <div class="flex items-start">
                        <svg class="h-5 w-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Request Details Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl border border-gray-200 dark:border-gray-700 mb-6">
                <div class="p-6">
                    <!-- Status Banner -->
                    @php
                        $statusConfig = [
                            'pending' => [
                                'bg' => 'bg-yellow-50 dark:bg-yellow-900',
                                'border' => 'border-yellow-200 dark:border-yellow-700',
                                'text' => 'text-yellow-800 dark:text-yellow-200',
                                'badge' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100',
                            ],
                            'approved' => [
                                'bg' => 'bg-green-50 dark:bg-green-900',
                                'border' => 'border-green-200 dark:border-green-700',
                                'text' => 'text-green-800 dark:text-green-200',
                                'badge' => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100',
                            ],
                            'denied' => [
                                'bg' => 'bg-red-50 dark:bg-red-900',
                                'border' => 'border-red-200 dark:border-red-700',
                                'text' => 'text-red-800 dark:text-red-200',
                                'badge' => 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100',
                            ],
                            'cancelled' => [
                                'bg' => 'bg-gray-50 dark:bg-gray-700',
                                'border' => 'border-gray-200 dark:border-gray-600',
                                'text' => 'text-gray-800 dark:text-gray-200',
                                'badge' => 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-100',
                            ],
                        ];
                        $config = $statusConfig[$leaveRequest->status] ?? $statusConfig['pending'];
                    @endphp

                    <div class="mb-6 p-4 {{ $config['bg'] }} border {{ $config['border'] }} rounded-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium {{ $config['text'] }}">Request Status</span>
                            <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $config['badge'] }}">
                                {{ ucfirst($leaveRequest->status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Request Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-900 dark:text-gray-100">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Leave Type</h3>
                            <p class="text-lg font-semibold">{{ ucwords(str_replace('_', ' ', $leaveRequest->leave_type)) }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Duration</h3>
                            <p class="text-lg font-semibold">{{ $leaveRequest->start_date->diffInDays($leaveRequest->end_date) + 1 }} days</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Start Date</h3>
                            <p class="text-lg">{{ $leaveRequest->start_date->format('l, F j, Y') }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">End Date</h3>
                            <p class="text-lg">{{ $leaveRequest->end_date->format('l, F j, Y') }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Submitted</h3>
                            <p class="text-lg">{{ $leaveRequest->submitted_at->format('M d, Y h:i A') }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Reviewing Manager</h3>
                            <p class="text-lg">{{ $leaveRequest->manager->name }}</p>
                        </div>

                        @if ($leaveRequest->reviewed_at)
                            <div class="md:col-span-2">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Reviewed</h3>
                                <p class="text-lg">{{ $leaveRequest->reviewed_at->format('M d, Y h:i A') }}</p>
                            </div>
                        @endif

                        @if ($leaveRequest->employee_notes)
                            <div class="md:col-span-2">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Your Notes</h3>
                                <p class="text-base whitespace-pre-wrap">{{ $leaveRequest->employee_notes }}</p>
                            </div>
                        @endif

                        @if ($leaveRequest->manager_notes)
                            <div class="md:col-span-2">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Manager's Notes</h3>
                                <p class="text-base whitespace-pre-wrap">{{ $leaveRequest->manager_notes }}</p>
                            </div>
                        @endif

                        @if ($leaveRequest->hasAttachment())
                            <div class="md:col-span-2">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Supporting Document</h3>
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

            <!-- History Card -->
            @if ($leaveRequest->history->isNotEmpty())
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                            <svg class="h-5 w-5 mr-2 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Request History
                        </h3>

                        <div class="flow-root">
                            <ul class="-mb-8">
                                @foreach ($leaveRequest->history as $index => $historyItem)
                                    <li>
                                        <div class="relative pb-8">
                                            @if (!$loop->last)
                                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>
                                            @endif
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                                        <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div>
                                                        <div class="text-sm text-gray-900 dark:text-gray-100">
                                                            <span class="font-medium">{{ $historyItem->performedBy->name }}</span>
                                                            <span class="text-gray-500 dark:text-gray-400">{{ $historyItem->action }}</span>
                                                            this request
                                                        </div>
                                                        <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                                                            {{ $historyItem->created_at->format('M d, Y h:i A') }}
                                                        </p>
                                                    </div>
                                                    @if ($historyItem->notes)
                                                        <div class="mt-2 text-sm text-gray-700 dark:text-gray-300">
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
