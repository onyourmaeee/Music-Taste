<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Music Taste</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800;900&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        docudark: '#1A1A1D',
                        docupink: '#FF69B4',
                    },
                    fontFamily: {
                        syne: ['Syne', 'sans-serif'],
                        dm: ['DM Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'DM Sans', sans-serif; }
        h1, h2, h3, .font-display { font-family: 'Syne', sans-serif; }

        .glow-card {
            transition: all 0.3s ease;
        }
        .glow-card:hover {
            box-shadow: 0 0 30px rgba(255, 105, 180, 0.15);
            transform: translateY(-2px);
        }

        .nav-link {
            position: relative;
            transition: color 0.2s;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: #FF69B4;
            transition: width 0.3s ease;
        }
        .nav-link:hover::after, .nav-link.active::after {
            width: 100%;
        }
        .nav-link.active { color: #FF69B4; }

        /* Sidebar */
        .sidebar-link {
            transition: all 0.2s ease;
            border-left: 2px solid transparent;
        }
        .sidebar-link:hover, .sidebar-link.active {
            background: rgba(255, 105, 180, 0.08);
            border-left-color: #FF69B4;
            color: #FF69B4;
        }

        /* Stat card pulse */
        @keyframes soft-pulse {
            0%, 100% { opacity: 0.6; }
            50% { opacity: 1; }
        }
        .icon-pulse { animation: soft-pulse 3s infinite; }

        /* Chart container */
        .chart-wrap { position: relative; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: #0F0F0F; }
        ::-webkit-scrollbar-thumb { background: #FF69B4; border-radius: 2px; }

        /* Progress bar */
        .progress-bar {
            background: linear-gradient(90deg, #FF69B4, #ff9dd1);
            border-radius: 999px;
            transition: width 1s ease;
        }

        /* Number counter animation */
        @keyframes countUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .count-animate {
            animation: countUp 0.6s ease forwards;
        }

        /* Mobile sidebar */
        .mobile-sidebar { transform: translateX(-100%); transition: transform 0.3s ease; }
        .mobile-sidebar.open { transform: translateX(0); }
        .sidebar-overlay { opacity: 0; pointer-events: none; transition: opacity 0.3s ease; }
        .sidebar-overlay.open { opacity: 1; pointer-events: auto; }

        /* Grid noise texture */
        .noise-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.03'/%3E%3C/svg%3E");
            pointer-events: none;
            border-radius: inherit;
            z-index: 0;
        }
    </style>
</head>
<body class="bg-[#0F0F0F] text-white min-h-screen flex">

{{-- SIDEBAR --}}
    <div id="sidebar-overlay" class="sidebar-overlay fixed inset-0 bg-black/60 z-20 lg:hidden" onclick="toggleSidebar()"></div>
    <aside class="mobile-sidebar w-64 flex-col bg-[#111113] border-r border-gray-800/60 min-h-screen fixed left-0 top-0 z-30 lg:flex lg:!translate-x-0">
        <div class="px-6 py-7 border-b border-gray-800/60 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-docupink flex items-center justify-center text-black font-black text-lg">♫</div>
            <span class="font-black text-lg tracking-tight">Music<span class="text-docupink">Taste</span></span>
        </div>
        <nav class="flex-1 px-3 py-6 space-y-1">
            <p class="text-[10px] text-gray-600 uppercase tracking-widest font-semibold px-3 mb-3">Main</p>
            
            <a href="{{ route('dashboard') }}" 
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'active' : 'text-gray-400' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>

            <a href="{{ route('users.index') }}" 
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('users.*') ? 'active' : 'text-gray-400' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Users
            </a>

            <a href="{{ route('songs.index') }}" 
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('songs.*') ? 'active' : 'text-gray-400' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                Songs
            </a>

            <a href="{{ route('genres.index') }}" 
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('genres.*') ? 'active' : 'text-gray-400' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                Genres
            </a>

            <a href="{{ route('playlists.index') }}" 
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('playlists.*') ? 'active' : 'text-gray-400' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Playlists
            </a>

            <p class="text-[10px] text-gray-600 uppercase tracking-widest font-semibold px-3 mb-3 mt-6">Account</p>
            
            <a href="{{ route('settings') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('settings') ? 'active' : 'text-gray-400' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Settings
            </a>
        </nav>

        {{-- User --}}
        <div class="px-4 py-5 border-t border-gray-800/60 flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-docupink to-pink-300 flex items-center justify-center text-black font-black text-sm">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold truncate">{{ auth()->user()->name ?? 'Guest' }}</p>
                <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email ?? 'guest@mail.com' }}</p>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="text-gray-600 hover:text-docupink transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>
        </div>
    </aside>

    {{-- ===== MAIN CONTENT ===== --}}
    <main class="flex-1 lg:ml-64 min-h-screen flex flex-col">

        {{-- Top bar --}}
        <header class="sticky top-0 z-20 bg-[#0F0F0F]/80 backdrop-blur-md border-b border-gray-800/60 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="lg:hidden text-gray-400 hover:text-white p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    <h1 class="font-display font-black text-xl tracking-tight">Dashboard</h1>
                    <p class="text-xs text-gray-500 mt-0.5">Kumusta, <span class="text-docupink">{{ auth()->user()->username ?? auth()->user()->name ?? 'Guest' }}</span></p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-500 hidden sm:block">{{ now()->format('F d, Y') }}</span>
                <div class="w-2 h-2 rounded-full bg-docupink icon-pulse"></div>
            </div>
        </header>

        {{-- Content --}}
        <div class="flex-1 p-6 space-y-8">

            {{-- ===== STAT CARDS ===== --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

                {{-- Total Users --}}
                <div class="glow-card noise-bg relative bg-[#1A1A1D] border border-gray-800/70 rounded-2xl p-5 overflow-hidden">
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-10 h-10 rounded-xl bg-docupink/10 border border-docupink/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-docupink" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <span class="text-xs text-green-400 bg-green-400/10 px-2 py-0.5 rounded-full font-medium">+12%</span>
                        </div>
                        <p class="text-3xl font-display font-black count-animate">{{ $totalUsers ?? '248' }}</p>
                        <p class="text-sm text-gray-400 mt-1 font-medium">Total Users</p>
                    </div>
                    <div class="absolute bottom-0 right-0 text-docupink/5 text-[100px] font-black leading-none pointer-events-none select-none -mb-4 -mr-2">U</div>
                </div>

                {{-- Total Songs --}}
                <div class="glow-card noise-bg relative bg-[#1A1A1D] border border-gray-800/70 rounded-2xl p-5 overflow-hidden">
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-10 h-10 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                            </div>
                            <span class="text-xs text-green-400 bg-green-400/10 px-2 py-0.5 rounded-full font-medium">+34%</span>
                        </div>
                        <p class="text-3xl font-display font-black count-animate">{{ $totalSongs ?? '1,042' }}</p>
                        <p class="text-sm text-gray-400 mt-1 font-medium">Total Songs</p>
                    </div>
                    <div class="absolute bottom-0 right-0 text-purple-500/5 text-[100px] font-black leading-none pointer-events-none select-none -mb-4 -mr-2">♫</div>
                </div>

                {{-- Total Genres --}}
                <div class="glow-card noise-bg relative bg-[#1A1A1D] border border-gray-800/70 rounded-2xl p-5 overflow-hidden">
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            </div>
                            <span class="text-xs text-amber-400 bg-amber-400/10 px-2 py-0.5 rounded-full font-medium">Stable</span>
                        </div>
                        <p class="text-3xl font-display font-black count-animate">{{ $totalGenres ?? '18' }}</p>
                        <p class="text-sm text-gray-400 mt-1 font-medium">Genres</p>
                    </div>
                    <div class="absolute bottom-0 right-0 text-amber-500/5 text-[100px] font-black leading-none pointer-events-none select-none -mb-4 -mr-2">#</div>
                </div>

                {{-- Total Playlists --}}
                <div class="glow-card noise-bg relative bg-[#1A1A1D] border border-gray-800/70 rounded-2xl p-5 overflow-hidden">
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h10M4 18h6"/></svg>
                            </div>
                            <span class="text-xs text-green-400 bg-green-400/10 px-2 py-0.5 rounded-full font-medium">+8%</span>
                        </div>
                        <p class="text-3xl font-display font-black count-animate">{{ $totalPlaylists ?? '389' }}</p>
                        <p class="text-sm text-gray-400 mt-1 font-medium">Playlists</p>
                    </div>
                    <div class="absolute bottom-0 right-0 text-cyan-500/5 text-[100px] font-black leading-none pointer-events-none select-none -mb-4 -mr-2">≡</div>
                </div>
            </div>

            {{-- ===== CHARTS ROW ===== --}}
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

                {{-- User Registrations Line Chart --}}
                <div class="glow-card xl:col-span-2 bg-[#1A1A1D] border border-gray-800/70 rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="font-display font-bold text-lg">User Registrations</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Monthly overview</p>
                        </div>
                        <div class="flex gap-2">
                            <button class="text-xs bg-docupink/10 text-docupink border border-docupink/20 px-3 py-1 rounded-full font-medium">2024</button>
                        </div>
                    </div>
                    <div class="chart-wrap h-56">
                        <canvas id="userChart"></canvas>
                    </div>
                </div>

                {{-- Genre Doughnut --}}
                <div class="glow-card bg-[#1A1A1D] border border-gray-800/70 rounded-2xl p-6">
                    <div class="mb-6">
                        <h3 class="font-display font-bold text-lg">Top Genres</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Distribution</p>
                    </div>
                    <div class="chart-wrap h-44 flex items-center justify-center">
                        <canvas id="genreChart"></canvas>
                    </div>
                    <div class="mt-4 space-y-2" id="genre-legend"></div>
                </div>
            </div>

            {{-- ===== BOTTOM ROW ===== --}}
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

                {{-- Songs Per Month Bar --}}
                <div class="glow-card bg-[#1A1A1D] border border-gray-800/70 rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="font-display font-bold text-lg">Songs Added</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Per month</p>
                        </div>
                    </div>
                    <div class="chart-wrap h-48">
                        <canvas id="songChart"></canvas>
                    </div>
                </div>

                {{-- Top Genres by Users --}}
                <div class="glow-card bg-[#1A1A1D] border border-gray-800/70 rounded-2xl p-6">
                    <div class="mb-5">
                        <h3 class="font-display font-bold text-lg">Top Genres by Users</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Popularity breakdown</p>
                    </div>
                    <div class="space-y-4">
                        @forelse($topGenres as $g)
                        <div>
                            <div class="flex justify-between items-center mb-1.5">
                                <span class="text-sm font-medium text-gray-300">{{ $g->genre }}</span>
                                <span class="text-xs text-gray-500">{{ $g->total }} songs</span>
                            </div>
                            <div class="w-full bg-gray-800 rounded-full h-1.5">
                                <div class="progress-bar h-1.5" style="width: {{ $g->total / $maxGenre * 100 }}%"></div>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-500 text-sm">No genres with songs yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

        {{-- Footer --}}
        <footer class="px-6 py-4 border-t border-gray-800/60 text-center text-xs text-gray-600">
            MusicTaste &copy; {{ date('Y') }} — All rights reserved.
        </footer>
    </main>

    {{-- ===== CHARTS JS ===== --}}
    <script>
        Chart.defaults.color = '#6B7280';
        Chart.defaults.borderColor = 'rgba(255,255,255,0.05)';
        Chart.defaults.font.family = "'DM Sans', sans-serif";

        const pink = '#FF69B4';
        const pinkFade = 'rgba(255,105,180,0.12)';

        // --- USER LINE CHART ---
        const userCtx = document.getElementById('userChart').getContext('2d');
        const gradient = userCtx.createLinearGradient(0, 0, 0, 200);
        gradient.addColorStop(0, 'rgba(255,105,180,0.3)');
        gradient.addColorStop(1, 'rgba(255,105,180,0)');

        new Chart(userCtx, {
            type: 'line',
            data: {
                labels: @json($months),
                datasets: [{
                    label: 'Users',
                    data: @json($userChartData),
                    borderColor: pink,
                    backgroundColor: gradient,
                    borderWidth: 2.5,
                    pointBackgroundColor: pink,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    tension: 0.4,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { font: { size: 11 } } },
                    y: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { font: { size: 11 } } }
                }
            }
        });

        // --- GENRE DOUGHNUT ---
        const genreLabels = @json($genreLabels);
        const genreData   = @json($genreData);
        const genreColors = @json(array_slice($genreColors, 0, count($genreLabels)));

        const genreCtx = document.getElementById('genreChart').getContext('2d');
        new Chart(genreCtx, {
            type: 'doughnut',
            data: {
                labels: genreLabels,
                datasets: [{
                    data: genreData,
                    backgroundColor: genreColors.map(c => c + 'CC'),
                    borderColor: genreColors,
                    borderWidth: 2,
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: { legend: { display: false } }
            }
        });

        // Custom legend
        const legendContainer = document.getElementById('genre-legend');
        genreLabels.forEach((label, i) => {
            const row = document.createElement('div');
            row.className = 'flex items-center justify-between text-xs';
            row.innerHTML = `
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-sm flex-shrink-0" style="background:${genreColors[i]}"></div>
                    <span class="text-gray-400">${label}</span>
                </div>
                <span class="text-gray-300 font-medium">${genreData[i]}</span>
            `;
            legendContainer.appendChild(row);
        });

        function toggleSidebar() {
            document.querySelector('.mobile-sidebar')?.classList.toggle('open');
            document.getElementById('sidebar-overlay')?.classList.toggle('open');
        }

        // --- SONG BAR CHART ---
        const songCtx = document.getElementById('songChart').getContext('2d');
        new Chart(songCtx, {
            type: 'bar',
            data: {
                labels: @json($months),
                datasets: [{
                    label: 'Songs',
                    data: @json($songChartData),
                    backgroundColor: 'rgba(255,105,180,0.2)',
                    borderColor: pink,
                    borderWidth: 2,
                    borderRadius: 6,
                    hoverBackgroundColor: 'rgba(255,105,180,0.4)',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                    y: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { font: { size: 11 } } }
                }
            }
        });
    </script>

</body>
</html>