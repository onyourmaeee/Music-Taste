<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music Taste, ano tara?</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        docudark: '#1A1A1D',
                        docupink: '#FF69B4',
                    }
                }
            }
        }
    </script>
    <style>
        .glow-input {
            transition: all 0.3s ease;
        }
        .glow-input:focus {
            outline: none;
            border-color: #FF69B4 !important;
            box-shadow: 0 0 15px rgba(255, 105, 180, 0.8), inset 0 0 5px rgba(255, 105, 180, 0.5);
            transform: scale(1.01);
        }
    </style>
</head>
<body class="bg-[#0F0F0F] flex items-center justify-center min-h-screen p-6">

    <div class="flex bg-docudark text-white rounded-[30px] shadow-2xl w-full max-w-4xl overflow-hidden min-h-[500px] border border-gray-800">
        
        <div class="hidden md:flex w-1/2 p-10 bg-gradient-to-br from-[#1A1A1D] via-[#2D1B33] to-[#FF69B4]/20 flex-col justify-center relative">
            <div class="z-10">
                <h1 class="text-5xl font-black tracking-tighter leading-none">Ipakita mo sino ka</h1>
                <p class="text-gray-400 mt-4 text-md font-medium uppercase tracking-widest">genggeng sa umaga, batak mag relapse sa gabi</p>
                
                <div class="mt-12 flex justify-center">
                    <div class="text-docupink animate-pulse opacity-40 text-[120px] leading-none">
                        ♫
                    </div>
                </div>
            </div>
            <div class="absolute top-8 right-8 text-docupink text-xl opacity-30">+</div>
        </div>

        <div class="w-full md:w-1/2 p-10 flex flex-col justify-center bg-[#161618]">
            
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold text-white">Gumawa ka account rito!</h2>
                <a href="{{ route('login') }}" class="text-[10px] bg-docupink text-black px-3 py-1 rounded-full font-bold uppercase hover:bg-white transition">Login</a>
            </div>

            <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
                @csrf 

                <div>
                    <input type="text" name="name" placeholder="Full Name" value="{{ old('name') }}" required
                           class="glow-input w-full px-5 py-3 bg-[#252529] border border-gray-700 rounded-xl text-white placeholder-gray-500">
                </div>

                <div>
                    <input type="text" name="username" placeholder="Username" value="{{ old('username') }}" required
                           class="glow-input w-full px-5 py-3 bg-[#252529] border border-gray-700 rounded-xl text-white placeholder-gray-500">
                </div>

                <div>
                    <input type="email" name="email" placeholder="Email address" value="{{ old('email') }}" required
                           class="glow-input w-full px-5 py-3 bg-[#252529] border border-gray-700 rounded-xl text-white placeholder-gray-500">
                </div>

                <div>
                    <input type="password" name="password" placeholder="Password" required
                           class="glow-input w-full px-5 py-3 bg-[#252529] border border-gray-700 rounded-xl text-white placeholder-gray-500">
                </div>

                <div>
                    <input type="password" name="password_confirmation" placeholder="Confirm Password" required
                           class="glow-input w-full px-5 py-3 bg-[#252529] border border-gray-700 rounded-xl text-white placeholder-gray-500">
                </div>
                <button type="submit" 
                        class="w-full bg-docupink hover:shadow-[0_0_20px_rgba(255,105,180,0.4)] text-black font-black py-4 rounded-full transition-all active:scale-95 uppercase tracking-widest">
                    Sign up
                </button>
            </form>
        </div>
    </div>

    @if($errors->any())
    <script>
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            iconColor: '#ef4444',
            title: '{{ $errors->first() }}',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            background: '#1A1A1D',
            color: '#FFFFFF'
        });
    </script>
    @endif

    @if(session('success'))
    <script>
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            iconColor: '#FF69B4',
            title: '{{ session("success") }}',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
            background: '#1A1A1D',
            color: '#FFFFFF'
        });
    </script>
    @endif

</body>
</html>