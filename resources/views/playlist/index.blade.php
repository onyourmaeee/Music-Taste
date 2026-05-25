<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Playlists — Music Taste</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800;900&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
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
 
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: #0F0F0F; }
        ::-webkit-scrollbar-thumb { background: #FF69B4; border-radius: 2px; }
 
        .modal-backdrop {
            backdrop-filter: blur(4px);
            background: rgba(0,0,0,0.6);
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
 
        .noise-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.03'/%3E%3C/svg%3E");
            pointer-events: none;
            border-radius: inherit;
            z-index: 0;
        }

        .mobile-sidebar { transform: translateX(-100%); transition: transform 0.3s ease; }
        .mobile-sidebar.open { transform: translateX(0); }
        .sidebar-overlay { opacity: 0; pointer-events: none; transition: opacity 0.3s ease; }
        .sidebar-overlay.open { opacity: 1; pointer-events: auto; }

        .active-track {
            background: rgba(255, 105, 180, 0.08) !important;
            border-left: 3px solid #FF69B4;
        }
    </style>
</head>
<body class="bg-[#0F0F0F] text-white min-h-screen flex w-full overflow-x-hidden">
 
    {{-- YouTube Player iframe (off-screen but fully opaque for Chrome audio) --}}
    <iframe id="yt-player"
            width="200" height="200"
            style="position:fixed;top:-9999px;left:-9999px;width:200px;height:200px;opacity:1;"
            frameborder="0" allow="autoplay; encrypted-media" allowfullscreen>
    </iframe>

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
    </aside>
 
    {{-- MAIN LAYOUT CONTENT --}}
    <main class="flex-1 lg:ml-64 min-h-screen w-full max-w-full flex flex-col lg:flex-row gap-6 p-6 box-border overflow-x-hidden">
        
        {{-- LEFT CONTAINER: Playlist System --}}
        <div class="flex-1 flex flex-col space-y-6 min-w-0">
            
            {{-- Mobile hamburger --}}
            <div class="lg:hidden flex items-center gap-3">
                <button onclick="toggleSidebar()" class="text-gray-400 hover:text-white p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="font-display font-black text-xl tracking-tight">Playlists</h1>
            </div>

            {{-- Alert Handlers --}}
            @if(session('success'))
                <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-xl text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Playlist Banner Card --}}
            <div class="relative noise-bg bg-[#1A1A1D] border border-gray-800/70 rounded-3xl p-6 flex flex-col md:flex-row gap-6 items-center justify-between overflow-hidden">
                <div class="flex items-center gap-5 z-10 w-full md:w-auto">
                    @if($activePlaylist)
                    <div onclick="openCoverModal()" class="relative w-24 h-24 md:w-32 md:h-32 rounded-2xl bg-gray-800 shadow-2xl flex-shrink-0 bg-cover bg-center group cursor-pointer overflow-hidden" style="background-image: url('{{ $activePlaylist->coverPhotoUrl }}');">
                        <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                    </div>
                    @else
                    <div id="banner-cover" class="w-24 h-24 md:w-32 md:h-32 rounded-2xl bg-gray-800 shadow-2xl flex-shrink-0 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1614613535308-eb5fbd3d2c17?q=80&w=300&auto=format&fit=crop');"></div>
                    @endif
                    <div class="min-w-0">
                        <span class="text-xs uppercase font-bold tracking-widest text-docupink font-display">Active Playlist</span>
                        <div class="flex items-center gap-2 mt-1">
                            <h2 id="playlist-name-display" class="font-display font-black text-2xl md:text-3xl tracking-tight text-white">
                                {{ $activePlaylist ? $activePlaylist->name : 'Pumili o Gumawa ng Playlist' }}
                            </h2>
                            @if($activePlaylist)
                            <button onclick="openRenameModal()" class="text-gray-500 hover:text-docupink transition p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            @endif
                        </div>
                        <p class="text-xs text-gray-400 mt-2">
                            <span id="track-count-badge">0 Songs</span> &bull; Audio Stream
                        </p>
                    </div>
                </div>
                
                @if($activePlaylist)
                <div class="flex gap-3 w-full md:w-auto justify-end z-10">
                    <button onclick="openAddMusicModal()" class="btn-primary text-black text-sm px-5 py-2.5 rounded-xl transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        Add Music From Songs Tab
                    </button>
                    <form action="{{ route('playlists.destroy', $activePlaylist->id) }}" method="POST" onsubmit="return confirmDeletePlaylist(event, this)">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-sm px-4 py-2.5 rounded-xl border border-red-500/40 text-red-400 hover:bg-red-500/10 transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Delete
                        </button>
                    </form>
                </div>
                @endif
            </div>

            {{-- Tracks Table Container --}}
            <div class="glow-card bg-[#1A1A1D] border border-gray-800/70 rounded-3xl p-6 flex-1 flex flex-col min-w-0">
                <h3 class="font-display font-bold text-base mb-4">Tracks Inside Playlist</h3>
                
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-sm min-w-[550px]">
                        <thead>
                            <tr class="text-left text-xs text-gray-500 border-b border-gray-800/60 uppercase font-semibold">
                                <th class="pb-3 w-12 text-center">#</th>
                                <th class="pb-3">Title</th>
                                <th class="pb-3">Artist</th>
                                <th class="pb-3">Album</th>
                                <th class="pb-3 text-right pr-4">Action</th>
                            </tr>
                        </thead>
                        <tbody id="playlist-tracks-body" class="divide-y divide-gray-800/30">
                            {{-- Dynamic Javascript Render --}}
                        </tbody>
                    </table>
                    
                    <div id="empty-state" class="hidden flex flex-col items-center justify-center py-16 text-gray-600 gap-2">
                        <svg class="w-10 h-10 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z"/></svg>
                        <p class="text-xs">Walang kanta ang playlist na ito. Magdagdag gamit ang button sa itaas!</p>
                    </div>
                </div>
            </div>

            {{-- FOOTER MEDIA CONTROLLER --}}
            <div class="bg-[#1A1A1D] border border-gray-800/80 rounded-2xl p-4 flex items-center justify-between shadow-2xl">
                <div class="flex items-center gap-3 w-1/3 min-w-0">
                    <div id="player-cover" class="w-11 h-11 rounded-xl bg-gray-800 bg-cover bg-center flex-shrink-0" style="background-image: url('https://images.unsplash.com/photo-1614613535308-eb5fbd3d2c17?q=80&w=100&auto=format&fit=crop');"></div>
                    <div class="min-w-0">
                        <p id="player-title" class="text-sm font-bold text-white truncate">Pumili ng Kanta</p>
                        <p id="player-artist" class="text-xs text-gray-400 truncate">No artist</p>
                    </div>
                </div>

                <div class="flex flex-col items-center gap-1.5 w-1/3">
                    <div class="flex items-center gap-4">
                        <button onclick="prevTrack()" class="text-gray-400 hover:text-white transition" title="Previous">
                            <svg class="w-4 h-4 fill-currentColor" viewBox="0 0 24 24"><path d="M6 6h2v12H6zm3.5 6L18 18V6z"/></svg>
                        </button>
                        <button id="player-play-btn" onclick="togglePlay()" class="w-9 h-9 rounded-full bg-white text-black flex items-center justify-center hover:scale-105 transition">
                            <svg id="play-icon" class="w-4 h-4 fill-black ml-0.5" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        </button>
                        <button onclick="nextTrack()" class="text-gray-400 hover:text-white transition" title="Next">
                            <svg class="w-4 h-4 fill-currentColor" viewBox="0 0 24 24"><path d="M7 6v12l8.5-6z"/></svg>
                        </button>
                    </div>
                    <div class="w-full flex items-center gap-2 text-[10px] text-gray-500">
                        <span id="player-current-time">0:00</span>
                        <div class="flex-1 h-1 bg-gray-800 rounded-full relative overflow-hidden cursor-pointer" onclick="seekAudio(event)">
                            <div id="player-progress-bar" class="absolute left-0 top-0 h-full bg-docupink w-0 rounded-full"></div>
                        </div>
                        <span id="player-total-duration">0:00</span>
                    </div>
                </div>

                <div class="w-1/3 flex justify-end items-center gap-3 text-gray-500">
                    <button id="loop-btn" onclick="toggleLoop()" class="p-1.5 rounded-lg transition text-gray-500 hover:text-white" title="Loop">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </button>
                    <button id="mute-btn" onclick="toggleMute()" class="p-1.5 rounded-lg transition hover:text-white" title="Mute">
                        <svg id="mute-icon" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg>
                    </button>
                    <input id="volume-slider" type="range" min="0" max="1" step="0.05" value="0.7" oninput="changeVolume(this.value)" class="w-14 accent-docupink h-1 bg-gray-800 rounded-full appearance-none cursor-pointer">
                </div>
            </div>
        </div>

        {{-- RIGHT SIDEBAR: Playlists Manager & Meta Widgets --}}
        <div class="w-full lg:w-80 flex flex-col gap-6 flex-shrink-0">
            
            {{-- Form: Create New Playlist --}}
            <div class="bg-[#1A1A1D] border border-gray-800/70 rounded-3xl p-5">
                <h4 class="font-display font-bold text-xs tracking-wide text-gray-400 uppercase mb-3">Create New Playlist</h4>
                <form action="{{ route('playlists.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="text" name="name" placeholder="Playlist Name (e.g., My Retro Hits)" required
                           class="w-full bg-[#0F0F0F] border border-gray-700/60 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-docupink transition">
                    <button type="submit" class="w-full btn-primary text-xs py-2 rounded-xl">
                        + Create Playlist
                    </button>
                </form>
            </div>

            {{-- List of Existing Playlists --}}
            <div class="bg-[#1A1A1D] border border-gray-800/70 rounded-3xl p-5 flex-1">
                <h4 class="font-display font-bold text-xs tracking-wide text-gray-400 uppercase mb-3">Your Playlists Database</h4>
                <div class="space-y-2 overflow-y-auto max-h-[250px] pr-1">
                    @forelse($playlists as $pl)
                        <a href="{{ route('playlists.index', ['playlist_id' => $pl->id]) }}" 
                           class="flex items-center justify-between p-2.5 rounded-xl border {{ $activePlaylist && $activePlaylist->id == $pl->id ? 'border-docupink bg-docupink/5' : 'border-gray-800 hover:border-gray-700 bg-[#0F0F0F]/40' }} transition">
                            <span class="text-sm font-semibold truncate max-w-[150px]">{{ $pl->name }}</span>
                            <span class="text-[10px] bg-gray-800 px-2 py-0.5 rounded-md text-gray-400">{{ $pl->songs->count() }} tracks</span>
                        </a>
                    @empty
                        <p class="text-xs text-gray-600 italic">No playlists found. Create one above!</p>
                    @endforelse
                </div>
            </div>
        </div>
    </main>

    {{-- MODAL: Add Music From Songs Database --}}
    @if($activePlaylist)
    <div id="addMusicModal" class="hidden fixed inset-0 z-50 flex items-center justify-center modal-backdrop p-4">
        <div class="bg-[#1A1A1D] border border-gray-800/80 rounded-3xl w-full max-w-md p-6 fade-in shadow-2xl">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-display font-bold text-lg">Add Track From Songs Database</h2>
                <button onclick="closeAddMusicModal()" class="text-gray-500 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <form action="{{ route('playlists.add-song', $activePlaylist->id) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Select Available Song</label>
                    <select name="song_id" class="w-full bg-[#0F0F0F] border border-gray-700/60 rounded-xl px-3 py-2.5 text-sm text-white focus:outline-none focus:border-docupink transition">
                        @foreach($songs as $song)
                            <option value="{{ $song->id }}">{{ $song->title }} — {{ $song->artist }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeAddMusicModal()" class="flex-1 px-4 py-2.5 rounded-xl border border-gray-700 text-sm text-gray-400 hover:text-white transition">Cancel</button>
                    <button type="submit" class="flex-1 btn-primary text-sm px-4 py-2.5 rounded-xl">Confirm Add</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- MODAL: Rename Playlist --}}
    @if($activePlaylist)
    <div id="renameModal" class="hidden fixed inset-0 z-50 flex items-center justify-center modal-backdrop p-4">
        <div class="bg-[#1A1A1D] border border-gray-800/80 rounded-3xl w-full max-w-md p-6 fade-in shadow-2xl">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-display font-bold text-lg">Rename Playlist</h2>
                <button onclick="closeRenameModal()" class="text-gray-500 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="space-y-4">
                <input type="text" id="rename-input" value="{{ $activePlaylist->name }}" placeholder="New playlist name"
                       class="w-full bg-[#0F0F0F] border border-gray-700/60 rounded-xl px-3 py-2.5 text-sm text-white focus:outline-none focus:border-docupink transition">
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeRenameModal()" class="flex-1 px-4 py-2.5 rounded-xl border border-gray-700 text-sm text-gray-400 hover:text-white transition">Cancel</button>
                    <button type="button" onclick="renamePlaylist()" class="flex-1 btn-primary text-sm px-4 py-2.5 rounded-xl">Save</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL: Update Cover Photo --}}
    @if($activePlaylist)
    <div id="coverModal" class="hidden fixed inset-0 z-50 flex items-center justify-center modal-backdrop p-4">
        <div class="bg-[#1A1A1D] border border-gray-800/80 rounded-3xl w-full max-w-md p-6 fade-in shadow-2xl">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-display font-bold text-lg">Update Cover Photo</h2>
                <button onclick="closeCoverModal()" class="text-gray-500 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Upload Image File</label>
                    <input type="file" id="cover-file-input" accept="image/*"
                           class="w-full bg-[#0F0F0F] border border-gray-700/60 rounded-xl px-3 py-2 text-sm text-gray-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-docupink file:text-black file:font-bold file:text-xs hover:file:bg-docupink/90 transition">
                    <button type="button" onclick="updateCoverFile()" class="mt-2 w-full btn-primary text-xs py-2 rounded-xl">Upload Cover</button>
                </div>
                <div class="border-t border-gray-800/60 pt-4">
                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Or Use Image URL</label>
                    <div class="flex gap-2">
                        <input type="url" id="cover-url-input" placeholder="https://example.com/cover.jpg"
                               class="flex-1 bg-[#0F0F0F] border border-gray-700/60 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-docupink transition">
                        <button type="button" onclick="updateCoverUrl()" class="btn-primary text-xs px-4 py-2 rounded-xl">Set</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- JAVASCRIPT LOGIC ENGINE --}}
    <script>
        var isPlaying = false;
        const currentPlaylistTracks = @json($activePlaylistSongs ?? []);
        let activePlayingTrackId = null;
        let isLooping = false;
        let isMuted = false;
        let previousVolume = 70;
        let loadedVideoId = null;
        let trackDuration = 0;
        let trackPosition = 0;
        let lastSeek = 0;
        let progressInterval = null;
        let progressStartedAt = 0;
        let progressStartPos = 0;

        function toggleSidebar() {
            document.querySelector('.mobile-sidebar')?.classList.toggle('open');
            document.getElementById('sidebar-overlay')?.classList.toggle('open');
        }

        // ─── Listen for YouTube iframe events via postMessage ────────
        window.addEventListener('message', function(event) {
            try {
                var data = JSON.parse(event.data);
                if (data.event === 'onStateChange') {
                    var state = data.info;
                    if (state === 0) { // ended
                        isPlaying = false;
                        stopProgress();
                        setPlayIcon();
                        document.getElementById('player-play-btn').classList.remove('bg-docupink');
                        document.getElementById('player-play-btn').classList.add('bg-white');
                        if (isLooping) {
                            playTrackById(activePlayingTrackId);
                        } else {
                            nextTrack();
                        }
                    } else if (state === 1) { // playing
                        isPlaying = true;
                        setPauseIcon();
                        document.getElementById('player-play-btn').classList.remove('bg-white');
                        document.getElementById('player-play-btn').classList.add('bg-docupink');
                        startProgress();
                    } else if (state === 2) { // paused
                        isPlaying = false;
                        stopProgress();
                        setPlayIcon();
                        document.getElementById('player-play-btn').classList.remove('bg-docupink');
                        document.getElementById('player-play-btn').classList.add('bg-white');
                    }
                }
                if (data.event === 'infoDelivery') {
                    var dur = data.info && data.info.duration;
                    var cur = data.info && data.info.currentTime;
                    if (dur && dur > 0) trackDuration = dur;
                    if (cur !== undefined) {
                        trackPosition = cur;
                        progressStartedAt = Date.now() / 1000;
                        progressStartPos = cur;
                    }
                    updateTimeDisplay();
                }
            } catch(e) {}
        });

        function ytCmd(func, args) {
            var f = document.getElementById('yt-player');
            if (f && f.contentWindow) {
                f.contentWindow.postMessage(JSON.stringify({ event: 'command', func: func, args: args || '' }), '*');
            }
        }

        function loadVideo(ytId) {
            loadedVideoId = ytId;
            var f = document.getElementById('yt-player');
            var url = 'https://www.youtube.com/embed/' + ytId + '?autoplay=1&controls=0&showinfo=0&rel=0&enablejsapi=1&origin=' + encodeURIComponent(location.origin);
            f.src = url;
            setTimeout(function() {
                if (f && f.contentWindow) {
                    f.contentWindow.postMessage(JSON.stringify({ event: 'listening', id: 1, channel: 'tester' }), '*');
                }
            }, 1500);
        }

        document.addEventListener("DOMContentLoaded", () => {
            renderTracksTable();
        });

        // ─── Progress & Time ───────────────────────────────────────
        function startProgress() {
            clearInterval(progressInterval);
            progressStartedAt = Date.now() / 1000;
            progressStartPos = trackPosition;
            progressInterval = setInterval(function() {
                var elapsed = (Date.now() / 1000 - progressStartedAt) + progressStartPos;
                trackPosition = elapsed;
                updateTimeDisplay();
            }, 200);
        }

        function stopProgress() {
            clearInterval(progressInterval);
            progressInterval = null;
        }

        function updateTimeDisplay() {
            document.getElementById('player-current-time').textContent = formatTime(trackPosition);
            if (trackDuration > 0) {
                document.getElementById('player-total-duration').textContent = formatTime(trackDuration);
                var pct = Math.min(trackPosition / trackDuration * 100, 100);
                document.getElementById('player-progress-bar').style.width = pct + '%';
            }
        }

        function formatTime(s) {
            if (isNaN(s) || !isFinite(s)) return '0:00';
            var m = Math.floor(s / 60);
            var sec = Math.floor(s % 60);
            return m + ':' + (sec < 10 ? '0' : '') + sec;
        }

        function seekAudio(event) {
            var bar = event.currentTarget;
            var rect = bar.getBoundingClientRect();
            var pct = (event.clientX - rect.left) / rect.width;
            var dur = trackDuration;
            if (dur > 0) {
                var seekTo = dur * pct;
                trackPosition = seekTo;
                progressStartedAt = Date.now() / 1000;
                progressStartPos = seekTo;
                ytCmd('seekTo', [seekTo, true]);
                updateTimeDisplay();
            }
        }

        // ─── Track Table Render ────────────────────────────────────
        function renderTracksTable() {
            const tbody = document.getElementById('playlist-tracks-body');
            const emptyState = document.getElementById('empty-state');
            const countBadge = document.getElementById('track-count-badge');

            tbody.innerHTML = '';
            countBadge.innerText = `${currentPlaylistTracks.length} Song${currentPlaylistTracks.length === 1 ? '' : 's'}`;

            if(currentPlaylistTracks.length === 0) {
                emptyState.classList.remove('hidden');
                return;
            } else {
                emptyState.classList.add('hidden');
            }

            currentPlaylistTracks.forEach((track, i) => {
                let trackCover = track.thumbnail_url || track.cover_image || track.cover || 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?q=80&w=200&auto=format&fit=crop';

                const tr = document.createElement('tr');
                tr.id = `track-row-${track.id}`;
                tr.className = `group cursor-pointer table-row border-b border-gray-800/20 transition ${activePlayingTrackId === track.id ? 'active-track' : ''}`;
                tr.setAttribute('onclick', `selectTrack(${track.id})`);

                tr.innerHTML = `
                    <td class="px-4 py-4 text-center text-gray-500 font-medium">${String(i + 1).padStart(2, '0')}</td>
                    <td class="px-2 py-4">
                        <div class="flex items-center gap-3">
                            <img src="${trackCover}" class="w-9 h-9 rounded-lg object-cover bg-gray-800 flex-shrink-0">
                            <span class="font-bold text-white group-hover:text-docupink transition">${track.title}</span>
                        </div>
                    </td>
                    <td class="px-2 py-4 text-gray-300 font-medium">${track.artist}</td>
                    <td class="px-2 py-4 text-gray-500">${track.album || 'Single'}</td>
                    <td class="px-4 py-4 text-right" onclick="event.stopPropagation();">
                        <button onclick="removeTrack({{ $activePlaylist ? $activePlaylist->id : 0 }}, ${track.id})" class="text-gray-600 hover:text-red-400 p-1.5 rounded-lg hover:bg-red-500/10 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        // ─── Track Selection & Playback ────────────────────────────
        function getYtId(url) {
            if (!url) return '';
            var m;
            m = url.match(/youtube\.com\/watch\?(?:.*&)?v=([a-zA-Z0-9_-]{11})/);
            if (m) return m[1];
            m = url.match(/youtu\.be\/([a-zA-Z0-9_-]{11})/);
            if (m) return m[1];
            m = url.match(/youtube\.com\/(?:embed|v|shorts|live)\/([a-zA-Z0-9_-]{11})/);
            if (m) return m[1];
            return '';
        }

        function playTrackById(id) {
            const track = currentPlaylistTracks.find(t => t.id === id);
            if (!track) return;
            var ytId = track.youtube_id || getYtId(track.youtube_url || '');
            if (ytId) loadVideo(ytId);
        }

        function selectTrack(id) {
            const track = currentPlaylistTracks.find(t => t.id === id);
            if(!track) return;

            if (activePlayingTrackId === id) {
                if (isPlaying) {
                    togglePlay();
                } else {
                    var ytId = track.youtube_id || getYtId(track.youtube_url || '');
                    if (loadedVideoId === ytId) {
                        ytCmd('playVideo');
                    } else {
                        playTrackById(id);
                    }
                }
                return;
            }

            activePlayingTrackId = id;
            trackPosition = 0;
            trackDuration = 0;
            stopProgress();

            let trackCover = track.thumbnail_url || track.cover_image || track.cover || 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?q=80&w=200&auto=format&fit=crop';

            document.querySelectorAll('#playlist-tracks-body tr').forEach(row => row.classList.remove('active-track'));
            const currentRow = document.getElementById(`track-row-${id}`);
            if(currentRow) currentRow.classList.add('active-track');

            playTrackById(id);

            document.getElementById('player-cover').style.backgroundImage = `url('${trackCover}')`;
            document.getElementById('player-title').innerText = track.title;
            document.getElementById('player-artist').innerText = track.artist;
            setPauseIcon();
        }

        function togglePlay() {
            if(!activePlayingTrackId && currentPlaylistTracks.length > 0) {
                selectTrack(currentPlaylistTracks[0].id);
                return;
            }
            if (!activePlayingTrackId) return;
            if (isPlaying) {
                ytCmd('pauseVideo');
                isPlaying = false;
                setPlayIcon();
                stopProgress();
                document.getElementById('player-play-btn').classList.remove('bg-docupink');
                document.getElementById('player-play-btn').classList.add('bg-white');
            } else {
                var track = currentPlaylistTracks.find(t => t.id === activePlayingTrackId);
                if (!track) return;
                var ytId = track.youtube_id || getYtId(track.youtube_url || '');
                if (!ytId) return;
                if (loadedVideoId === ytId) {
                    ytCmd('playVideo');
                } else {
                    loadVideo(ytId);
                }
                isPlaying = true;
                setPauseIcon();
                startProgress();
                document.getElementById('player-play-btn').classList.remove('bg-white');
                document.getElementById('player-play-btn').classList.add('bg-docupink');
            }
        }

        // ─── Track Navigation ──────────────────────────────────────
        function prevTrack() {
            const currentIndex = currentPlaylistTracks.findIndex(t => t.id === activePlayingTrackId);
            if (currentIndex > 0) {
                selectTrack(currentPlaylistTracks[currentIndex - 1].id);
            }
        }

        function nextTrack() {
            const currentIndex = currentPlaylistTracks.findIndex(t => t.id === activePlayingTrackId);
            if (currentIndex !== -1 && currentIndex < currentPlaylistTracks.length - 1) {
                selectTrack(currentPlaylistTracks[currentIndex + 1].id);
            }
        }

        // ─── Play / Pause Icon ─────────────────────────────────────
        function setPlayIcon() {
            var icon = document.getElementById('play-icon');
            if (icon) icon.innerHTML = '<path d="M8 5v14l11-7z"/>';
        }

        function setPauseIcon() {
            var icon = document.getElementById('play-icon');
            if (icon) icon.innerHTML = '<path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>';
        }

        // ─── Loop Toggle ───────────────────────────────────────────
        function toggleLoop() {
            isLooping = !isLooping;
            const btn = document.getElementById('loop-btn');
            if (isLooping) {
                btn.classList.add('text-docupink');
                btn.classList.remove('text-gray-500');
                btn.title = 'Looping On';
            } else {
                btn.classList.remove('text-docupink');
                btn.classList.add('text-gray-500');
                btn.title = 'Loop';
            }
        }

        // ─── Volume & Mute ─────────────────────────────────────────
        function toggleMute() {
            const slider = document.getElementById('volume-slider');
            const icon = document.getElementById('mute-icon');
            isMuted = !isMuted;
            if (isMuted) {
                ytCmd('mute');
                slider.value = 0;
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"/>';
                document.getElementById('mute-btn').classList.add('text-red-400');
            } else {
                ytCmd('unMute');
                ytCmd('setVolume', [previousVolume]);
                slider.value = previousVolume / 100;
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>';
                document.getElementById('mute-btn').classList.remove('text-red-400');
            }
        }

        function changeVolume(value) {
            var vol = parseFloat(value);
            var ytVol = Math.round(vol * 100);
            ytCmd('setVolume', [ytVol]);
            previousVolume = ytVol;
            if (vol > 0 && isMuted) {
                isMuted = false;
                ytCmd('unMute');
                document.getElementById('mute-btn').classList.remove('text-red-400');
                var icon = document.getElementById('mute-icon');
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>';
            }
        }

        // ─── Modal Helpers ─────────────────────────────────────────
        function openAddMusicModal() { document.getElementById('addMusicModal').classList.remove('hidden'); }
        function closeAddMusicModal() { document.getElementById('addMusicModal').classList.add('hidden'); }

        function confirmDeletePlaylist(event, form) {
            event.preventDefault();
            Swal.fire({
                title: 'Delete playlist?',
                text: 'This cannot be undone!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FF69B4',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!',
                background: '#1A1A1D',
                color: '#FFFFFF'
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
            return false;
        }

        // ─── Rename Playlist ───────────────────────────────────────
        function openRenameModal() {
            const input = document.getElementById('rename-input');
            if (input) input.value = '{{ $activePlaylist ? $activePlaylist->name : '' }}';
            document.getElementById('renameModal').classList.remove('hidden');
            setTimeout(() => input?.select(), 100);
        }
        function closeRenameModal() { document.getElementById('renameModal').classList.add('hidden'); }

        function renamePlaylist() {
            const input = document.getElementById('rename-input');
            const newName = input.value.trim();
            if (!newName) return;

            fetch('/playlists/{{ $activePlaylist ? $activePlaylist->id : 0 }}', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ name: newName, description: '' })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('playlist-name-display').textContent = newName;
                    closeRenameModal();
                }
            })
            .catch(err => console.error('Rename failed:', err));
        }

        // ─── Cover Photo ──────────────────────────────────────────
        function openCoverModal() { document.getElementById('coverModal').classList.remove('hidden'); }
        function closeCoverModal() { document.getElementById('coverModal').classList.add('hidden'); }

        function updateCoverFile() {
            const fileInput = document.getElementById('cover-file-input');
            if (!fileInput.files || !fileInput.files[0]) return;

            const formData = new FormData();
            formData.append('cover_image', fileInput.files[0]);
            formData.append('_method', 'PUT');
            formData.append('name', '{{ $activePlaylist ? addslashes($activePlaylist->name) : '' }}');

            fetch('/playlists/{{ $activePlaylist ? $activePlaylist->id : 0 }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(err => console.error('Cover upload failed:', err));
        }

        function updateCoverUrl() {
            const urlInput = document.getElementById('cover-url-input');
            const coverUrl = urlInput.value.trim();
            if (!coverUrl) return;

            fetch('/playlists/{{ $activePlaylist ? $activePlaylist->id : 0 }}', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ cover_image: coverUrl, name: '{{ $activePlaylist ? addslashes($activePlaylist->name) : '' }}' })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const coverEl = document.querySelector('[onclick="openCoverModal()"]');
                    if (coverEl) coverEl.style.backgroundImage = `url('${coverUrl}')`;
                    closeCoverModal();
                }
            })
            .catch(err => console.error('Cover update failed:', err));
        }

        // ─── Remove Track ──────────────────────────────────────────
        function removeTrack(playlistId, songId) {
            Swal.fire({
                title: 'Remove track?',
                text: 'Remove this track from the playlist?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FF69B4',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, remove it!',
                background: '#1A1A1D',
                color: '#FFFFFF'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('/playlists/' + playlistId + '/songs/' + songId, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const idx = currentPlaylistTracks.findIndex(t => t.id === songId);
                            if (idx !== -1) currentPlaylistTracks.splice(idx, 1);
                            if (activePlayingTrackId === songId) {
                                activePlayingTrackId = null;
                                document.getElementById('yt-player').src = '';
                            }
                            renderTracksTable();
                        }
                    })
                    .catch(err => console.error('Remove failed:', err));
                }
            });
        }
    </script>
</body>
</html>