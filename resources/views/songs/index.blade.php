<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Songs — MusicTaste</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800;900&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: { extend: { colors: { docudark: '#1A1A1D', docupink: '#FF69B4' } } }
        }
    </script>
    <style>
        body { font-family: 'DM Sans', sans-serif; background: #0F0F0F; }
        h1,h2,h3 { font-family: 'Syne', sans-serif; }

        .sidebar-link { transition: all 0.2s; border-left: 2px solid transparent; }
        .sidebar-link:hover, .sidebar-link.active {
            background: rgba(255,105,180,0.08);
            border-left-color: #FF69B4;
            color: #FF69B4;
        }

        .song-row { transition: background 0.2s; cursor: pointer; }
        .song-row:hover { background: rgba(255,255,255,0.05); }
        .song-row.playing { background: rgba(255,105,180,0.08); }

        /* Player bar */
        #player-bar {
            background: linear-gradient(135deg, #1a1a1d 0%, #2d1b33 100%);
            border-top: 1px solid rgba(255,105,180,0.2);
        }

        /* Progress bar */
        #progress-bar { cursor: pointer; }
        #progress-fill { transition: width 0.1s linear; background: linear-gradient(90deg, #FF69B4, #ff9dd1); }

        /* Thumbnail shimmer */
        .thumb-wrap { position: relative; overflow: hidden; border-radius: 12px; }
        .thumb-wrap::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,105,180,0.2), transparent);
            pointer-events: none;
        }

        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: #0F0F0F; }
        ::-webkit-scrollbar-thumb { background: #FF69B4; border-radius: 2px; }

        @keyframes spin-slow { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        .spinning { animation: spin-slow 4s linear infinite; }

        .genre-badge {
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 999px;
            background: rgba(255,105,180,0.1);
            border: 1px solid rgba(255,105,180,0.2);
            color: #FF69B4;
        }

        /* Mobile sidebar */
        .mobile-sidebar { transform: translateX(-100%); transition: transform 0.3s ease; }
        .mobile-sidebar.open { transform: translateX(0); }
        .sidebar-overlay { opacity: 0; pointer-events: none; transition: opacity 0.3s ease; }
        .sidebar-overlay.open { opacity: 1; pointer-events: auto; }

        /* Genre tabs */
        .genre-tab {
            transition: all 0.2s;
            white-space: nowrap;
        }
        .genre-tab.active {
            background: rgba(255,105,180,0.15);
            border-color: #FF69B4;
            color: #FF69B4;
        }
    </style>
</head>
<body class="text-white min-h-screen flex flex-col">

{{-- YouTube Player iframe (off-screen but fully opaque for Chrome audio) --}}
<iframe id="yt-player"
        width="200" height="200"
        style="position:fixed;top:-9999px;left:-9999px;width:200px;height:200px;opacity:1;"
        frameborder="0" allow="autoplay; encrypted-media" allowfullscreen>
</iframe>
<script>
    var currentSongIndex = -1, isPlaying = false;
    var loadedVideoId = null;
    var songs = [];
    @foreach($songs as $s)
    songs.push({
        id:     {{ $s->id }},
        title:  @json($s->title),
        artist: @json($s->artist),
        genre:  @json($s->genre),
        album:  @json($s->album),
        yt_id:  @json($s->youtube_id) || '',
        url:    @json($s->youtube_url) || '',
        thumb:  @json($s->thumbnail_url) || '',
    });
    @endforeach

    var userPlaylists = @json($playlists->map(fn($p) => ['id' => $p->id, 'name' => $p->name]));

    // ─── Listen for YouTube iframe events via postMessage ────────
    window.addEventListener('message', function(event) {
        try {
            var data = JSON.parse(event.data);
            if (data.event === 'onStateChange') {
                var state = data.info;
                if (state === 0) { // ended
                    isPlaying = false;
                    stopProgress();
                    updatePlayBtn(false);
                    playNext();
                } else if (state === 1) { // playing
                    isPlaying = true;
                    updatePlayBtn(true);
                    startProgress();
                } else if (state === 2) { // paused
                    isPlaying = false;
                    stopProgress();
                    updatePlayBtn(false);
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
        }, 1000);
    }

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

    function playSong(index) {
        if (!songs[index]) return;
        currentSongIndex = index;
        trackPosition = 0;
        trackDuration = 0;
        stopProgress();
        var s = songs[index];

        var ytId = s.yt_id || getYtId(s.url || '');
        if (!ytId) { alert('No YouTube ID found'); return; }

        document.querySelectorAll('.song-row').forEach((r, i) => {
            r.classList.toggle('playing', i === index);
            var num = r.querySelector('.song-num');
            var icon = r.querySelector('.play-icon');
            if (num) num.classList.toggle('hidden', i === index);
            if (icon) icon.classList.toggle('hidden', i !== index);
        });

        document.getElementById('player-title').textContent = s.title;
        document.getElementById('player-artist').textContent = s.artist;
        document.getElementById('player-thumb').src = s.thumb || 'https://via.placeholder.com/56x56/1a1a1d/FF69B4?text=♫';
        document.getElementById('player-bar').classList.remove('hidden');
        document.getElementById('disc-art').src = s.thumb || 'https://via.placeholder.com/56x56/1a1a1d/FF69B4?text=♫';

        loadVideo(ytId);
        isPlaying = true;
        updatePlayBtn(true);
    }

    function togglePlay() {
        if (isPlaying) {
            ytCmd('pauseVideo');
            isPlaying = false;
            updatePlayBtn(false);
            stopProgress();
        } else if (currentSongIndex >= 0 && songs[currentSongIndex]) {
            var ytId = songs[currentSongIndex].yt_id || getYtId(songs[currentSongIndex].url || '');
            if (!ytId) return;
            if (loadedVideoId === ytId) {
                ytCmd('playVideo');
            } else {
                loadVideo(ytId);
            }
            isPlaying = true;
            updatePlayBtn(true);
            startProgress();
        }
    }

    function playNext() {
        if (currentSongIndex < songs.length - 1) playSong(currentSongIndex + 1);
    }

    function playPrev() {
        if (currentSongIndex > 0) playSong(currentSongIndex - 1);
    }

    function updatePlayBtn(playing) {
        var btn = document.getElementById('play-btn');
        if (!btn) return;
        btn.innerHTML = playing
            ? `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 4h4v16H6zm8 0h4v16h-4z"/></svg>`
            : `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>`;
        var disc = document.getElementById('disc-art');
        if (disc) disc.classList.toggle('spinning', playing);
    }

    var trackDuration = 0, trackPosition = 0, progressStartedAt = 0, progressStartPos = 0, progressInterval = null;

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
        document.getElementById('time-current').textContent = formatTime(trackPosition);
        if (trackDuration > 0) {
            document.getElementById('time-total').textContent = formatTime(trackDuration);
            var pct = Math.min(trackPosition / trackDuration * 100, 100);
            document.getElementById('progress-fill').style.width = pct + '%';
        }
    }

    function seekTo(e) {
        var bar = document.getElementById('progress-bar');
        var rect = bar.getBoundingClientRect();
        var pct = (e.clientX - rect.left) / rect.width;
        var dur = trackDuration;
        if (dur > 0) {
            var seekPos = dur * pct;
            trackPosition = seekPos;
            progressStartedAt = Date.now() / 1000;
            progressStartPos = seekPos;
            ytCmd('seekTo', [seekPos, true]);
            updateTimeDisplay();
        }
    }

    function setVolume(val) {
        ytCmd('setVolume', [val]);
    }

    function confirmDeleteSong(event, form) {
        event.preventDefault();
        Swal.fire({
            title: 'Delete song?', text: 'This cannot be undone!', icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#FF69B4', cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!', background: '#1A1A1D', color: '#FFFFFF'
        }).then((r) => { if (r.isConfirmed) form.submit(); });
        return false;
    }

    function formatTime(s) {
        var m = Math.floor(s / 60);
        var sec = Math.floor(s % 60);
        return m + ':' + (sec < 10 ? '0' : '') + sec;
    }
</script>

<div class="flex flex-1">

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
    <main class="flex-1 lg:ml-64 flex flex-col pb-24">

        {{-- Header --}}
        <header class="sticky top-0 z-20 bg-[#0F0F0F]/80 backdrop-blur-md border-b border-gray-800/60 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="lg:hidden text-gray-400 hover:text-white p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    <h1 class="font-black text-xl tracking-tight">Songs</h1>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $songs->count() }} songs in your library</p>
                </div>
            </div>
            <button onclick="openAddModal()"
                    class="bg-docupink text-black text-sm font-black px-5 py-2 rounded-full hover:shadow-[0_0_20px_rgba(255,105,180,0.4)] transition">
                + Add Song
            </button>
        </header>

        {{-- Content --}}
        <div class="flex-1 p-6 space-y-8">

            {{-- Featured / Top Song --}}
            @if($songs->count() > 0)
            @php $featured = $songs->first(); @endphp
            <div class="relative rounded-2xl overflow-hidden h-52"
                 style="background: linear-gradient(135deg, #2d1b33 0%, #1a1a1d 50%, #1a1f3a 100%);">
                <div class="absolute inset-0 opacity-20"
                     style="background-image: url('{{ $featured->thumbnail_url }}'); background-size: cover; background-position: center; filter: blur(20px);"></div>
                <div class="relative z-10 h-full flex items-center gap-8 px-8">
                    <div class="thumb-wrap w-32 h-32 flex-shrink-0 shadow-2xl">
                        <img src="{{ $featured->thumbnail_url ?? 'https://via.placeholder.com/128/1a1a1d/FF69B4?text=♫' }}"
                             class="w-full h-full object-cover" alt="thumb">
                    </div>
                    <div>
                        <p class="text-xs text-docupink uppercase tracking-widest font-semibold mb-1">Playlist</p>
                        <h2 class="text-4xl font-black leading-none mb-1">{{ $featured->title }}</h2>
                        <p class="text-gray-400 text-sm mb-4">{{ $featured->artist }}{{ $featured->album ? ' · ' . $featured->album : '' }}</p>
                        <div class="flex gap-3">
                            <button onclick="playSong(0)"
                                    class="flex items-center gap-2 bg-docupink text-black font-black px-6 py-2.5 rounded-full text-sm hover:shadow-[0_0_20px_rgba(255,105,180,0.5)] transition">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                Play
                            </button>
                             <a href="{{ route('playlists.index') }}"
                                class="flex items-center gap-2 bg-white/10 border border-white/20 text-white font-semibold px-5 py-2.5 rounded-full text-sm hover:bg-white/20 transition">
                                 View Playlist
                             </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Song List --}}
            <div>
                {{-- Genre Tabs --}}
                <div id="genre-tabs" class="flex items-center gap-2 mb-5 flex-wrap">
                    <button data-genre="all"
                            class="genre-tab active px-4 py-1.5 rounded-full text-sm font-semibold border border-docupink text-docupink bg-docupink/15 transition">
                        All
                    </button>
                    {{-- Dynamically injected by JS --}}
                </div>

                <div class="flex items-center justify-between mb-4">
                    <h3 id="songs-count-label" class="font-black text-lg">Global Top {{ $songs->count() }}</h3>
                </div>

                <div class="bg-[#1A1A1D] border border-gray-800/70 rounded-2xl overflow-hidden">
                    {{-- Table Header --}}
                    <div class="grid grid-cols-12 px-6 py-3 border-b border-gray-800/60 text-gray-600 text-xs uppercase tracking-widest">
                        <div class="col-span-1">#</div>
                        <div class="col-span-4">Name Song</div>
                        <div class="col-span-3">Artist</div>
                        <div class="col-span-2">Album</div>
                        <div class="col-span-1">Genre</div>
                        <div class="col-span-1 text-right">•••</div>
                    </div>

                    <div id="songs-tbody">
                        @forelse($songs as $i => $song)
                        <div class="song-row grid grid-cols-12 px-6 py-3 border-b border-gray-800/20 items-center"
                             onclick="playSong({{ $i }})">
                            <div class="col-span-1 text-gray-500 text-sm relative w-6">
                                <span class="song-num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                <svg class="play-icon hidden w-4 h-4 text-docupink absolute top-0 left-0" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            </div>
                            <div class="col-span-4 flex items-center gap-3">
                                <div class="thumb-wrap w-10 h-10 flex-shrink-0">
                                    <img src="{{ $song->thumbnail_url ?? 'https://via.placeholder.com/40/252529/FF69B4?text=♫' }}"
                                         class="w-full h-full object-cover" alt="thumb">
                                </div>
                                <span class="text-sm font-medium truncate">{{ $song->title }}</span>
                            </div>
                            <div class="col-span-3 text-gray-400 text-sm truncate">{{ $song->artist }}</div>
                            <div class="col-span-2 text-gray-500 text-sm truncate">{{ $song->album ?? '—' }}</div>
                            <div class="col-span-1">
                                @if($song->genre)
                                <span class="genre-badge">{{ $song->genre }}</span>
                                @else
                                <span class="text-gray-600 text-xs">—</span>
                                @endif
                            </div>
                            <div class="col-span-1 flex justify-end gap-1" onclick="event.stopPropagation()">
                                <div class="relative add-to-playlist-wrapper">
                                    <button onclick="togglePlaylistDropdown(event, {{ $song->id }})"
                                            class="p-1.5 text-gray-600 hover:text-docupink transition rounded-lg hover:bg-docupink/10"
                                            title="Add to Playlist">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    </button>
                                    <div id="pl-dropdown-{{ $song->id }}" class="hidden absolute right-0 top-full mt-1 w-48 bg-[#1A1A1D] border border-gray-700 rounded-xl shadow-2xl z-50 py-1 max-h-48 overflow-y-auto"></div>
                                </div>
                                <button onclick="openEdit({{ $song->id }}, '{{ addslashes($song->title) }}', '{{ addslashes($song->artist) }}', '{{ addslashes($song->genre) }}', '{{ addslashes($song->album) }}', '{{ $song->youtube_url }}')"
                                        class="p-1.5 text-gray-600 hover:text-docupink transition rounded-lg hover:bg-docupink/10">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <form action="{{ route('songs.destroy', $song->id) }}" method="POST" onsubmit="return confirmDeleteSong(event, this)">
                                    @csrf @method('DELETE')
                                    <button class="p-1.5 text-gray-600 hover:text-red-400 transition rounded-lg hover:bg-red-500/10">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @empty
                        <div class="px-6 py-16 text-center text-gray-600">
                            <div class="text-5xl mb-3 opacity-30">♫</div>
                            <p>No songs yet. Add your first song!</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

{{-- PLAYER BAR --}}
<div id="player-bar" class="hidden fixed bottom-0 left-0 right-0 z-40 px-6 py-3">
    <div class="flex items-center gap-4 max-w-screen-xl mx-auto">

        {{-- Song Info --}}
        <div class="flex items-center gap-3 w-64 flex-shrink-0">
            <img id="disc-art" src="" class="w-12 h-12 rounded-lg object-cover" alt="art">
            <div class="min-w-0">
                <p id="player-title" class="text-sm font-semibold truncate"></p>
                <p id="player-artist" class="text-xs text-gray-500 truncate"></p>
            </div>
            <img id="player-thumb" src="" class="hidden">
        </div>

        {{-- Controls --}}
        <div class="flex-1 flex flex-col items-center gap-1">
            <div class="flex items-center gap-4">
                <button onclick="playPrev()" class="text-gray-500 hover:text-white transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 6h2v12H6zm3.5 6l8.5 6V6z"/></svg>
                </button>
                <button id="play-btn" onclick="togglePlay()"
                        class="w-10 h-10 bg-docupink rounded-full flex items-center justify-center text-black hover:scale-105 transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                </button>
                <button onclick="playNext()" class="text-gray-500 hover:text-white transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 18l8.5-6L6 6v12zM16 6v12h2V6h-2z"/></svg>
                </button>
            </div>
            <div class="flex items-center gap-2 w-full max-w-md">
                <span id="time-current" class="text-xs text-gray-500 w-8 text-right">0:00</span>
                <div id="progress-bar" onclick="seekTo(event)" class="flex-1 h-1.5 bg-gray-700 rounded-full overflow-hidden">
                    <div id="progress-fill" class="h-full w-0 rounded-full"></div>
                </div>
                <span id="time-total" class="text-xs text-gray-500 w-8">0:00</span>
            </div>
        </div>

        {{-- Volume --}}
        <div class="flex items-center gap-2 w-32 flex-shrink-0 justify-end">
            <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 24 24"><path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02z"/></svg>
            <input type="range" min="0" max="100" value="80" oninput="setVolume(this.value)"
                   class="w-20 accent-docupink cursor-pointer">
        </div>
    </div>
</div>

{{-- ADD MODAL --}}
<div id="addModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-[#1A1A1D] border border-gray-800 rounded-2xl w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="font-black text-lg">Add Song</h2>
            <button onclick="document.getElementById('addModal').classList.add('hidden')" class="text-gray-500 hover:text-white">✕</button>
        </div>
        <form action="{{ route('songs.store') }}" method="POST" class="space-y-4" autocomplete="off">
            @csrf
            <input type="text" name="title" placeholder="Song Title" required
                   class="w-full px-4 py-3 bg-[#252529] border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-docupink focus:outline-none">
            <input type="text" name="artist" placeholder="Artist" required
                   class="w-full px-4 py-3 bg-[#252529] border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-docupink focus:outline-none">
            <input type="text" name="genre" placeholder="Genre (optional)"
                   class="w-full px-4 py-3 bg-[#252529] border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-docupink focus:outline-none">
            <input type="text" name="album" placeholder="Album (optional)"
                   class="w-full px-4 py-3 bg-[#252529] border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-docupink focus:outline-none">
            <input type="url" name="youtube_url" placeholder="YouTube URL (e.g. https://youtube.com/watch?v=...)" required
                   class="w-full px-4 py-3 bg-[#252529] border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-docupink focus:outline-none">
            <button type="submit" class="w-full bg-docupink text-black font-black py-3 rounded-full uppercase tracking-widest text-sm hover:shadow-[0_0_20px_rgba(255,105,180,0.4)] transition">
                Add Song
            </button>
        </form>
    </div>
</div>

{{-- EDIT MODAL --}}
<div id="editModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-[#1A1A1D] border border-gray-800 rounded-2xl w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="font-black text-lg">Edit Song</h2>
            <button onclick="document.getElementById('editModal').classList.add('hidden')" class="text-gray-500 hover:text-white">✕</button>
        </div>
        <form id="editForm" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <input type="text" name="title" id="editTitle" placeholder="Song Title" required
                   class="w-full px-4 py-3 bg-[#252529] border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-docupink focus:outline-none">
            <input type="text" name="artist" id="editArtist" placeholder="Artist" required
                   class="w-full px-4 py-3 bg-[#252529] border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-docupink focus:outline-none">
            <input type="text" name="genre" id="editGenre" placeholder="Genre (optional)"
                   class="w-full px-4 py-3 bg-[#252529] border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-docupink focus:outline-none">
            <input type="text" name="album" id="editAlbum" placeholder="Album (optional)"
                   class="w-full px-4 py-3 bg-[#252529] border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-docupink focus:outline-none">
            <input type="url" name="youtube_url" id="editUrl" placeholder="YouTube URL" required
                   class="w-full px-4 py-3 bg-[#252529] border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-docupink focus:outline-none">
            <button type="submit" class="w-full bg-docupink text-black font-black py-3 rounded-full uppercase tracking-widest text-sm hover:shadow-[0_0_20px_rgba(255,105,180,0.4)] transition">
                Update Song
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

    function openAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
        document.querySelectorAll('#addModal form input').forEach(function(el) {
            if (el.type !== 'hidden') el.value = '';
        });
    }

    function openEdit(id, title, artist, genre, album, url) {
        document.getElementById('editForm').action = '/songs/' + id;
        document.getElementById('editTitle').value = title;
        document.getElementById('editArtist').value = artist;
        document.getElementById('editGenre').value = genre;
        document.getElementById('editAlbum').value = album;
        document.getElementById('editUrl').value = url;
        document.getElementById('editModal').classList.remove('hidden');
    }

    // ─── Genre Tabs ────────────────────────────────────────────────
    const genreTabsContainer = document.getElementById('genre-tabs');

    function setActiveTab(genre) {
        document.querySelectorAll('.genre-tab').forEach(t => {
            const isActive = t.dataset.genre === genre;
            t.classList.toggle('active', isActive);
            t.classList.toggle('border-docupink', isActive);
            t.classList.toggle('text-docupink', isActive);
            t.classList.toggle('bg-docupink/15', isActive);
            t.classList.toggle('border-gray-700', !isActive);
            t.classList.toggle('text-gray-400', !isActive);
        });
    }

    function loadGenreTabs() {
        fetch('{{ route("songs.genres") }}', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(genres => {
            // Remove old dynamic tabs, keep "All"
            genreTabsContainer.querySelectorAll('.genre-tab:not([data-genre="all"])').forEach(t => t.remove());

            genres.forEach(genre => {
                const btn = document.createElement('button');
                btn.dataset.genre = genre;
                btn.className = 'genre-tab px-4 py-1.5 rounded-full text-sm font-semibold border border-gray-700 text-gray-400 transition hover:border-docupink hover:text-docupink';
                btn.textContent = genre;
                btn.addEventListener('click', () => filterSongs(genre));
                genreTabsContainer.appendChild(btn);
            });
        })
        .catch(err => console.error('Failed to load genres:', err));
    }

    function filterSongs(genre) {
        setActiveTab(genre);

        const url = genre === 'all'
            ? '{{ route("songs.index") }}'
            : `{{ route("songs.index") }}?genre=${encodeURIComponent(genre)}`;

        fetch(url, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            const songsArray = Array.isArray(data) ? data : (data.songs || []);
            if (data.playlists) userPlaylists = data.playlists;
            renderSongs(songsArray);
        })
        .catch(err => console.error('Failed to filter songs:', err));
    }

    function renderSongs(data) {
        const tbody = document.getElementById('songs-tbody');
        document.getElementById('songs-count-label').textContent = `Global Top ${data.length}`;

        // Sync JS songs array para gumana pa rin ang player
        songs = data.map(s => ({
            id:     s.id,
            title:  s.title,
            artist: s.artist,
            genre:  s.genre,
            album:  s.album,
            yt_id:  s.youtube_id,
            url:    s.youtube_url,
            thumb:  s.thumbnail_url,
        }));

        if (data.length === 0) {
            tbody.innerHTML = `
                <div class="px-6 py-16 text-center text-gray-600">
                    <div class="text-5xl mb-3 opacity-30">♫</div>
                    <p>No songs found for this genre.</p>
                </div>`;
            return;
        }

        tbody.innerHTML = data.map((song, i) => {
            const num   = String(i + 1).padStart(2, '0');
            const thumb = song.thumbnail_url ?? 'https://via.placeholder.com/40/252529/FF69B4?text=♫';
            const album = song.album ?? '—';
            const genre = song.genre
                ? `<span class="genre-badge">${song.genre}</span>`
                : `<span class="text-gray-600 text-xs">—</span>`;
            const titleEsc  = (song.title  || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
            const artistEsc = (song.artist || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
            const genreEsc  = (song.genre  || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
            const albumEsc  = (song.album  || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
            const playlistOpts = userPlaylists.map(pl =>
                `<button type="button" onclick="addToPlaylist(${pl.id}, ${song.id}, this)" class="block w-full text-left px-3 py-2 text-sm text-gray-300 hover:bg-docupink/10 hover:text-white transition">${pl.name}</button>`
            ).join('') || '<div class="px-3 py-2 text-xs text-gray-500">No playlists</div>';

            return `
            <div class="song-row grid grid-cols-12 px-6 py-3 border-b border-gray-800/20 items-center" onclick="playSong(${i})">
                <div class="col-span-1 text-gray-500 text-sm relative w-6">
                    <span class="song-num">${num}</span>
                    <svg class="play-icon hidden w-4 h-4 text-docupink absolute top-0 left-0" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                </div>
                <div class="col-span-4 flex items-center gap-3">
                    <div class="thumb-wrap w-10 h-10 flex-shrink-0">
                        <img src="${thumb}" class="w-full h-full object-cover" alt="thumb">
                    </div>
                    <span class="text-sm font-medium truncate">${song.title}</span>
                </div>
                <div class="col-span-3 text-gray-400 text-sm truncate">${song.artist}</div>
                <div class="col-span-2 text-gray-500 text-sm truncate">${album}</div>
                <div class="col-span-1">${genre}</div>
                <div class="col-span-1 flex justify-end gap-1" onclick="event.stopPropagation()">
                    <div class="relative add-to-playlist-wrapper">
                        <button onclick="event.stopPropagation(); togglePlaylistDropdown(event, ${song.id})"
                                class="p-1.5 text-gray-600 hover:text-docupink transition rounded-lg hover:bg-docupink/10"
                                title="Add to Playlist">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </button>
                        <div id="pl-dropdown-${song.id}" class="hidden absolute right-0 top-full mt-1 w-48 bg-[#1A1A1D] border border-gray-700 rounded-xl shadow-2xl z-50 py-1 max-h-48 overflow-y-auto">${playlistOpts}</div>
                    </div>
                    <button onclick="openEdit(${song.id}, '${titleEsc}', '${artistEsc}', '${genreEsc}', '${albumEsc}', '${song.youtube_url}')"
                            class="p-1.5 text-gray-600 hover:text-docupink transition rounded-lg hover:bg-docupink/10">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                </div>
            </div>`;
        }).join('');
    }

    // ─── Add to Playlist Bridge ────────────────────────────────────
    function togglePlaylistDropdown(event, songId) {
        event.stopPropagation();
        // Close all other dropdowns
        document.querySelectorAll('[id^="pl-dropdown-"]').forEach(el => el.classList.add('hidden'));
        const dropdown = document.getElementById('pl-dropdown-' + songId);
        if (dropdown) {
            dropdown.classList.toggle('hidden');
            // Populate if empty
            if (!dropdown.hasChildNodes() || dropdown.children.length === 0) {
                dropdown.innerHTML = userPlaylists.map(pl =>
                    `<button type="button" onclick="addToPlaylist(${pl.id}, ${songId}, this)" class="block w-full text-left px-3 py-2 text-sm text-gray-300 hover:bg-docupink/10 hover:text-white transition">${pl.name}</button>`
                ).join('') || '<div class="px-3 py-2 text-xs text-gray-500">No playlists</div>';
            }
        }
    }

    function addToPlaylist(playlistId, songId, btnEl) {
        btnEl.disabled = true;
        btnEl.classList.add('opacity-50');
        btnEl.textContent = 'Adding...';

        fetch('/playlists/' + playlistId + '/songs', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ song_id: songId })
        })
        .then(res => {
            if (!res.ok) throw new Error('Server returned ' + res.status);
            return res.json();
        })
        .then(data => {
            if (data.success) {
                const dropdown = document.getElementById('pl-dropdown-' + songId);
                if (dropdown) dropdown.classList.add('hidden');
                const playlistName = userPlaylists.find(p => p.id === playlistId)?.name || 'Playlist';
                Swal.fire({
                    icon: 'success',
                    title: 'Added!',
                    text: 'Song added to "' + playlistName + '"',
                    showConfirmButton: true,
                    confirmButtonColor: '#FF69B4',
                    confirmButtonText: 'View Playlist',
                    showCancelButton: true,
                    cancelButtonColor: '#6b7280',
                    cancelButtonText: 'Continue',
                    background: '#1A1A1D',
                    color: '#FFFFFF'
                }).then((r) => {
                    if (r.isConfirmed) window.location.href = '/playlists?playlist_id=' + playlistId;
                });
            }
        })
        .catch(err => {
            console.error('Failed to add song:', err);
            Swal.fire({
                icon: 'error',
                title: 'Failed!',
                text: 'Could not add song to playlist. Check console for details.',
                confirmButtonColor: '#FF69B4',
                background: '#1A1A1D',
                color: '#FFFFFF'
            });
        })
        .finally(() => {
            btnEl.disabled = false;
            btnEl.classList.remove('opacity-50');
            btnEl.textContent = userPlaylists.find(p => p.id === playlistId)?.name || 'Playlist';
        });
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.add-to-playlist-wrapper')) {
            document.querySelectorAll('[id^="pl-dropdown-"]').forEach(el => el.classList.add('hidden'));
        }
    });

    // "All" tab click handler
    document.querySelector('[data-genre="all"]').addEventListener('click', () => filterSongs('all'));

    // Load genre tabs on page load
    document.addEventListener('DOMContentLoaded', loadGenreTabs);
</script>
</body>
</html>