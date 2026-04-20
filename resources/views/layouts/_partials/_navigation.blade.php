<nav class="bg-secondary fixed w-full top-0 z-40 border-b border-gray-200">
    <div class="w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-24">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('welcome') }}" class="flex items-center group">
                    <div class="logo-container brand-logo  flex ">

                    </div>
                </a>
            </div>

            {{-- <!-- Desktop Navigation - Centered -->
            @auth
                <div class="hidden lg:flex items-center space-x-8">
                    @foreach (mainMenu() as $menu)
                        <a href="{{ route($menu->key) }}"
                            class="
                                text-md menu hover:menu-active transition-colors duration-200
                                {{ request()->routeIs($menu->key) ? 'menu-active font-bold' : '' }}
                            ">{!! $menu->title !!}</a>
                    @endforeach
                </div>
            @endauth --}}


            <!-- Right Side Actions -->
            <div class="flex items-center space-x-4">
                @guest
                    <a href="{{ route('login') }}"
                        class="primary-button py-2.5 px-4 text-[15px] font-medium rounded-md">Sign In</a>
                @else
                    <!-- User Dropdown -->
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <div
                            class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0 gap-4">
                            <!-- Notifications Dropdown -->
                            {{-- <div class="relative" x-data="{ open: false }" @click.away="open = false">
                                 <button @click="open = !open" type="button"
                                    class="relative rounded-full p-2 text-[#1F3B67] hover:text-[#FFB901] hover:bg-gray-100 transition-all duration-200 focus:outline-none">
                                    <span class="sr-only">Notifications</span>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                        class="w-6 h-6">
                                        <path
                                            d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    @if (auth()->user()->unreadNotifications)
                                        @if (auth()->user()->unreadNotifications->count() > 0)
                                            <span
                                                class="animate-ping-3 absolute top-1.5 right-1.5 w-2 h-2 bg-[#FFB901] rounded-full ring-2 ring-white"></span>
                                        @endif
                                    @endif
                                </button> 

                                <!-- Notifications Dropdown Menu -->
                                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute right-0 mt-3 w-80 bg-white rounded-2xl shadow-xl ring-1 ring-black ring-opacity-5 overflow-hidden z-50"
                                    style="display: none;">

                                    <!-- Notifications -->
                                    <div class="max-h-96 overflow-y-auto">
                                        @forelse(auth()->user()->unreadNotifications->take(10) as $notification)
                                            <div
                                                class="block px-4 py-3 hover:bg-gray-50 transition-colors duration-150 border-b border-gray-50">
                                                <div class="flex gap-3">
                                                    <div class="flex-shrink-0">
                                                        <div
                                                            class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                stroke-width="1.5" class="w-5 h-5 text-[#1F3B67]">
                                                                <path
                                                                    d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"
                                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <a href="{{ $notification->data['url'] ?? '#' }}"
                                                            onclick="markAsRead('{{ $notification->id }}')" class="block">
                                                            <p class="text-sm font-medium text-[#1F3B67]">
                                                                {{ $notification->data['title'] ?? 'Notification' }}</p>
                                                            <p class="text-xs text-[#1F3B67] mt-1">
                                                                {{ $notification->data['message'] ?? '' }}</p>
                                                            <p class="text-xs text-[#1F3B67] mt-1">
                                                                {{ $notification->created_at->diffForHumans() }}</p>
                                                        </a>
                                                    </div>
                                                    <div class="flex-shrink-0">
                                                        <button onclick="markAsRead('{{ $notification->id }}')"
                                                            class="text-[#1F3B67] hover:text-[#FFB901]">
                                                            <div class="w-2 h-2 bg-[#1F3B67] rounded-full"></div>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="p-4 text-center">
                                                <p class="text-sm font-medium text-[#1F3B67]">Oops! No new notifications</p>
                                                <p class="text-xs text-[#1F3B67] mt-1">We'll let you know when deadlines are
                                                    approaching, or there is a course update</p>
                                            </div>
                                        @endforelse
                                    </div>

                                    @if (auth()->user()->unreadNotifications->count() > 0)
                                        <div class="border-t border-gray-100 px-4 py-2">
                                            <button onclick="markAllAsRead()"
                                                class="text-sm text-[#1F3B67] hover:text-[#FFB901] font-medium">
                                                Mark all as read
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div> --}}
                            <button @click="open = !open" class="flex items-center space-x-3 focus:outline-none group">
                                <span
                                    class="hidden lg:flex items-center gap-2 text-sm font-medium bg-primary text-white p-2 rounded-lg">
                                    {{ucfirst(Auth::user()->name_value) }}

                                    <svg class="w-4 h-4 shrink-0 inline-block" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m19 9-7 7-7-7" />
                                    </svg>
                                </span>

                            </button>
                        </div>

                        <!-- Dropdown Menu -->
                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="rounded-lg absolute right-0 mt-3 w-56 bg-white shadow-xl ring-1 ring-black ring-opacity-5 overflow-hidden"
                            style="display: none;">

                            <!-- User Info -->
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-sm font-semibold text-[#1F3B67]">{{ucfirst(Auth::user()->first_name.' '.Auth::user()->last_name) }}</p>
                                <p class="text-xs text-[#1F3B67] mt-0.5 truncate">{{ Auth::user()->email }}</p>
                            </div>

                            <!-- Menu Items -->
                            <div class="py-2">

                                {{-- <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2.5 text-sm text-[#1F3B67] hover:bg-gray-50 transition-colors duration-150">
                                    <svg class="w-4 h-4 mr-3 text-[#1F3B67]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Profile
                                </a>

                                <a href="{{ route('community.index') }}" class="flex items-center px-4 py-2.5 text-sm text-[#1F3B67] hover:bg-gray-50 transition-colors duration-150">
                                    <svg class="w-4 h-4 mr-3 text-[#1F3B67]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    My Community
                                </a>  --}}

                                @if (auth()->guard('twill_users')->user()?->role_id)
                                    @if (auth()->guard('twill_users')->user()->role_id === 1 ||
                                            auth()->guard('twill_users')->user()->is_superadmin ||
                                            auth()->guard('twill_users')->user()->role_value === 'SUPERADMIN')
                                        <a href="{{ route('twill.dashboard') }}"
                                            class="flex items-center px-4 py-2.5 text-sm text-[#1F3B67] hover:bg-gray-50 transition-colors duration-150">
                                            <svg class="w-4 h-4 mr-3 text-[#1F3B67]" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                                </path>
                                            </svg>
                                            Admin Dashboard
                                        </a>
                                    @elseif (auth()->guard('twill_users')->user()->role_id === 3 ||
                                            auth()->guard('twill_users')->user()->is_company_admin ||
                                            auth()->guard('twill_users')->user()->role_value === 'Company HR')
                                        <a href="{{ route(name: 'twill.dashboard') }}"
                                            class="flex items-center px-4 py-2.5 text-sm text-[#1F3B67] hover:bg-gray-50 transition-colors duration-150">
                                            <svg class="w-4 h-4 mr-3 text-[#1F3B67]" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                                </path>
                                            </svg>
                                            HR Dashboard
                                        </a>
                                    @endif
                                @endif
                            </div>

                            <!-- Logout -->
                            <div class="border-t border-gray-100 py-2">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="flex items-center w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors duration-150">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                            </path>
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endguest

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-button"
                    class="lg:hidden text-[#1F3B67] hover:text-[#FFB901] transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden lg:hidden bg-white border-t border-gray-100">
        <div class="px-4 py-6 space-y-1">
            {{-- @auth
                @foreach (mainMenu() as $menu)
                    <a href="{{ route($menu->key) }}"
                        class="block px-3 py-2 text-base font-medium text-[#1F3B67] hover:text-[#FFB901] hover:bg-gray-50 rounded-lg transition-colors duration-150">{!! $menu->title !!}</a>
                @endforeach
            @endauth --}}

            @guest
                <div class="pt-4 space-y-2 border-t border-gray-100 mt-4">
                     <a href="{{ route('login') }}"
                        class="primary-button py-2.5 px-4 text-[15px] font-medium rounded-md">Sign In</a>
                </div>
            @else
                <!-- Mobile User Section -->
                <div class="pt-4 space-y-1 border-t border-gray-100 mt-4">
                    <div class="px-3 py-2 mb-2">
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-10 h-10 rounded-full bg-[#1F3B67] flex items-center justify-center text-white font-semibold text-sm shadow-sm">
                                {{ strtoupper(substr(Auth::user()->first_name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-[#1F3B67]">{{ucfirst(Auth::user()->first_name.' '.Auth::user()->last_name) }}</p>
                                <p class="text-xs text-[#1F3B67]">{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- <a href="{{ route('profile.edit') }}" class="flex items-center px-3 py-2 text-base font-medium text-[#1F3B67] hover:text-[#FFB901] hover:bg-gray-50 rounded-lg transition-colors duration-150">
                        <svg class="w-5 h-5 mr-3 text-[#1F3B67]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Profile
                    </a>
                    
                    <a href="{{ route('community.index') }}" class="flex items-center px-3 py-2 text-base font-medium text-[#1F3B67] hover:text-[#FFB901] hover:bg-gray-50 rounded-lg transition-colors duration-150">
                        <svg class="w-5 h-5 mr-3 text-[#1F3B67]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        My Community
                    </a> --}}

                    {{-- @if (auth()->guard('twill_users')->user()?->role_id)
                        @if (auth()->guard('twill_users')->user()->role_id === 1 || auth()->guard('twill_users')->user()->is_superadmin || auth()->guard('twill_users')->user()->role_value === 'SUPERADMIN')
                            <a href="{{ route('twill.dashboard') }}"
                                class="flex items-center px-4 py-2.5 text-sm text-[#1F3B67] hover:bg-gray-50 transition-colors duration-150">
                                <svg class="w-4 h-4 mr-3 text-[#1F3B67]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                    </path>
                                </svg>
                                Admin Dashboard
                            </a>
                        @elseif (
                            (auth()->guard('twill_users')->user()->role_id === 3) || 
                            (auth()->guard('twill_users')->user()->is_company_admin) || 
                            (auth()->guard('twill_users')->user()->role_value === 'Company HR')
                        )
                            <a href="{{ route(name: 'twill.dashboard') }}"
                                class="flex items-center px-4 py-2.5 text-sm text-[#1F3B67] hover:bg-gray-50 transition-colors duration-150">
                                <svg class="w-4 h-4 mr-3 text-[#1F3B67]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                    </path>
                                </svg>
                                HR Dashboard
                            </a>
                        @endif
                    @endif
                     --}}
                    <form method="POST" action="{{ route('logout') }}" class="pt-2 border-t border-gray-100 mt-2">
                        @csrf
                        <button type="submit"
                            class="flex items-center w-full px-3 py-2 text-base font-medium text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-150">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                </path>
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            @endguest
        </div>
    </div>
</nav>

<script>
    function markAsRead(notificationId) {
        fetch(`/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                }
            });
    }

    function markAllAsRead() {
        const notifications = document.querySelectorAll('[onclick^="markAsRead"]');
        const promises = Array.from(notifications).map(el => {
            const match = el.getAttribute('onclick').match(/'([^']+)'/);
            if (match) {
                return fetch(`/notifications/${match[1]}/read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
            }
        });

        Promise.all(promises).then(() => window.location.reload());
    }
</script>
