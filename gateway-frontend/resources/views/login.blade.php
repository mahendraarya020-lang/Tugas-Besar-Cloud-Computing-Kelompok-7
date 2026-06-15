<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TUBES CLOUD COMPUTING</title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            primary: '#24389c',
                            secondary: '#505f76',
                            background: '#f7f9fb',
                            outline: '#e2e8f0',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7f9fb;
        }
        .card-shadow {
            box-shadow: 0px 1px 3px rgba(0,0,0,0.06), 0px 1px 2px rgba(0,0,0,0.04);
        }
        .card-shadow-hover:hover {
            box-shadow: 0px 10px 15px -3px rgba(0,0,0,0.05), 0px 4px 6px -2px rgba(0,0,0,0.03);
            border-color: rgba(36, 56, 156, 0.2);
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md space-y-6">
        <!-- Logo / Title -->
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-[#24389c] to-[#3f51b5] text-white font-extrabold text-xl shadow-md mb-3">
                FO
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-[#191c1e] mb-1">
                TUBES CLOUD COMPUTING
            </h1>
            <p class="text-brand-secondary text-xs">Crafted for Professionals</p>
        </div>

        <!-- Card -->
        <div class="bg-white rounded-2xl p-8 border border-brand-outline card-shadow card-shadow-hover transition-all duration-300">
            <h2 class="text-xl font-bold text-[#191c1e] mb-5 text-center">Welcome Back</h2>

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-lg p-3 mb-5">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-700 text-sm rounded-lg p-3 mb-5">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="/login" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-semibold text-[#191c1e] uppercase tracking-wider mb-1.5">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required 
                           class="w-full px-3.5 py-2.5 rounded-lg bg-white border border-[#cbd5e1] text-[#191c1e] placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-[#24389c] focus:border-[#24389c] transition-all text-sm"
                           placeholder="you@example.com">
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-[#191c1e] uppercase tracking-wider mb-1.5">Password</label>
                    <input type="password" id="password" name="password" required 
                           class="w-full px-3.5 py-2.5 rounded-lg bg-white border border-[#cbd5e1] text-[#191c1e] placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-[#24389c] focus:border-[#24389c] transition-all text-sm"
                           placeholder="••••••••">
                </div>

                <button type="submit" 
                        class="w-full py-2.5 rounded-lg bg-[#24389c] hover:bg-[#1a2c80] text-white font-medium text-sm transition-all duration-200 shadow-sm hover:shadow active:scale-[0.99] mt-2">
                    Sign In
                </button>
            </form>

            <div class="mt-5 text-center text-xs text-brand-secondary">
                Don't have an account? 
                <a href="/register" class="text-[#24389c] hover:text-[#1a2c80] font-semibold underline underline-offset-2 transition-colors">
                    Create one now
                </a>
            </div>
        </div>
    </div>
</body>
</html>
