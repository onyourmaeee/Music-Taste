<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users — MusicTaste</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { docudark: '#1A1A1D', docupink: '#FF69B4' } } }
        }
    </script>
    <style>
        body { font-family: 'DM Sans', sans-serif; }
        .sidebar-link { transition: all 0.2s ease; border-left: 2px solid transparent; }
        .sidebar-link:hover, .sidebar-link.active {
            background: rgba(255,105,180,0.08);
            border-left-color: #FF69B4;
            color: #FF69B4;
        }
    </style>
</head>
<body class="bg-[#0F0F0F] text-white min-h-screen flex">

    {{-- SIDEBAR --}}
    <aside class="hidden lg:flex w-64 flex-col bg-[#111113] border-r border-gray-800/60 min-h-screen fixed left-0 top-0 z-30">
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
            <a href="{{ route('users.index') }}" class="sidebar-link active flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Users
            </a>
            <a href="{{ route('songs.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                Songs
            </a>
            <a href="{{ route('genres.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                Genres
            </a>
            <a href="{{ route('playlists.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Playlists
            </a>
            <p class="text-[10px] text-gray-600 uppercase tracking-widest font-semibold px-3 mb-3 mt-6">Account</p>
            <a href="{{ route('settings') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400">
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
        <header class="sticky top-0 z-20 bg-[#0F0F0F]/80 backdrop-blur-md border-b border-gray-800/60 px-6 py-4 flex items-center justify-between">
            <div>
                <h1 class="font-black text-xl tracking-tight">Users Management</h1>
                <p class="text-xs text-gray-500 mt-0.5">Manage all registered users</p>
            </div>
            <button onclick="document.getElementById('addModal').classList.remove('hidden')"
                    class="bg-docupink text-black text-sm font-black px-5 py-2 rounded-full hover:shadow-[0_0_20px_rgba(255,105,180,0.4)] transition">
                + Add User
            </button>
        </header>

        <div class="flex-1 p-6">
            <div class="bg-[#1A1A1D] border border-gray-800/70 rounded-2xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-800/60 text-gray-500 text-xs uppercase tracking-widest">
                            <th class="text-left px-6 py-4">#</th>
                            <th class="text-left px-6 py-4">Name</th>
                            <th class="text-left px-6 py-4">Username</th>
                            <th class="text-left px-6 py-4">Email</th>
                            <th class="text-left px-6 py-4">Created</th>
                            <th class="text-left px-6 py-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $i => $user)
                        <tr class="border-b border-gray-800/30 hover:bg-white/5 transition">
                            <td class="px-6 py-4 text-gray-500">{{ $i + 1 }}</td>
                            <td class="px-6 py-4 font-medium">{{ $user->name }}</td>
                            <td class="px-6 py-4 text-gray-400">@{{ $user->username }}</td>
                            <td class="px-6 py-4 text-gray-400">{{ $user->email }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 flex gap-2">
                                <button onclick="openEdit({{ $user->id }}, '{{ $user->name }}', '{{ $user->username }}', '{{ $user->email }}')"
                                        class="text-xs bg-docupink/10 text-docupink border border-docupink/20 px-3 py-1.5 rounded-lg hover:bg-docupink/20 transition">
                                    Edit
                                </button>
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                      onsubmit="return confirmDeleteUser(event, this)">
                                    @csrf @method('DELETE')
                                    <button class="text-xs bg-red-500/10 text-red-400 border border-red-500/20 px-3 py-1.5 rounded-lg hover:bg-red-500/20 transition">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-600">No users found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    {{-- ADD MODAL --}}
    <div id="addModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-[#1A1A1D] border border-gray-800 rounded-2xl w-full max-w-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="font-black text-lg">Add New User</h2>
                <button onclick="document.getElementById('addModal').classList.add('hidden')" class="text-gray-500 hover:text-white">✕</button>
            </div>
            <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="text" name="name" placeholder="Full Name" required
                       class="w-full px-4 py-3 bg-[#252529] border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-docupink focus:outline-none">
                <input type="text" name="username" placeholder="Username" required
                       class="w-full px-4 py-3 bg-[#252529] border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-docupink focus:outline-none">
                <input type="email" name="email" placeholder="Email" required
                       class="w-full px-4 py-3 bg-[#252529] border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-docupink focus:outline-none">
                <input type="password" name="password" placeholder="Password" required
                       class="w-full px-4 py-3 bg-[#252529] border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-docupink focus:outline-none">
                <input type="password" name="password_confirmation" placeholder="Confirm Password" required
                       class="w-full px-4 py-3 bg-[#252529] border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-docupink focus:outline-none">
                <button type="submit" class="w-full bg-docupink text-black font-black py-3 rounded-full uppercase tracking-widest text-sm hover:shadow-[0_0_20px_rgba(255,105,180,0.4)] transition">
                    Add User
                </button>
            </form>
        </div>
    </div>

    {{-- EDIT MODAL --}}
    <div id="editModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-[#1A1A1D] border border-gray-800 rounded-2xl w-full max-w-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="font-black text-lg">Edit User</h2>
                <button onclick="document.getElementById('editModal').classList.add('hidden')" class="text-gray-500 hover:text-white">✕</button>
            </div>
            <form id="editForm" method="POST" class="space-y-4">
                @csrf @method('PUT')
                <input type="text" name="name" id="editName" placeholder="Full Name" required
                       class="w-full px-4 py-3 bg-[#252529] border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-docupink focus:outline-none">
                <input type="text" name="username" id="editUsername" placeholder="Username" required
                       class="w-full px-4 py-3 bg-[#252529] border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-docupink focus:outline-none">
                <input type="email" name="email" id="editEmail" placeholder="Email" required
                       class="w-full px-4 py-3 bg-[#252529] border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-docupink focus:outline-none">
                <button type="submit" class="w-full bg-docupink text-black font-black py-3 rounded-full uppercase tracking-widest text-sm hover:shadow-[0_0_20px_rgba(255,105,180,0.4)] transition">
                    Update User
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
    <script>
        Swal.fire({
            toast: true, position: 'top-end', icon: 'success',
            iconColor: '#FF69B4', title: '{{ session("success") }}',
            showConfirmButton: false, timer: 3500, timerProgressBar: true,
            background: '#1A1A1D', color: '#FFFFFF'
        });
    </script>
    @endif

    @if($errors->any())
    <script>
        Swal.fire({
            toast: true, position: 'top-end', icon: 'error',
            iconColor: '#ef4444', title: '{{ $errors->first() }}',
            showConfirmButton: false, timer: 4000, timerProgressBar: true,
            background: '#1A1A1D', color: '#FFFFFF'
        });
    </script>
    @endif

    <script>
        function openEdit(id, name, username, email) {
            document.getElementById('editForm').action = '/users/' + id;
            document.getElementById('editName').value = name;
            document.getElementById('editUsername').value = username;
            document.getElementById('editEmail').value = email;
            document.getElementById('editModal').classList.remove('hidden');
        }
        function confirmDeleteUser(event, form) {
            event.preventDefault();
            Swal.fire({
                title: 'Delete user?', text: 'This cannot be undone!', icon: 'warning',
                showCancelButton: true, confirmButtonColor: '#FF69B4', cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!', background: '#1A1A1D', color: '#FFFFFF'
            }).then((r) => { if (r.isConfirmed) form.submit(); });
            return false;
        }
    </script>
</body>
</html>