<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Settings — Music Taste</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800;900&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { docudark: '#1A1A1D', docupink: '#FF69B4' },
                    fontFamily: { syne: ['Syne', 'sans-serif'], dm: ['DM Sans', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'DM Sans', sans-serif; background: #0F0F0F; color: #fff; }
        h1, h2, h3, .font-display { font-family: 'Syne', sans-serif; }
        .sidebar-link { transition: all 0.2s ease; border-left: 2px solid transparent; }
        .sidebar-link:hover, .sidebar-link.active { background: rgba(255, 105, 180, 0.08); border-left-color: #FF69B4; color: #FF69B4; }
        .glow-card { transition: all 0.3s ease; }
        .glow-card:hover { box-shadow: 0 0 30px rgba(255, 105, 180, 0.15); transform: translateY(-2px); }
        .mobile-sidebar { transform: translateX(-100%); transition: transform 0.3s ease; }
        .mobile-sidebar.open { transform: translateX(0); }
        .sidebar-overlay { opacity: 0; pointer-events: none; transition: opacity 0.3s ease; }
        .sidebar-overlay.open { opacity: 1; pointer-events: auto; }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: #0F0F0F; }
        ::-webkit-scrollbar-thumb { background: #FF69B4; border-radius: 2px; }
        .noise-bg::before { content: ''; position: absolute; inset: 0; background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.03'/%3E%3C/svg%3E"); pointer-events: none; border-radius: inherit; z-index: 0; }
        input, select { transition: border-color 0.2s ease; }
        input:focus, select:focus { border-color: #FF69B4; outline: none; }
    </style>
</head>
<body class="min-h-screen flex">

    <div id="sidebar-overlay" class="sidebar-overlay fixed inset-0 bg-black/60 z-20 lg:hidden" onclick="toggleSidebar()"></div>
    <aside class="mobile-sidebar w-64 flex-col bg-[#111113] border-r border-gray-800/60 min-h-screen fixed left-0 top-0 z-30 lg:flex lg:!translate-x-0">
        <div class="px-6 py-7 border-b border-gray-800/60 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-docupink flex items-center justify-center text-black font-black text-lg">♫</div>
            <span class="font-black text-lg tracking-tight">Music<span class="text-docupink">Taste</span></span>
        </div>
        <nav class="flex-1 px-3 py-6 space-y-1">
            <p class="text-[10px] text-gray-600 uppercase tracking-widest font-semibold px-3 mb-3">Main</p>
            <a href="{{ route('dashboard') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'active' : 'text-gray-400' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>
            <a href="{{ route('users.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('users.*') ? 'active' : 'text-gray-400' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Users
            </a>
            <a href="{{ route('songs.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('songs.*') ? 'active' : 'text-gray-400' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                Songs
            </a>
            <a href="{{ route('genres.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('genres.*') ? 'active' : 'text-gray-400' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                Genres
            </a>
            <a href="{{ route('playlists.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('playlists.*') ? 'active' : 'text-gray-400' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Playlists
            </a>
            <p class="text-[10px] text-gray-600 uppercase tracking-widest font-semibold px-3 mb-3 mt-6">Account</p>
            <a href="{{ route('settings') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium active">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Settings
            </a>
        </nav>
        <div class="px-4 py-5 border-t border-gray-800/60 flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-docupink to-pink-300 flex items-center justify-center text-black font-black text-sm">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div class="min-w-0">
                <p class="text-sm font-bold truncate">{{ auth()->user()->name }}</p>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button class="text-[10px] text-gray-500 hover:text-red-400 transition">Logout</button>
                </form>
            </div>
        </div>
    </aside>

    <main class="flex-1 lg:ml-64 min-h-screen p-6">
        <div class="lg:hidden flex items-center gap-3 mb-6">
            <button onclick="toggleSidebar()" class="text-gray-400 hover:text-white p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <h1 class="font-display font-black text-xl tracking-tight">Settings</h1>
        </div>

        @if(session('success'))
            <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-xl text-sm mb-6">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-xl text-sm mb-6">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="max-w-2xl mx-auto">
            <div class="noise-bg relative bg-[#1A1A1D] border border-gray-800/70 rounded-3xl p-8">
                <div class="relative z-10">
                    <h1 class="font-display font-black text-2xl tracking-tight mb-1">Profile Settings</h1>
                    <p class="text-gray-400 text-sm mb-8">Update your personal information</p>

                    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        {{-- Avatar --}}
                        <div class="flex items-center gap-6">
                            <div class="relative">
                                <div id="avatar-preview" class="w-24 h-24 rounded-full bg-cover bg-center bg-gray-800 border-2 border-docupink/40" style="background-image: url('{{ $user->avatar ? (str_starts_with($user->avatar, 'http') ? $user->avatar : asset('storage/' . $user->avatar)) : 'https://via.placeholder.com/96/1a1a1d/FF69B4?text=' . urlencode(substr($user->name, 0, 1)) }}');"></div>
                                <label for="avatar-input" class="absolute -bottom-1 -right-1 w-8 h-8 rounded-full bg-docupink text-black flex items-center justify-center cursor-pointer hover:scale-105 transition shadow-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </label>
                                <input id="avatar-input" name="avatar" type="file" accept="image/*" class="hidden" onchange="previewAvatar(event)">
                            </div>
                            <div>
                                <p class="font-bold text-sm">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                <p class="text-[10px] text-gray-600 mt-1">Click the camera icon to upload a new photo</p>
                            </div>
                        </div>

                        <hr class="border-gray-800/60">

                        {{-- Name --}}
                        <div>
                            <label class="block text-xs text-gray-400 mb-1.5 font-medium">Full Name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                   class="w-full bg-[#0F0F0F] border border-gray-700/60 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-docupink transition">
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-xs text-gray-400 mb-1.5 font-medium">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                   class="w-full bg-[#0F0F0F] border border-gray-700/60 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-docupink transition">
                        </div>

                        {{-- Gender --}}
                        <div>
                            <label class="block text-xs text-gray-400 mb-1.5 font-medium">Gender</label>
                            <select name="gender"
                                    class="w-full bg-[#0F0F0F] border border-gray-700/60 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-docupink transition">
                                <option value="">Select gender</option>
                                @foreach(['Male', 'Female', 'Other', 'Prefer not to say'] as $g)
                                    <option value="{{ $g }}" {{ old('gender', $user->gender) === $g ? 'selected' : '' }}>{{ $g }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Address --}}
                        <div>
                            <label class="block text-xs text-gray-400 mb-1.5 font-medium">Address</label>
                            <textarea name="address" rows="2"
                                      class="w-full bg-[#0F0F0F] border border-gray-700/60 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-docupink transition resize-none">{{ old('address', $user->address) }}</textarea>
                        </div>

                        <div class="flex gap-3 pt-2">
                            <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-xl border border-gray-700 text-sm text-gray-400 hover:text-white transition">Cancel</a>
                            <button type="submit" class="flex-1 btn-primary text-sm px-5 py-2.5 rounded-xl font-bold text-black" style="background: #FF69B4;">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        function toggleSidebar() {
            document.querySelector('.mobile-sidebar')?.classList.toggle('open');
            document.getElementById('sidebar-overlay')?.classList.toggle('open');
        }
        function previewAvatar(event) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatar-preview').style.backgroundImage = `url('${e.target.result}')`;
            };
            reader.readAsDataURL(file);
        }
    </script>
</body>
</html>
