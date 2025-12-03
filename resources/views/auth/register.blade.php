<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Linknet Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
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

        <!-- Register Container -->
        <div class="relative z-10 w-full max-w-md">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-cyan-500 mb-4 shadow-lg shadow-blue-500/50">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2 tracking-tight">LINKNET</h1>
                <p class="text-slate-400 text-sm">Production Management System</p>
            </div>

            <!-- Card -->
            <div class="bg-slate-900/50 backdrop-blur-xl rounded-3xl border border-slate-800 shadow-2xl p-8">
                <h2 class="text-2xl font-bold text-white mb-6">Create Account</h2>

                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-slate-300 text-sm font-medium mb-2">Full Name</label>
                        <input id="name" 
                            type="text" 
                            name="name" 
                            value="{{ old('name') }}" 
                            required 
                            autofocus 
                            autocomplete="name"
                            placeholder="John Doe"
                            class="block w-full px-4 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" />
                        @error('name')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-slate-300 text-sm font-medium mb-2">Email</label>
                        <input id="email" 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            required 
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
                            autocomplete="new-password"
                            placeholder="••••••••"
                            class="block w-full px-4 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" />
                        @error('password')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label for="password_confirmation" class="block text-slate-300 text-sm font-medium mb-2">Confirm Password</label>
                        <input id="password_confirmation" 
                            type="password"
                            name="password_confirmation"
                            required 
                            autocomplete="new-password"
                            placeholder="••••••••"
                            class="block w-full px-4 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" />
                        @error('password_confirmation')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Register Button -->
                    <button type="submit" class="w-full py-3 bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 hover:scale-[1.02] active:scale-[0.98]">
                        Create Account
                    </button>

                    <!-- Login Link -->
                    <div class="text-center pt-4 border-t border-slate-800">
                        <p class="text-sm text-slate-400">
                            Already have an account?
                            <a href="{{ route('login') }}" class="text-blue-400 hover:text-blue-300 font-medium transition ml-1">
                                Sign in
                            </a>
                        </p>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <p class="text-center text-slate-500 text-xs mt-8">
                © 2024 Linknet. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>