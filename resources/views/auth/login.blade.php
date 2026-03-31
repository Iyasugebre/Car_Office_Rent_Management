<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Car & Office ERP Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        outfit: ['Outfit', 'sans-serif'],
                        inter: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: '#6366f1',
                        secondary: '#a855f7',
                        accent: '#ec4899',
                        surface: '#0f172a',
                    },
                    animation: {
                        blob: 'blob 7s infinite',
                    },
                    keyframes: {
                        blob: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                            '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                            '100%': { transform: 'translate(0px, 0px) scale(1)' },
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full font-inter antialiased bg-[#0f172a] text-slate-200 selection:bg-indigo-500 selection:text-white">
    <div class="relative min-h-screen flex items-center justify-center p-6 overflow-hidden">
        {{-- Decorative background elements --}}
        <div class="absolute top-0 -left-4 w-72 h-72 bg-indigo-600 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
        <div class="absolute top-0 -right-4 w-72 h-72 bg-purple-600 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-600 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>
        
        <div class="relative w-full max-w-md">
            {{-- Logo / Header --}}
            <div class="mb-10 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 mb-6 rounded-3xl bg-gradient-to-tr from-indigo-600 to-purple-600 shadow-xl shadow-indigo-500/20 ring-1 ring-white/20">
                    <i data-feather="key" class="w-10 h-10 text-white"></i>
                </div>
                <h1 class="text-4xl font-extrabold tracking-tight text-white mb-2 font-outfit">Admin Panel</h1>
                <p class="text-slate-400 font-medium">Enterprise Resource Management System</p>
            </div>

            {{-- Glassmorphism Card --}}
            <div class="bg-white/5 backdrop-blur-2xl border border-white/10 rounded-[2.5rem] p-10 shadow-2xl overflow-hidden relative group">
                {{-- Inner glow --}}
                <div class="absolute -inset-0.5 bg-gradient-to-r from-indigo-500/20 to-purple-500/20 rounded-[2.5rem] blur opacity-0 group-hover:opacity-100 transition duration-1000"></div>
                
                <form action="{{ url('/login') }}" method="POST" class="relative space-y-6">
                    @csrf
                    
                    <div class="space-y-2">
                        <label for="email" class="text-sm font-semibold text-slate-300 ml-1">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i data-feather="mail" class="w-5 h-5 text-slate-500"></i>
                            </div>
                            <input type="email" name="email" id="email" required autofocus
                                class="w-full pl-12 pr-4 py-4 bg-slate-900/50 border border-white/10 rounded-2xl text-white placeholder-slate-500 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 outline-none transition-all duration-300"
                                placeholder="name@company.com" value="{{ old('email') }}">
                        </div>
                        @error('email')
                            <p class="text-xs font-medium text-rose-400 ml-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center justify-between ml-1">
                            <label for="password" class="text-sm font-semibold text-slate-300">Password</label>
                            <a href="#" class="text-xs font-bold text-indigo-400 hover:text-indigo-300 transition-colors">Forgot?</a>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i data-feather="lock" class="w-5 h-5 text-slate-500"></i>
                            </div>
                            <input type="password" name="password" id="password" required
                                class="w-full pl-12 pr-4 py-4 bg-slate-900/50 border border-white/10 rounded-2xl text-white placeholder-slate-500 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 outline-none transition-all duration-300"
                                placeholder="••••••••">
                        </div>
                        @error('password')
                            <p class="text-xs font-medium text-rose-400 ml-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center ml-1">
                        <label class="flex items-center space-x-3 cursor-pointer group">
                            <input type="checkbox" name="remember" class="w-5 h-5 rounded-lg border-white/10 bg-slate-900/50 text-indigo-600 focus:ring-offset-0 focus:ring-indigo-500-50 transition-all cursor-pointer">
                            <span class="text-sm font-medium text-slate-400 group-hover:text-slate-300 transition-colors">Stay signed in for 30 days</span>
                        </label>
                    </div>

                    <button type="submit" 
                        class="w-full py-4 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold rounded-2xl shadow-lg shadow-indigo-600/20 active:scale-[0.98] transition-all duration-200 flex items-center justify-center space-x-2">
                        <span>Sign In</span>
                        <i data-feather="chevron-right" class="w-5 h-5"></i>
                    </button>
                </form>
            </div>

            {{-- Footer text --}}
            <div class="mt-12 text-center text-slate-500 text-sm font-medium">
                <p>&copy; {{ date('Y') }} Car & Office ERP. Secure Management Gateway.</p>
            </div>
        </div>
    </div>

    <style>
        .animate-blob {
            animation: blob 7s infinite;
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        .animation-delay-4000 {
            animation-delay: 4s;
        }
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            feather.replace();
        });
    </script>
</body>
</html>
