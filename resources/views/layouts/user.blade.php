<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO & Social Media Optimization -->
    <title>@yield('title', config('app.name', 'KilaShillingi')) - Simamia Fedha zako kwa Usahihi</title>
    <meta name="description" content="KilaShillingi ni mfumo bora wa kusimamia mapato, matumizi, bajeti na madeni kwa Watanzania. Rekodi kila shillingi na imarisha uchumi wako leo.">
    <meta name="keywords" content="fedha, usimamizi wa fedha, bajeti, mhasibu, Tanzania, matumizi, mapato, madeni, KilaShillingi, akiba">
    <meta name="author" content="KilaShillingi">
    <meta name="robots" content="index, follow">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="KilaShillingi - Mfumo wa Usimamizi wa Fedha Tanzania">
    <meta property="og:description" content="Mfumo rahisi uliotengenezwa mahususi kwa ajili ya usimamizi wa fedha za kila siku nchini Tanzania.">
    <meta property="og:image" content="{{ asset('xing_5968847.png') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="KilaShillingi - Simamia Fedha zako kwa Usahihi">
    <meta property="twitter:description" content="Mfumo rahisi uliotengenezwa mahususi kwa ajili ya usimamizi wa fedha za kila siku nchini Tanzania.">
    <meta property="twitter:image" content="{{ asset('xing_5968847.png') }}">

    <link rel="icon" type="image/png" href="{{ asset('xing_5968847.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome (AdminLTE style) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
<body class="bg-gray-50 font-sans text-gray-900 overflow-x-hidden">
    <!-- Mobile Sidebar Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden transition-opacity duration-300"></div>

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside id="main-sidebar" class="fixed inset-y-0 left-0 w-72 bg-emerald-900 text-white z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
            <div class="flex flex-col h-full">
                <!-- Logo Section -->
                <div class="p-6 border-b border-white/10 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('xing_5968847.png') }}" alt="Logo" class="h-10 w-auto bg-white rounded-lg p-1">
                        <span class="text-xl font-bold tracking-tight">KilaShillingi</span>
                    </div>
                    <button id="close-sidebar" class="lg:hidden text-white/70 hover:text-white">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Navigation Menu -->
                <nav class="flex-1 overflow-y-auto p-4 space-y-6">
                    <!-- Main Menu -->
                    <div>
                        <div class="px-4 mb-3 text-[10px] font-bold text-white/40 uppercase tracking-widest">Muhtasari</div>
                        <div class="space-y-1">
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/5 hover:text-white' }} transition">
                                <i class="fas fa-tachometer-alt w-5 text-center"></i>
                                <span>Nyumbani</span>
                            </a>
                            <a href="{{ route('expenses.create') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('expenses.create') ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/5 hover:text-white' }} transition">
                                <i class="fas fa-minus-circle w-5 text-center"></i>
                                <span>Ongeza Matumizi</span>
                            </a>
                            <a href="{{ route('income.create') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('income.create') ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/5 hover:text-white' }} transition">
                                <i class="fas fa-plus-circle w-5 text-center"></i>
                                <span>Ongeza Mapato</span>
                            </a>
                            <a href="{{ route('debts.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('debts.*') ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/5 hover:text-white' }} transition">
                                <i class="fas fa-hand-holding-usd w-5 text-center"></i>
                                <span>Madeni</span>
                            </a>
                        </div>
                    </div>

                    <!-- Analysis Menu -->
                    <div>
                        <div class="px-4 mb-3 text-[10px] font-bold text-white/40 uppercase tracking-widest">Uchambuzi</div>
                        <div class="space-y-1">
                            <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('reports.*') ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/5 hover:text-white' }} transition">
                                <i class="fas fa-file-invoice-dollar w-5 text-center"></i>
                                <span>Ripoti</span>
                            </a>
                            <a href="{{ route('budget.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('budget.*') ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/5 hover:text-white' }} transition">
                                <i class="fas fa-chart-pie w-5 text-center"></i>
                                <span>Bajeti</span>
                            </a>
                        </div>
                    </div>

                    <!-- Others -->
                    <div>
                        <div class="px-4 mb-3 text-[10px] font-bold text-white/40 uppercase tracking-widest">Mengineyo</div>
                        <div class="space-y-1">
                            <a href="{{ route('reminders.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('reminders.*') ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/5 hover:text-white' }} transition">
                                <i class="fas fa-bell w-5 text-center"></i>
                                <span>Kumbusho</span>
                            </a>
                            <a href="{{ route('history.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('history.*') ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/5 hover:text-white' }} transition">
                                <i class="fas fa-history w-5 text-center"></i>
                                <span>Historia</span>
                            </a>
                            <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('settings.*') ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/5 hover:text-white' }} transition">
                                <i class="fas fa-cog w-5 text-center"></i>
                                <span>Mipangilio</span>
                            </a>
                        </div>
                    </div>
                </nav>

                <!-- Admin Link -->
                @if(Auth::user()->isAdmin())
                <div class="p-4 border-t border-white/10">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg bg-red-600/20 text-red-100 hover:bg-red-600/30 transition border border-red-600/30">
                        <i class="fas fa-user-shield w-5 text-center"></i>
                        <span>Admin Panel</span>
                    </a>
                </div>
                @endif
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 lg:ml-72 min-w-0 flex flex-col">
            <!-- Header -->
            <header class="h-16 bg-white border-b border-gray-200 sticky top-0 z-30 px-4 sm:px-8 flex items-center justify-between">
                <!-- Left: Sidebar Toggle -->
                <button id="open-sidebar" class="lg:hidden p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>

                <div class="flex-1 lg:flex-none ml-4 lg:ml-0">
                    <h1 class="text-lg font-bold text-gray-900 truncate">@yield('page_title', 'Dashibodi')</h1>
                </div>

                <!-- Right: Profile Dropdown -->
                <div class="relative" id="profile-dropdown-wrapper">
                    <button id="profile-dropdown-btn" class="flex items-center gap-3 p-1 hover:bg-gray-50 rounded-full transition group focus:outline-none">
                        <div class="w-9 h-9 rounded-full bg-emerald-700 flex items-center justify-center text-white font-bold shadow-sm overflow-hidden shrink-0">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                            @else
                                {{ substr(Auth::user()->name, 0, 1) }}
                            @endif
                        </div>
                        <div class="text-left hidden sm:block pr-2">
                            <div class="text-sm font-semibold text-gray-900 leading-none mb-1">{{ Auth::user()->name }}</div>
                            <div class="text-[10px] font-bold text-gray-500 uppercase tracking-tight">Mtumiaji</div>
                        </div>
                        <i class="fas fa-chevron-down text-xs text-gray-400 transition group-hover:text-gray-600 shrink-0 pr-2"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="profile-dropdown-menu" class="absolute right-0 mt-3 w-64 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden transform scale-95 opacity-0 pointer-events-none transition-all duration-200 origin-top-right z-50">
                        <div class="p-4 border-b border-gray-50 bg-gray-50/50 text-center">
                            <div class="w-16 h-16 rounded-full bg-emerald-700 flex items-center justify-center text-white font-bold mx-auto mb-3 text-2xl overflow-hidden">
                                @if(Auth::user()->avatar)
                                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                                @else
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                @endif
                            </div>
                            <div class="text-sm font-bold text-gray-900">{{ Auth::user()->name }}</div>
                            <div class="text-[11px] text-gray-500 truncate mt-1">{{ Auth::user()->email }}</div>
                        </div>
                        <div class="p-2 space-y-1">
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-50 transition text-sm">
                                <i class="fas fa-user-circle w-5 text-gray-400"></i>
                                <span>Profaili Yangu</span>
                            </a>
                            <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-50 transition text-sm">
                                <i class="fas fa-cog w-5 text-gray-400"></i>
                                <span>Mipangilio</span>
                            </a>
                            @if(Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-red-600 hover:bg-red-50 transition text-sm">
                                <i class="fas fa-user-shield w-5 text-red-400"></i>
                                <span>Dashboard ya Admin</span>
                            </a>
                            @endif
                        </div>
                        <div class="p-2 border-t border-gray-50 bg-gray-50/30">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-red-50 hover:text-red-700 transition text-sm">
                                    <i class="fas fa-sign-out-alt w-5 text-gray-400 group-hover:text-red-500"></i>
                                    <span>Ondoka</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="flex-1 p-4 sm:p-8">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Layout Scripts -->
    <script>
        (function() {
            // Sidebar Elements
            const sidebar = document.getElementById('main-sidebar');
            const openSidebarBtn = document.getElementById('open-sidebar');
            const closeSidebarBtn = document.getElementById('close-sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            // Toggle Sidebar
            const toggleSidebar = (show) => {
                sidebar.classList.toggle('-translate-x-full', !show);
                overlay.classList.toggle('hidden', !show);
                document.body.classList.toggle('overflow-hidden', show);
            };

            openSidebarBtn?.addEventListener('click', () => toggleSidebar(true));
            closeSidebarBtn?.addEventListener('click', () => toggleSidebar(false));
            overlay?.addEventListener('click', () => toggleSidebar(false));

            // Profile Dropdown
            const dropdownBtn = document.getElementById('profile-dropdown-btn');
            const dropdownMenu = document.getElementById('profile-dropdown-menu');
            const dropdownWrapper = document.getElementById('profile-dropdown-wrapper');

            const toggleDropdown = (show) => {
                if (show) {
                    dropdownMenu.classList.remove('scale-95', 'opacity-0', 'pointer-events-none');
                    dropdownMenu.classList.add('scale-100', 'opacity-100');
                } else {
                    dropdownMenu.classList.add('scale-95', 'opacity-0', 'pointer-events-none');
                    dropdownMenu.classList.remove('scale-100', 'opacity-100');
                }
            };

            dropdownBtn?.addEventListener('click', (e) => {
                e.stopPropagation();
                const isVisible = !dropdownMenu.classList.contains('opacity-0');
                toggleDropdown(!isVisible);
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!dropdownWrapper.contains(e.target)) {
                    toggleDropdown(false);
                }
            });

            // Close sidebar on resize if screen is large
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) {
                    toggleSidebar(false);
                }
            });
        })();
    </script>
</body>
</html>
