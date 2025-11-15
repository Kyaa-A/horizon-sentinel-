<x-app-layout>
    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <h2 class="mb-6 text-2xl font-bold text-gray-800">
                Leave Balance Management
            </h2>

            {{-- Filters --}}
            <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="GET" action="{{ route('hr-admin.balances') }}" class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <label for="user_id" class="block text-sm font-medium text-gray-700">Employee</label>
                            <select name="user_id" id="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-navy-500 focus:ring-navy-500 sm:text-sm">
                                <option value="">All Employees</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="leave_type" class="block text-sm font-medium text-gray-700">Leave Type</label>
                            <select name="leave_type" id="leave_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-navy-500 focus:ring-navy-500 sm:text-sm">
                                <option value="">All Types</option>
                                <option value="vacation" {{ request('leave_type') === 'vacation' ? 'selected' : '' }}>Vacation</option>
                                <option value="sick_leave" {{ request('leave_type') === 'sick_leave' ? 'selected' : '' }}>Sick Leave</option>
                                <option value="personal_leave" {{ request('leave_type') === 'personal_leave' ? 'selected' : '' }}>Personal Leave</option>
                                <option value="unpaid_leave" {{ request('leave_type') === 'unpaid_leave' ? 'selected' : '' }}>Unpaid Leave</option>
                            </select>
                        </div>

                        <div class="flex items-end gap-2">
                            <button type="submit" class="flex-1 rounded-md bg-navy-600 px-4 py-2 text-sm font-medium text-white hover:bg-navy-700">
                                Filter
                            </button>
                            <a href="{{ route('hr-admin.balances') }}" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Balances Table --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Employee
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Leave Type
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Total Allocated
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Available
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Used
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Pending
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($balances as $balance)
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $balance->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $balance->user->email }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        {{ str_replace('_', ' ', ucwords($balance->leave_type, '_')) }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm text-gray-900">
                                        {{ number_format($balance->total_allocated_days, 1) }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                        <span class="text-sm font-medium text-green-600">{{ number_format($balance->available_days, 1) }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                        <span class="text-sm font-medium text-blue-600">{{ number_format($balance->used_days, 1) }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                        <span class="text-sm font-medium text-yellow-600">{{ number_format($balance->pending_days, 1) }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-center text-sm font-medium">
                                        <a href="{{ route('hr-admin.balances.edit', $balance) }}" class="text-navy-600 hover:text-navy-900">
                                            Adjust
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No balances found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($balances->hasPages())
                    <div class="border-t border-gray-200 bg-white px-4 py-3">
                        {{ $balances->links() }}
                    </div>
                @endif
            </div>

            {{-- Balance Legend --}}
            <div class="mt-4 rounded-lg bg-blue-50 p-4">
                <h4 class="mb-2 text-sm font-medium text-gray-900">Balance Breakdown:</h4>
                <div class="grid grid-cols-1 gap-2 text-sm text-gray-700 md:grid-cols-3">
                    <div><span class="font-medium text-green-600">Available:</span> Days the employee can request</div>
                    <div><span class="font-medium text-blue-600">Used:</span> Days from approved leave requests</div>
                    <div><span class="font-medium text-yellow-600">Pending:</span> Days reserved for pending requests</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
