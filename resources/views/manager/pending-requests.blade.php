<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-100 dark:bg-green-900/30 border-l-4 border-green-500 text-green-800 dark:text-green-200 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="font-semibold">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if ($pendingRequests->isEmpty())
                        <div class="text-center py-16">
                            <div class="mx-auto h-16 w-16 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center mb-4">
                                <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">No pending requests</h3>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">All requests from your team have been reviewed.</p>
                        </div>
                    @else
                        <div class="space-y-5">
                            @foreach ($pendingRequests as $request)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5 hover:bg-gray-50 dark:hover:bg-gray-750 transition-all duration-200 hover:shadow-md">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <!-- Employee Info -->
                                            <div class="flex items-center mb-4">
                                                <div class="h-12 w-12 rounded-full bg-gradient-horizon flex items-center justify-center text-white font-bold text-lg">
                                                    {{ substr($request->user->name, 0, 1) }}
                                                </div>
                                                <div class="ml-4">
                                                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                                        {{ $request->user->name }}
                                                    </h3>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                                        Submitted {{ $request->submitted_at->diffForHumans() }}
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Request Details -->
                                            <div class="ml-16 space-y-3">
                                                <div class="flex items-center text-sm">
                                                    <svg class="h-5 w-5 text-primary-600 dark:text-primary-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                                    </svg>
                                                    <div class="flex items-center gap-2">
                                                        <span class="font-semibold text-gray-900 dark:text-gray-100">{{ ucwords(str_replace('_', ' ', $request->leave_type)) }}</span>
                                                        @if ($request->hasAttachment())
                                                            <svg class="h-4 w-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" title="Has attachment">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                            </svg>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="flex items-center text-sm">
                                                    <svg class="h-5 w-5 text-primary-600 dark:text-primary-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    <span class="text-gray-900 dark:text-gray-100">
                                                        {{ $request->start_date->format('M d, Y') }} - {{ $request->end_date->format('M d, Y') }}
                                                        <span class="text-gray-600 dark:text-gray-400 ml-2">({{ $request->start_date->diffInDays($request->end_date) + 1 }} days)</span>
                                                    </span>
                                                </div>

                                                @if ($request->employee_notes)
                                                    <div class="mt-3 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg text-sm text-gray-900 dark:text-gray-100">
                                                        <span class="font-semibold">Note:</span> {{ Str::limit($request->employee_notes, 150) }}
                                                    </div>
                                                @endif

                                                <!-- Conflict Warnings -->
                                                @if (!empty($request->conflicts))
                                                    @foreach ($request->conflicts as $conflict)
                                                        @php
                                                            $severityConfig = [
                                                                'critical' => ['bg' => 'bg-red-50 dark:bg-red-900/20', 'border' => 'border-red-300 dark:border-red-700', 'icon' => 'text-red-500', 'text' => 'text-red-900 dark:text-red-200', 'detail' => 'text-red-800 dark:text-red-300'],
                                                                'high' => ['bg' => 'bg-orange-50 dark:bg-orange-900/20', 'border' => 'border-orange-300 dark:border-orange-700', 'icon' => 'text-orange-500', 'text' => 'text-orange-900 dark:text-orange-200', 'detail' => 'text-orange-800 dark:text-orange-300'],
                                                                'medium' => ['bg' => 'bg-yellow-50 dark:bg-yellow-900/20', 'border' => 'border-yellow-300 dark:border-yellow-700', 'icon' => 'text-yellow-500', 'text' => 'text-yellow-900 dark:text-yellow-200', 'detail' => 'text-yellow-800 dark:text-yellow-300'],
                                                                'low' => ['bg' => 'bg-blue-50 dark:bg-blue-900/20', 'border' => 'border-blue-300 dark:border-blue-700', 'icon' => 'text-blue-500', 'text' => 'text-blue-900 dark:text-blue-200', 'detail' => 'text-blue-800 dark:text-blue-300'],
                                                            ];
                                                            $config = $severityConfig[$conflict['severity']] ?? $severityConfig['medium'];
                                                        @endphp
                                                        <div class="mt-3 p-4 {{ $config['bg'] }} border-l-4 {{ $config['border'] }} rounded-r-lg">
                                                            <div class="flex">
                                                                <svg class="h-5 w-5 {{ $config['icon'] }} flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                                </svg>
                                                                <div class="ml-3 flex-1">
                                                                    <p class="text-sm font-semibold {{ $config['text'] }}">
                                                                        <span class="uppercase text-xs font-bold">{{ $conflict['severity'] }}:</span> {{ $conflict['message'] }}
                                                                    </p>
                                                                    @if (!empty($conflict['details']) && is_array($conflict['details']) && isset($conflict['details'][0]))
                                                                        <div class="mt-2 text-sm {{ $config['detail'] }}">
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
                                            </div>
                                        </div>

                                        <!-- Action Button -->
                                        <div class="ml-6">
                                            <a href="{{ route('manager.show-request', $request) }}" class="inline-flex items-center px-6 py-3 bg-gradient-horizon border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-wide hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 active:scale-[0.98] transition-all duration-200">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                Review
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-4">
                            {{ $pendingRequests->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
