<div class="select-none">
    <div class="relative min-h-screen flex items-center justify-center overflow-hidden bg-[#FAF7EF] dark:bg-[#0E1A14] px-6 py-10" style="font-family: 'Inter', sans-serif;">

        <!-- Subtle grille pattern, echoes the building's iron railings -->
        <svg class="absolute inset-0 w-full h-full opacity-[0.04] pointer-events-none" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="grille" width="28" height="28" patternUnits="userSpaceOnUse">
                    <path d="M0 0 L0 28 M14 0 L14 28" stroke="#123524" stroke-width="1"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#grille)"/>
        </svg>

        <div class="relative max-w-6xl w-full grid md:grid-cols-2 items-stretch shadow-2xl rounded-3xl overflow-hidden border border-[#E4DFCE] dark:border-[#1C3B2E]">

            <!-- Left: Diagonal photo panel -->
            <div class="relative hidden md:block bg-[#123524]">
                <img src="{{ asset('images/csav_building.jpg') }}"
                     alt="Colegio De Sta. Ana de Victorias, Inc. campus"
                     class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-[#0E1A14] via-[#0E1A14]/40 to-[#0E1A14]/10"></div>

                <div class="relative h-full flex flex-col justify-between p-10">
                    <div class="flex items-start gap-3">
                    </div>

                    <div>
                        <p class="text-sm text-white/75 max-w-xs">
                            Reserve facilities, request materials, and track approvals — one system for every student, faculty member, and coordinator on campus.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Mobile-only header -->
            <div class="md:hidden text-center pt-10 px-8">
                <img src="https://upload.wikimedia.org/wikipedia/commons/5/55/LogoCSAV.png"
                     alt="CSAV logo"
                     class="w-14 h-14 object-contain mx-auto mb-3">
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#1C6B45] dark:text-[#7FBF8E] mb-2">Est. Campus Portal</p>
                <h1 class="text-[#123524] dark:text-green-200 mb-1" style="font-family: 'Fraunces', serif; font-size: 1.9rem; font-weight: 700;">Colegio De Sta. Ana de Victorias, Inc.</h1>
            </div>

            <!-- Right: Login form -->
            <div class="relative bg-white dark:bg-[#16281F] p-10 sm:p-12 flex flex-col justify-center">

                <!-- Logo beside the school name -->
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-11 h-11 shrink-0 rounded-full bg-white ring-2 ring-[#D4A537]/50 p-1 shadow-sm">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/5/55/LogoCSAV.png"
                             alt="CSAV logo"
                             class="w-full h-full object-contain">
                    </div>
                    <span class="text-sm font-semibold text-[#123524] dark:text-green-200 leading-tight" style="font-family: 'Fraunces', serif;">
                        Colegio De Sta. Ana de Victorias, Inc.
                    </span>
                </div>

                <h2 class="text-[#123524] dark:text-green-200 mb-1" style="font-family: 'Fraunces', serif; font-size: 1.75rem; font-weight: 700;">Welcome back</h2>
                <p class="text-sm text-gray-500 dark:text-neutral-400 mb-8">Sign in to continue to your portal</p>

                <form wire:submit.prevent="login" class="space-y-5">
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-[#123524] dark:text-[#7FBF8E]">Email or Mobile</label>
                        <input type="email" id="email" name="email" wire:model='email'
                               class="mt-2 w-full px-4 py-3 rounded-lg border border-[#D8D4C8] focus:ring-2 focus:ring-[#1C6B45] focus:border-[#1C6B45] dark:bg-[#0E1A14] dark:border-[#2A4B3A] dark:text-green-100 transition"
                               placeholder="you@example.com" required>
                               @error('email') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-[#123524] dark:text-[#7FBF8E]">Password</label>
                        <input type="password" id="password" name="password" wire:model='password'
                               class="mt-2 w-full px-4 py-3 rounded-lg border border-[#D8D4C8] focus:ring-2 focus:ring-[#1C6B45] focus:border-[#1C6B45] dark:bg-[#0E1A14] dark:border-[#2A4B3A] dark:text-green-100 transition"
                               placeholder="••••••••" required>
                               @error('password') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center gap-2 text-[#123524] dark:text-green-300">
                            <input type="checkbox" class="rounded border-[#D8D4C8] text-[#1C6B45] focus:ring-[#1C6B45]">
                            Remember me
                        </label>
                        <a href="/forgot-password" class="text-[#B8862A] hover:text-[#966E22] font-medium">Forgot password?</a>
                    </div>

                    <!-- Submit -->
                    <button type="submit"
                            class="w-full px-6 py-3.5 rounded-lg bg-[#123524] text-white font-semibold shadow-lg hover:bg-[#0C2418] transition">
                        Log In
                    </button>
                </form>

                <!-- Divider -->
                <div class="my-7 flex items-center">
                    <div class="flex-grow border-t border-[#E4E1D8] dark:border-[#2A4B3A]"></div>
                    <span class="px-3 text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500">or</span>
                    <div class="flex-grow border-t border-[#E4E1D8] dark:border-[#2A4B3A]"></div>
                </div>

                <!-- Create Account -->
                <div class="text-center">
                    <a href="/register"
                       class="inline-block w-full px-6 py-3.5 rounded-lg bg-[#D4A537] text-white font-semibold shadow hover:bg-[#B8862A] transition">
                        Create New Account
                    </a>
                </div>
            </div>
        </div>
    </div>
    {{-- I have not failed. I've just found 10,000 ways that won't work. - Thomas Edison --}}
</div>
