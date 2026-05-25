<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Genres — MusicTaste</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800;900&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: { extend: { colors: { docudark: '#1A1A1D', docupink: '#FF69B4' } } }
        }
    </script>
    <style>
        body { font-family: 'DM Sans', sans-serif; background: #0a0a0a; }
        h1,h2,h3 { font-family: 'Syne', sans-serif; }

        .sidebar-link { transition: all 0.2s; border-left: 2px solid transparent; }
        .sidebar-link:hover, .sidebar-link.active {
            background: rgba(255,105,180,0.08);
            border-left-color: #FF69B4;
            color: #FF69B4;
        }

        /* Vinyl record */
        .vinyl {
            position: relative;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            background: radial-gradient(circle at center,
                #1a1a1a 0%, #1a1a1a 18%,
                #111 18%, #111 20%,
                #222 20%, #0a0a0a 35%,
                #1a1a1a 35%, #111 40%,
                #0a0a0a 40%, #1a1a1a 55%,
                #111 55%, #0a0a0a 70%,
                #1a1a1a 70%, #111 85%,
                #0a0a0a 85%, #1a1a1a 100%
            );
            box-shadow: 0 0 60px rgba(0,0,0,0.8), inset 0 0 30px rgba(0,0,0,0.5);
            flex-shrink: 0;
        }

        .vinyl::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: repeating-conic-gradient(
                rgba(255,255,255,0.015) 0deg,
                transparent 1deg,
                transparent 3deg
            );
        }

        .vinyl-label {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: #FF69B4;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(255,105,180,0.4);
        }

        .vinyl-hole {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #0a0a0a;
            z-index: 10;
        }

        .vinyl-shine {
            position: absolute;
            top: 15%;
            left: 15%;
            width: 30%;
            height: 30%;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
            pointer-events: none;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .vinyl.spinning { animation: spin 4s linear infinite; }

        /* Tonearm */
        .tonearm {
            position: absolute;
            top: -20px;
            right: -30px;
            width: 140px;
            height: 4px;
            background: linear-gradient(90deg, #888, #ccc, #888);
            border-radius: 2px;
            transform-origin: right center;
            transform: rotate(-25deg);
            box-shadow: 0 2px 8px rgba(0,0,0,0.5);
        }
        .tonearm::after {
            content: '';
            position: absolute;
            right: -6px;
            top: -6px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: radial-gradient(circle, #ccc, #888);
            box-shadow: 0 2px 6px rgba(0,0,0,0.5);
        }

        /* Genre card */
        .genre-card {
            transition: all 0.2s;
            cursor: pointer;
            border-left: 3px solid transparent;
        }
        .genre-card:hover { background: rgba(255,255,255,0.04); }
        .genre-card.active {
            border-left-color: #FF69B4;
            background: rgba(255,105,180,0.06);
        }

        /* Song item */
        .song-item { transition: background 0.15s; }
        .song-item:hover { background: rgba(255,255,255,0.04); }

        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: #0a0a0a; }
        ::-webkit-scrollbar-thumb { background: #FF69B4; border-radius: 2px; }

        /* Red needle line */
        .mobile-sidebar { transform: translateX(-100%); transition: transform 0.3s ease; }
        .mobile-sidebar.open { transform: translateX(0); }
        .sidebar-overlay { opacity: 0; pointer-events: none; transition: opacity 0.3s ease; }
        .sidebar-overlay.open { opacity: 1; pointer-events: auto; }

        .needle-line {
            position: absolute;
            bottom: 20px;
            left: 30px;
            width: 2px;
            height: 40px;
            background: #ef4444;
        }

        .waveform {
            display: flex;
            align-items: center;
            gap: 2px;
            height: 20px;
        }
        .waveform span {
            display: block;
            width: 3px;
            background: #FF69B4;
            border-radius: 2px;
            animation: wave 1s ease-in-out infinite;
        }
        .waveform span:nth-child(1) { height: 8px; animation-delay: 0s; }
        .waveform span:nth-child(2) { height: 16px; animation-delay: 0.1s; }
        .waveform span:nth-child(3) { height: 12px; animation-delay: 0.2s; }
        .waveform span:nth-child(4) { height: 18px; animation-delay: 0.3s; }
        .waveform span:nth-child(5) { height: 10px; animation-delay: 0.4s; }
        @keyframes wave {
            0%, 100% { transform: scaleY(1); }
            50% { transform: scaleY(0.4); }
        }
    </style>
</head>
<body class="text-white min-h-screen flex">

    {{-- SIDEBAR --}}
    <div id="sidebar-overlay" class="sidebar-overlay fixed inset-0 bg-black/60 z-20 lg:hidden" onclick="toggleSidebar()"></div>
    <aside class="mobile-sidebar w-64 flex-col bg-[#111113] border-r border-gray-800/60 min-h-screen fixed left-0 top-0 z-30 lg:flex lg:!translate-x-0">
        <div class="px-6 py-7 border-b border-gray-800/60 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-docupink flex items-center justify-center text-black font-black text-lg">♫</div>
            <span class="font-black text-lg tracking-tight">Music<span class="text-docupink">Taste</span></span>
        </div>
        <nav class="flex-1 px-3 py-6 space-y-1">
            <p class="text-[10px] text-gray-600 uppercase tracking-widest font-semibold px-3 mb-3">Main</p>
            <a href="{{ route('dashboard') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>
            <a href="{{ route('users.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Users
            </a>
            <a href="{{ route('songs.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                Songs
            </a>
            <a href="{{ route('genres.index') }}" class="sidebar-link active flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                Genres
            </a>
            <a href="{{ route('playlists.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Playlists
            </a>
            <p class="text-[10px] text-gray-600 uppercase tracking-widest font-semibold px-3 mb-3 mt-6">Account</p>
            <a href="{{ route('settings') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('settings') ? 'active' : 'text-gray-400' }}">
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
                <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email ?? '' }}</p>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="text-gray-600 hover:text-docupink transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>
        </div>
    </aside>

    {{-- MAIN --}}
    <main class="flex-1 lg:ml-64 min-h-screen flex flex-col">

        {{-- Header --}}
        <header class="sticky top-0 z-20 bg-[#0a0a0a]/80 backdrop-blur-md border-b border-gray-800/40 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="lg:hidden text-gray-400 hover:text-white p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="text-xs text-gray-600 font-bold tracking-widest">S/M</div>
            </div>
            <nav class="flex items-center gap-8 text-xs font-bold tracking-widest text-gray-500">
                <span class="text-white">GENRES</span>
                <a href="{{ route('songs.index') }}" class="hover:text-white transition">SONGS</a>
                <a href="{{ route('playlists.index') }}" class="hover:text-white transition">PLAYLISTS</a>

                <button onclick="document.getElementById('addModal').classList.remove('hidden')"
                        class="bg-docupink text-black px-4 py-1.5 rounded-full hover:shadow-[0_0_15px_rgba(255,105,180,0.4)] transition">
                    + ADD
                </button>
            </nav>
        </header>

        {{-- Hero Section --}}
        @if($featured)
        <div class="flex flex-col lg:flex-row min-h-[420px] border-b border-gray-800/40" id="hero-section">

            {{-- Vinyl Side --}}
            <div class="w-full lg:w-2/5 bg-[#0d0d0d] flex items-center justify-center p-12 relative overflow-hidden">
                <div class="absolute inset-0 opacity-5"
                     style="background: radial-gradient(circle at 30% 50%, #FF69B4 0%, transparent 60%)"></div>

                {{-- Vinyl --}}
                <div class="relative" id="vinyl-wrap">
                    <div class="vinyl" id="main-vinyl">
                        <div class="vinyl-label" id="vinyl-label">
                            @if($featured->image)
                            <img src="{{ asset('storage/' . $featured->image) }}" class="w-full h-full object-cover" alt="">
                            @else
                            <span class="text-black font-black text-2xl">♫</span>
                            @endif
                        </div>
                        <div class="vinyl-hole"></div>
                        <div class="vinyl-shine"></div>
                    </div>
                    <div class="tonearm"></div>
                </div>

                {{-- Now playing info --}}
                <div class="absolute bottom-6 left-8 flex items-center gap-3">
                    <div class="waveform" id="waveform">
                        <span></span><span></span><span></span><span></span><span></span>
                    </div>
                    <span class="text-xs text-gray-500 font-bold tracking-widest uppercase" id="now-playing-label">{{ strtoupper($featured->name) }}</span>
                </div>
            </div>

            {{-- Info Side --}}
            <div class="flex-1 p-10 lg:p-16 flex flex-col justify-center relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 opacity-5 rounded-full"
                     style="background: radial-gradient(circle, #FF69B4, transparent); transform: translate(30%, -30%)"></div>

                <p class="text-xs text-gray-600 font-bold tracking-widest mb-2 uppercase" id="genre-tags">Genre</p>
                <h1 class="text-6xl lg:text-8xl font-black tracking-tighter leading-none mb-4 uppercase" id="featured-name">
                    {{ $featured->name }}
                </h1>
                <p class="text-gray-500 text-sm mb-8 max-w-md" id="featured-desc">
                    {{ $featured->description ?? 'No description available.' }}
                </p>

                <p class="text-xs text-gray-600 font-bold tracking-widest uppercase mb-4">Popular</p>
                <div class="space-y-1" id="popular-list">
                    <div class="text-gray-600 text-sm italic">Add songs to this genre to see them here.</div>
                </div>
            </div>
        </div>
        @endif

        {{-- Genre Grid --}}
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-black text-xl tracking-tight">All Genres <span class="text-gray-600 font-normal text-base">({{ $genres->count() }})</span></h2>
            </div>

            @if($genres->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($genres as $genre)
                <div class="genre-card group relative rounded-2xl overflow-hidden bg-[#111113] border border-gray-800/60 p-5 {{ $loop->first ? 'active' : '' }}"
                     onclick="selectGenre({{ $genre->id }}, '{{ addslashes($genre->name) }}', '{{ addslashes($genre->description ?? '') }}', '{{ $genre->image ? asset('storage/' . $genre->image) : '' }}')">

                    {{-- Background image --}}
                    @if($genre->image)
                    <div class="absolute inset-0 opacity-10 group-hover:opacity-20 transition"
                         style="background: url('{{ asset('storage/' . $genre->image) }}') center/cover"></div>
                    @endif

                    <div class="relative z-10">
                        {{-- Mini vinyl --}}
                        <div class="w-14 h-14 rounded-full mb-4 flex items-center justify-center overflow-hidden flex-shrink-0"
                             style="background: radial-gradient(circle at center, #1a1a1a 0%, #1a1a1a 25%, #111 25%, #0a0a0a 40%, #1a1a1a 40%, #111 60%, #0a0a0a 60%, #1a1a1a 80%, #111 100%)">
                            @if($genre->image)
                            <img src="{{ asset('storage/' . $genre->image) }}" class="w-6 h-6 rounded-full object-cover" alt="">
                            @else
                            <div class="w-6 h-6 rounded-full bg-docupink flex items-center justify-center">
                                <span class="text-black text-xs font-black">♫</span>
                            </div>
                            @endif
                        </div>

                        <h3 class="font-black text-lg tracking-tight leading-tight mb-1">{{ $genre->name }}</h3>
                        <p class="text-gray-500 text-xs line-clamp-2">{{ $genre->description ?? 'No description.' }}</p>

                        <div class="flex items-center justify-between mt-4">
                            <span class="text-[10px] text-gray-600 uppercase tracking-widest">Genre</span>
                            <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition">
                                <button onclick="event.stopPropagation(); openEdit({{ $genre->id }}, '{{ addslashes($genre->name) }}', '{{ addslashes($genre->description ?? '') }}')"
                                        class="p-1.5 text-gray-600 hover:text-docupink rounded-lg hover:bg-docupink/10 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <form action="{{ route('genres.destroy', $genre->id) }}" method="POST" onsubmit="return confirmDeleteGenre(event, this)" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="p-1.5 text-gray-600 hover:text-red-400 rounded-lg hover:bg-red-500/10 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-24 text-gray-600">
                <div class="text-6xl mb-4 opacity-20">♫</div>
                <p class="text-lg font-semibold">No genres yet.</p>
                <p class="text-sm mt-1">Add your first genre to get started.</p>
            </div>
            @endif
        </div>
    </main>

    {{-- ADD MODAL --}}
    <div id="addModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-[#111113] border border-gray-800 rounded-2xl w-full max-w-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="font-black text-lg">Add Genre</h2>
                <button onclick="document.getElementById('addModal').classList.add('hidden')" class="text-gray-500 hover:text-white">✕</button>
            </div>
            <form action="{{ route('genres.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="text" name="name" placeholder="Genre Name" required
                       class="w-full px-4 py-3 bg-[#1a1a1d] border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-docupink focus:outline-none">
                <textarea name="description" placeholder="Description (optional)" rows="3"
                          class="w-full px-4 py-3 bg-[#1a1a1d] border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-docupink focus:outline-none resize-none"></textarea>
                <div>
                    <label class="text-xs text-gray-500 mb-1 block uppercase tracking-widest">Genre Image (optional)</label>
                    <input type="file" name="image" accept="image/*"
                           class="w-full px-4 py-3 bg-[#1a1a1d] border border-gray-700 rounded-xl text-white text-sm focus:border-docupink focus:outline-none file:bg-docupink file:text-black file:border-0 file:rounded-lg file:px-3 file:py-1 file:text-xs file:font-bold file:mr-3">
                </div>
                <button type="submit" class="w-full bg-docupink text-black font-black py-3 rounded-full uppercase tracking-widest text-sm hover:shadow-[0_0_20px_rgba(255,105,180,0.4)] transition">
                    Add Genre
                </button>
            </form>
        </div>
    </div>

    {{-- EDIT MODAL --}}
    <div id="editModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-[#111113] border border-gray-800 rounded-2xl w-full max-w-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="font-black text-lg">Edit Genre</h2>
                <button onclick="document.getElementById('editModal').classList.add('hidden')" class="text-gray-500 hover:text-white">✕</button>
            </div>
            <form id="editForm" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf @method('PUT')
                <input type="text" name="name" id="editName" placeholder="Genre Name" required
                       class="w-full px-4 py-3 bg-[#1a1a1d] border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-docupink focus:outline-none">
                <textarea name="description" id="editDesc" placeholder="Description (optional)" rows="3"
                          class="w-full px-4 py-3 bg-[#1a1a1d] border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-docupink focus:outline-none resize-none"></textarea>
                <div>
                    <label class="text-xs text-gray-500 mb-1 block uppercase tracking-widest">New Image (optional)</label>
                    <input type="file" name="image" accept="image/*"
                           class="w-full px-4 py-3 bg-[#1a1a1d] border border-gray-700 rounded-xl text-white text-sm focus:border-docupink focus:outline-none file:bg-docupink file:text-black file:border-0 file:rounded-lg file:px-3 file:py-1 file:text-xs file:font-bold file:mr-3">
                </div>
                <button type="submit" class="w-full bg-docupink text-black font-black py-3 rounded-full uppercase tracking-widest text-sm hover:shadow-[0_0_20px_rgba(255,105,180,0.4)] transition">
                    Update Genre
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
    <script>
        Swal.fire({ toast: true, position: 'top-end', icon: 'success', iconColor: '#FF69B4',
            title: '{{ session("success") }}', showConfirmButton: false, timer: 3500,
            timerProgressBar: true, background: '#1A1A1D', color: '#FFFFFF' });
    </script>
    @endif

    @if($errors->any())
    <script>
        Swal.fire({ toast: true, position: 'top-end', icon: 'error', iconColor: '#ef4444',
            title: '{{ $errors->first() }}', showConfirmButton: false, timer: 4000,
            timerProgressBar: true, background: '#1A1A1D', color: '#FFFFFF' });
    </script>
    @endif

    <script>
        function toggleSidebar() {
            document.querySelector('.mobile-sidebar')?.classList.toggle('open');
            document.getElementById('sidebar-overlay')?.classList.toggle('open');
        }
        function confirmDeleteGenre(event, form) {
            event.preventDefault();
            Swal.fire({
                title: 'Delete genre?', text: 'This cannot be undone!', icon: 'warning',
                showCancelButton: true, confirmButtonColor: '#FF69B4', cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!', background: '#1A1A1D', color: '#FFFFFF'
            }).then((r) => { if (r.isConfirmed) form.submit(); });
            return false;
        }

        function selectGenre(id, name, desc, image) {
            document.getElementById('featured-name').textContent = name;
            document.getElementById('featured-desc').textContent = desc || 'No description available.';
            document.getElementById('now-playing-label').textContent = name.toUpperCase();

            var label = document.getElementById('vinyl-label');
            if (image) {
                label.innerHTML = '<img src="' + image + '" class="w-full h-full object-cover" alt="">';
            } else {
                label.innerHTML = '<span class="text-black font-black text-2xl">♫</span>';
            }

            var vinyl = document.getElementById('main-vinyl');
            vinyl.classList.add('spinning');
            setTimeout(() => vinyl.classList.remove('spinning'), 3000);

            document.querySelectorAll('.genre-card').forEach(c => c.classList.remove('active'));
            event.currentTarget.classList.add('active');
        }

        function openEdit(id, name, desc) {
            document.getElementById('editForm').action = '/genres/' + id;
            document.getElementById('editName').value = name;
            document.getElementById('editDesc').value = desc;
            document.getElementById('editModal').classList.remove('hidden');
        }
    </script>
</body>
</html>