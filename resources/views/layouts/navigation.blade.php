<nav x-data="{ open: false }" class="bg-white/95 dark:bg-gray-800/95 backdrop-blur-lg border-b border-gray-200 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ Auth::user()->isHRAdmin() ? route('hr-admin.dashboard') : (Auth::user()->isManager() ? route('manager.dashboard') : route('dashboard')) }}" class="flex items-center space-x-2">
                        <span class="text-2xl font-bold bg-gradient-horizon bg-clip-text text-transparent">Horizon Sentinel</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @if (Auth::user()->isHRAdmin())
                        <x-nav-link :href="route('hr-admin.dashboard')" :active="request()->routeIs('hr-admin.dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('hr-admin.users')" :active="request()->routeIs('hr-admin.users*')">
                            {{ __('Users') }}
                        </x-nav-link>
                        <x-nav-link :href="route('hr-admin.balances')" :active="request()->routeIs('hr-admin.balances*')">
                            {{ __('Balances') }}
                        </x-nav-link>
                        <x-nav-link :href="route('hr-admin.holidays')" :active="request()->routeIs('hr-admin.holidays*')">
                            {{ __('Holidays') }}
                        </x-nav-link>
                        <x-nav-link :href="route('hr-admin.reports')" :active="request()->routeIs('hr-admin.reports')">
                            {{ __('Reports') }}
                        </x-nav-link>
                    @elseif (Auth::user()->isManager())
                        <x-nav-link :href="route('manager.dashboard')" :active="request()->routeIs('manager.dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('manager.pending-requests')" :active="request()->routeIs('manager.pending-requests') || request()->routeIs('manager.show-request')">
                            {{ __('Pending') }}
                        </x-nav-link>
                        <x-nav-link :href="route('manager.team-status')" :active="request()->routeIs('manager.team-status')">
                            {{ __('Team Status') }}
                        </x-nav-link>
                        <x-nav-link :href="route('manager.team-calendar')" :active="request()->routeIs('manager.team-calendar')">
                            {{ __('Calendar') }}
                        </x-nav-link>
                        <x-nav-link :href="route('manager.delegations')" :active="request()->routeIs('manager.delegations*')">
                            {{ __('Delegations') }}
                        </x-nav-link>
                        <x-nav-link :href="route('leave-requests.index')" :active="request()->routeIs('leave-requests.*')">
                            {{ __('My Requests') }}
                        </x-nav-link>
                    @else
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('leave-requests.index')" :active="request()->routeIs('leave-requests.*')">
                            {{ __('My Leave Requests') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Notifications Dropdown -->
                <div class="relative" style="margin-right: 32px;" x-data="{
                    open: false,
                    unreadCount: {{ Auth::user()->unreadNotifications()->count() }}
                }" @click.away="open = false">
                    <button @click="open = !open" class="relative rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-navy-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span x-show="unreadCount > 0" x-text="unreadCount" class="absolute right-0 top-0 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs font-bold text-white"></span>
                    </button>

                    <!-- Dropdown -->
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 z-50 mt-2 w-96 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5"
                         style="display: none;">
                        <div class="border-b border-gray-200 px-4 py-3">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                                <a href="{{ route('notifications.index') }}" class="text-xs font-medium text-navy-600 hover:text-navy-900">View All</a>
                            </div>
                        </div>

                        <div class="max-h-96 overflow-y-auto">
                            @forelse(Auth::user()->unreadNotifications()->limit(5)->get() as $notification)
                                @php
                                    $data = $notification->data;
                                @endphp
                                <a href="{{ route('notifications.mark-as-read', $notification->id) }}"
                                   onclick="event.preventDefault(); document.getElementById('notification-{{ $notification->id }}').submit();"
                                   class="block border-b border-gray-100 px-4 py-3 hover:bg-gray-50">
                                    <p class="text-sm font-medium text-gray-900">{{ $data['message'] ?? 'Notification' }}</p>
                                    <p class="mt-1 text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</p>
                                </a>
                                <form id="notification-{{ $notification->id }}" method="POST" action="{{ route('notifications.mark-as-read', $notification->id) }}" style="display: none;">
                                    @csrf
                                </form>
                            @empty
                                <div class="px-4 py-8 text-center">
                                    <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500">No new notifications</p>
                                </div>
                            @endforelse
                        </div>

                        @if(Auth::user()->unreadNotifications()->count() > 0)
                            <div class="border-t border-gray-200 px-4 py-2">
                                <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-center text-xs font-medium text-navy-600 hover:text-navy-900">
                                        Mark All as Read
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm leading-5 font-semibold rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-all duration-150 shadow-sm">
                            <svg class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-2">
                                <svg class="fill-current h-4 w-4 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if (Auth::user()->isHRAdmin())
                <x-responsive-nav-link :href="route('hr-admin.dashboard')" :active="request()->routeIs('hr-admin.dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('hr-admin.users')" :active="request()->routeIs('hr-admin.users*')">
                    {{ __('Users') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('hr-admin.balances')" :active="request()->routeIs('hr-admin.balances*')">
                    {{ __('Balances') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('hr-admin.holidays')" :active="request()->routeIs('hr-admin.holidays*')">
                    {{ __('Holidays') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('hr-admin.reports')" :active="request()->routeIs('hr-admin.reports')">
                    {{ __('Reports') }}
                </x-responsive-nav-link>
            @elseif (Auth::user()->isManager())
                <x-responsive-nav-link :href="route('manager.dashboard')" :active="request()->routeIs('manager.dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('manager.pending-requests')" :active="request()->routeIs('manager.pending-requests') || request()->routeIs('manager.show-request')">
                    {{ __('Pending') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('manager.team-status')" :active="request()->routeIs('manager.team-status')">
                    {{ __('Team Status') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('manager.team-calendar')" :active="request()->routeIs('manager.team-calendar')">
                    {{ __('Calendar') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('manager.delegations')" :active="request()->routeIs('manager.delegations*')">
                    {{ __('Delegations') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('leave-requests.index')" :active="request()->routeIs('leave-requests.*')">
                    {{ __('My Requests') }}
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('leave-requests.index')" :active="request()->routeIs('leave-requests.*')">
                    {{ __('My Leave Requests') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
