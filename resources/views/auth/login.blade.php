<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Linknet Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<style>
    .brand-logo {
        width: 100px;
        height: 100px;
        object-fit: contain;
        margin-bottom: 8px;
        filter: drop-shadow(0 0 20px rgba(255, 255, 255, 1)) drop-shadow(0 0 6px rgba(255, 255, 255, 0.8)) drop-shadow(0 4px 12px rgba(0, 0, 0, 0.5));
    }
</style>

<body class="bg-slate-950">
    <div class="min-h-screen flex items-center justify-center relative overflow-hidden p-4">
        <!-- Animated Background -->
        <div class="absolute inset-0 overflow-hidden">
            <!-- Grid Pattern -->
            <div class="absolute inset-0 bg-[linear-gradient(to_right,#1e293b_1px,transparent_1px),linear-gradient(to_bottom,#1e293b_1px,transparent_1px)] bg-[size:4rem_4rem] opacity-20"></div>

            <!-- Glowing Orbs -->
            <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-cyan-500/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
        </div>

        <!-- Login Container -->
        <div class="relative z-10 w-full max-w-md">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-28 h-28">

                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="brand-logo">

                </div>

                <h1 class="text-3xl font-bold text-white mb-2 tracking-tight">
                    LINKNET
                </h1>
                <p class="text-slate-400 text-sm">
                    Production Management System
                </p>
            </div>


            <!-- Card -->
            <div class="bg-slate-900/50 backdrop-blur-xl rounded-3xl border border-slate-800 shadow-2xl p-8">
                <h2 class="text-2xl font-bold text-white mb-6">Welcome Back</h2>

                @if (session('status'))
                <div class="mb-4 text-sm text-green-400 bg-green-400/10 border border-green-400/20 rounded-lg p-3">
                    {{ session('status') }}
                </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-slate-300 text-sm font-medium mb-2">Email</label>
                        <input id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="your@email.com"
                            class="block w-full px-4 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" />
                        @error('email')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-slate-300 text-sm font-medium mb-2">Password</label>
                        <input id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="block w-full px-4 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" />
                        @error('password')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember & Forgot -->
                    <div class="flex items-center justify-between text-sm">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                            <input id="remember_me" type="checkbox" name="remember" class="rounded border-slate-700 bg-slate-800 text-blue-500 focus:ring-blue-500 focus:ring-offset-slate-900 cursor-pointer">
                            <span class="ml-2 text-slate-400 group-hover:text-slate-300 transition">Remember me</span>
                        </label>
                    </div>

                    <!-- Login Button -->
                    <button type="submit" class="w-full py-3 bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 hover:scale-[1.02] active:scale-[0.98]">
                        Sign In
                    </button>

                    <!-- Register Link -->
                    @if (Route::has('register'))
                    <div class="text-center pt-4 border-t border-slate-800">
                        <p class="text-sm text-slate-400">
                            © 2025 Linknet. All rights reserved.
                        </p>
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</body>

</html>