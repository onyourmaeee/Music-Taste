<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users — Music Taste</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
        body { font-family: 'DM Sans', sans-serif; overflow-x: hidden; }
        h1, h2, h3, .font-display { font-family: 'Syne', sans-serif; }
 
        .glow-card { transition: all 0.3s ease; }
        .glow-card:hover {
            box-shadow: 0 0 30px rgba(255, 105, 180, 0.15);
            transform: translateY(-2px);
        }
 
        .sidebar-link {
            transition: all 0.2s ease;
            border-left: 2px solid transparent;
        }
        .sidebar-link:hover, .sidebar-link.active {
            background: rgba(255, 105, 180, 0.08);
            border-left-color: #FF69B4;
            color: #FF69B4;
        }
 
        @keyframes soft-pulse {
            0%, 100% { opacity: 0.6; }
            50% { opacity: 1; }
        }
        .icon-pulse { animation: soft-pulse 3s infinite; }
 
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: #0F0F0F; }
        ::-webkit-scrollbar-thumb { background: #FF69B4; border-radius: 2px; }
 
        .modal-backdrop {
            backdrop-filter: blur(4px);
            background: rgba(0,0,0,0.6);
        }
 
        .table-row {
            transition: background 0.15s ease;
        }
        .table-row:hover {
            background: rgba(255, 105, 180, 0.04);
        }
 
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in { animation: fadeIn 0.4s ease forwards; }
 
        .btn-primary {
            background: #FF69B4;
            color: #000;
            font-weight: 700;
            transition: all 0.2s;
        }
        .btn-primary:hover {
            background: #ff85c1;
            box-shadow: 0 0 20px rgba(255,105,180,0.4);
        }
 
        .mobile-sidebar { transform: translateX(-100%); transition: transform 0.3s ease; }
        .mobile-sidebar.open { transform: translateX(0); }
        .sidebar-overlay { opacity: 0; pointer-events: none; transition: opacity 0.3s ease; }
        .sidebar-overlay.open { opacity: 1; pointer-events: auto; }

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
<body class="bg-[#0F0F0F] text-white min-h-screen flex w-full overflow-x-hidden">
 
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
        
        <div class="px-4 py-5 border-t border-gray-800/60 flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-docupink to-pink-300 flex items-center justify-center text-black font-black text-sm">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold truncate">{{ auth()->user()->name ?? 'Guest' }}</p>
                <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email ?? 'guest@mail.com' }}</p>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button class="text-gray-600 hover:text-docupink transition flex items-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>
        </div>
    </aside>
 
    {{-- ===== MAIN CONTENT ===== --}}
    <main class="flex-1 lg:ml-64 min-h-screen w-full max-w-full flex flex-col overflow-x-hidden">
 
        {{-- Top bar --}}
        <header class="sticky top-0 z-20 bg-[#0F0F0F]/80 backdrop-blur-md border-b border-gray-800/60 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="lg:hidden text-gray-400 hover:text-white p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    <h1 class="font-display font-black text-xl tracking-tight">User Management</h1>
                    <p class="text-xs text-gray-500 mt-0.5">Manage all registered users</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-500 hidden sm:block">{{ now()->format('F d, Y') }}</span>
                <div class="w-2 h-2 rounded-full bg-docupink icon-pulse"></div>
            </div>
        </header>
 
        <div class="flex-1 p-6 space-y-6 w-full max-w-full box-border">
 
            {{-- Flash Messages --}}
            @if(session('success'))
            <div class="fade-in flex items-center gap-3 bg-green-500/10 border border-green-500/30 text-green-400 px-4 py-3 rounded-xl text-sm">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
            @endif
 
            {{-- Stats + Add Button Row --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="noise-bg relative bg-[#1A1A1D] border border-gray-800/70 rounded-2xl px-5 py-3 overflow-hidden">
                        <div class="relative z-10 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-docupink/10 border border-docupink/20 flex items-center justify-center">
                                <svg class="w-4 h-4 text-docupink" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-xl font-display font-black">{{ $users->count() }}</p>
                                <p class="text-xs text-gray-500">Total Users</p>
                            </div>
                        </div>
                    </div>
                </div>
                <button onclick="document.getElementById('addModal').classList.remove('hidden')"
                        class="btn-primary flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold self-start sm:self-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add User
                </button>
            </div>
 
            {{-- Table --}}
            <div class="glow-card bg-[#1A1A1D] border border-gray-800/70 rounded-2xl overflow-hidden w-full">
                <div class="px-6 py-4 border-b border-gray-800/60 flex items-center justify-between">
                    <h3 class="font-display font-bold text-base">All Users</h3>
                    <span class="text-xs text-gray-500">{{ $users->count() }} records</span>
                </div>
 
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-sm min-w-[600px]">
                        <thead>
                            <tr class="border-b border-gray-800/60">
                                <th class="text-left px-6 py-3 text-xs text-gray-500 uppercase tracking-wider font-semibold">#</th>
                                <th class="text-left px-6 py-3 text-xs text-gray-500 uppercase tracking-wider font-semibold">User</th>
                                <th class="text-left px-6 py-3 text-xs text-gray-500 uppercase tracking-wider font-semibold">Username</th>
                                <th class="text-left px-6 py-3 text-xs text-gray-500 uppercase tracking-wider font-semibold">Email</th>
                                <th class="text-left px-6 py-3 text-xs text-gray-500 uppercase tracking-wider font-semibold">Joined</th>
                                <th class="text-right px-6 py-3 text-xs text-gray-500 uppercase tracking-wider font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800/40">
                            @forelse($users as $index => $user)
                            <tr class="table-row fade-in">
                                <td class="px-6 py-4 text-gray-500 text-xs">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-docupink to-pink-300 flex items-center justify-center text-black font-black text-xs flex-shrink-0">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <span class="font-medium text-white">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-400">{{ $user->username ?? '—' }}</td>
                                <td class="px-6 py-4 text-gray-400">{{ $user->email }}</td>
                                <td class="px-6 py-4 text-gray-500 text-xs">{{ $user->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Edit --}}
                                        <button
                                            onclick="openEditModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ addslashes($user->username ?? '') }}', '{{ addslashes($user->email) }}')"
                                            class="p-1.5 text-gray-500 hover:text-docupink hover:bg-docupink/10 rounded-lg transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        {{-- Delete --}}
                                        <button
                                            onclick="openDeleteModal({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                            class="p-1.5 text-gray-500 hover:text-red-400 hover:bg-red-400/10 rounded-lg transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center text-gray-600">
                                    <div class="flex flex-col items-center gap-3">
                                        <svg class="w-10 h-10 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <p class="text-sm">Walang users pa. Mag-add na!</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
 
        <footer class="mt-auto px-6 py-4 border-t border-gray-800/60 text-center text-xs text-gray-600">
            MusicTaste &copy; {{ date('Y') }} — All rights reserved.
        </footer>
    </main>
 
    {{-- ===== ADD USER MODAL ===== --}}
    <div id="addModal" class="hidden fixed inset-0 z-50 flex items-center justify-center modal-backdrop p-4">
        <div class="bg-[#1A1A1D] border border-gray-800/70 rounded-2xl w-full max-w-md p-6 fade-in">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-display font-bold text-lg">Add New User</h2>
                <button onclick="document.getElementById('addModal').classList.add('hidden')"
                        class="text-gray-500 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Full Name</label>
                    <input type="text" name="name" required placeholder="Juan dela Cruz"
                           class="w-full bg-[#0F0F0F] border border-gray-700/60 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-docupink transition">
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Username</label>
                    <input type="text" name="username" required placeholder="juandelacruz"
                           class="w-full bg-[#0F0F0F] border border-gray-700/60 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-docupink transition">
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Email</label>
                    <input type="email" name="email" required placeholder="juan@email.com"
                           class="w-full bg-[#0F0F0F] border border-gray-700/60 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-docupink transition">
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Password</label>
                    <input type="password" name="password" required placeholder="••••••••"
                           class="w-full bg-[#0F0F0F] border border-gray-700/60 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-docupink transition">
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Confirm Password</label>
                    <input type="password" name="password_confirmation" required placeholder="••••••••"
                           class="w-full bg-[#0F0F0F] border border-gray-700/60 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-docupink transition">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')"
                            class="flex-1 px-4 py-2.5 rounded-xl border border-gray-700/60 text-sm text-gray-400 hover:text-white hover:border-gray-600 transition">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 btn-primary px-4 py-2.5 rounded-xl text-sm">
                        Add User
                    </button>
                </div>
            </form>
        </div>
    </div>
 
    {{-- ===== EDIT USER MODAL ===== --}}
    <div id="editModal" class="hidden fixed inset-0 z-50 flex items-center justify-center modal-backdrop p-4">
        <div class="bg-[#1A1A1D] border border-gray-800/70 rounded-2xl w-full max-w-md p-6 fade-in">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-display font-bold text-lg">Edit User</h2>
                <button onclick="document.getElementById('editModal').classList.add('hidden')"
                        class="text-gray-500 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="editForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Full Name</label>
                    <input type="text" id="editName" name="name" required
                           class="w-full bg-[#0F0F0F] border border-gray-700/60 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-docupink transition">
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Username</label>
                    <input type="text" id="editUsername" name="username" required
                           class="w-full bg-[#0F0F0F] border border-gray-700/60 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-docupink transition">
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Email</label>
                    <input type="email" id="editEmail" name="email" required
                           class="w-full bg-[#0F0F0F] border border-gray-700/60 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-docupink transition">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')"
                            class="flex-1 px-4 py-2.5 rounded-xl border border-gray-700/60 text-sm text-gray-400 hover:text-white hover:border-gray-600 transition">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 btn-primary px-4 py-2.5 rounded-xl text-sm">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
 
    {{-- ===== DELETE CONFIRM MODAL ===== --}}
    <div id="deleteModal" class="hidden fixed inset-0 z-50 flex items-center justify-center modal-backdrop p-4">
        <div class="bg-[#1A1A1D] border border-gray-800/70 rounded-2xl w-full max-w-sm p-6 fade-in text-center">
            <div class="w-12 h-12 rounded-2xl bg-red-500/10 border border-red-500/20 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h2 class="font-display font-bold text-lg mb-2">Delete User?</h2>
            <p class="text-sm text-gray-400 mb-6">Are you sure you want to delete user <span id="deleteUserName" class="text-white font-semibold"></span>? You can't retrieve it again.</p>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('deleteModal').classList.add('hidden')"
                            class="flex-1 px-4 py-2.5 rounded-xl border border-gray-700/60 text-sm text-gray-400 hover:text-white hover:border-gray-600 transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex-1 bg-red-500/20 border border-red-500/30 text-red-400 hover:bg-red-500/30 px-4 py-2.5 rounded-xl text-sm font-semibold transition">
                        Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
 
    <script>
        function toggleSidebar() {
            document.querySelector('.mobile-sidebar')?.classList.toggle('open');
            document.getElementById('sidebar-overlay')?.classList.toggle('open');
        }

        function openEditModal(id, name, username, email) {
            document.getElementById('editForm').action = `/users/${id}`;
            document.getElementById('editName').value = name;
            document.getElementById('editUsername').value = username;
            document.getElementById('editEmail').value = email;
            document.getElementById('editModal').classList.remove('hidden');
        }
 
        function openDeleteModal(id, name) {
            document.getElementById('deleteForm').action = `/users/${id}`;
            document.getElementById('deleteUserName').textContent = name;
            document.getElementById('deleteModal').classList.remove('hidden');
        }
 
        ['addModal','editModal','deleteModal'].forEach(id => {
            document.getElementById(id).addEventListener('click', function(e) {
                if (e.target === this) this.classList.add('hidden');
            });
        });
    </script>
</body>
</html>