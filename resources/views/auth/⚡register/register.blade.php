<div class="select-none">
    <div class="relative min-h-screen flex items-center justify-center overflow-hidden bg-[#FAF7EF] dark:bg-[#0E1A14] px-6 py-10" style="font-family: 'Inter', sans-serif;">

        <svg class="absolute inset-0 w-full h-full opacity-[0.04] pointer-events-none" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="grille2" width="28" height="28" patternUnits="userSpaceOnUse">
                    <path d="M0 0 L0 28 M14 0 L14 28" stroke="#123524" stroke-width="1"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#grille2)"/>
        </svg>

        <div class="relative max-w-6xl w-full grid md:grid-cols-2 items-stretch shadow-2xl rounded-3xl overflow-hidden border border-[#E4DFCE] dark:border-[#1C3B2E]">

            <!-- Left: Photo panel -->
            <div class="relative hidden md:block bg-[#123524]">
                <img src="{{ asset('images/csav_building.jpg') }}"
                     alt="Colegio De Sta. Ana de Victorias, Inc. campus"
                     class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-[#0E1A14] via-[#0E1A14]/40 to-[#0E1A14]/10"></div>

                <div class="relative h-full flex flex-col justify-between p-10">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 shrink-0 rounded-full bg-white ring-2 ring-[#D4A537]/50 p-1 shadow-lg">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/5/55/LogoCSAV.png"
                                 alt="CSAV logo"
                                 class="w-full h-full object-contain">
                        </div>
                        <span class="text-white font-semibold leading-tight drop-shadow-md" style="font-family: 'Fraunces', serif; font-size: 1.1rem;">
                            Colegio De Sta. Ana<br>de Victorias, Inc.
                        </span>
                    </div>

                    <div>
                        <p class="text-sm text-white/75 max-w-xs mb-6">
                            Create an account to reserve facilities, request materials, and track your requests in one place.
                        </p>
                        <div class="flex gap-3">
                            <div class="flex-1 bg-white/10 backdrop-blur rounded-xl px-4 py-3 border border-white/15">
                                <p class="text-sm font-semibold text-white">Quick access</p>
                                <p class="text-xs text-white/60">Instant to materials</p>
                            </div>
                            <div class="flex-1 bg-white/10 backdrop-blur rounded-xl px-4 py-3 border border-white/15">
                                <p class="text-sm font-semibold text-white">Easy booking</p>
                                <p class="text-xs text-white/60">Reserve facilities fast</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile-only header -->
            <div class="md:hidden text-center pt-10 px-8">
                <img src="https://upload.wikimedia.org/wikipedia/commons/5/55/LogoCSAV.png"
                     alt="CSAV logo"
                     class="w-14 h-14 object-contain mx-auto mb-3">
                <h1 class="text-[#123524] dark:text-green-200 mb-1" style="font-family: 'Fraunces', serif; font-size: 1.9rem; font-weight: 700;">Join the CSAV Community</h1>
            </div>

            <!-- Right: Register form -->
            <div class="relative bg-white dark:bg-[#16281F] p-10 sm:p-12">

                <h2 class="text-[#123524] dark:text-green-200 mb-1" style="font-family: 'Fraunces', serif; font-size: 1.75rem; font-weight: 700;">Create your account</h2>
                <p class="text-sm text-gray-500 dark:text-neutral-400 mb-7">Fill in your details to get started</p>

                <form wire:submit="register" class="space-y-5">

                    {{-- Full Name --}}
                    <div>
                        <label class="block text-sm font-medium text-[#123524] dark:text-[#7FBF8E]">Full Name</label>
                        <input type="text" wire:model="name"
                               class="mt-2 w-full px-4 py-3 rounded-lg border border-[#D8D4C8] focus:ring-2 focus:ring-[#1C6B45] focus:border-[#1C6B45] dark:bg-[#0E1A14] dark:border-[#2A4B3A] dark:text-green-100 transition"
                               placeholder="Juan Dela Cruz">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-[#123524] dark:text-[#7FBF8E]">Email Address</label>
                        <input type="email" wire:model="email"
                               class="mt-2 w-full px-4 py-3 rounded-lg border border-[#D8D4C8] focus:ring-2 focus:ring-[#1C6B45] focus:border-[#1C6B45] dark:bg-[#0E1A14] dark:border-[#2A4B3A] dark:text-green-100 transition"
                               placeholder="you@csav.edu.ph">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Role Selector --}}
                    <div>
                        <label class="block text-sm font-medium text-[#123524] dark:text-[#7FBF8E] mb-2">I am a...</label>
                        <div class="grid grid-cols-3 gap-3">

                            <button type="button"
                                wire:click="$set('role', 'student')"
                                class="flex flex-col items-center gap-2 px-3 py-4 rounded-xl border-2 text-sm font-medium transition
                                    {{ $role === 'student'
                                        ? 'bg-[#123524] text-white border-[#123524] shadow-md'
                                        : 'bg-white dark:bg-[#0E1A14] text-[#123524] dark:text-green-100 border-[#D8D4C8] dark:border-[#2A4B3A] hover:border-[#1C6B45] hover:bg-[#F5F2E9] dark:hover:bg-[#16281F]' }}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M12 14l9-5-9-5-9 5 9 5z"/>
                                    <path d="M12 14l6.16-3.422A12.083 12.083 0 0 1 21 12c0 4-4 8-9 8s-9-4-9-8c0-.857.212-1.668.84-2.578L12 14z"/>
                                </svg>
                                Student
                            </button>

                            <button type="button"
                                wire:click="$set('role', 'faculty')"
                                class="flex flex-col items-center gap-2 px-3 py-4 rounded-xl border-2 text-sm font-medium transition
                                    {{ $role === 'faculty'
                                        ? 'bg-[#123524] text-white border-[#123524] shadow-md'
                                        : 'bg-white dark:bg-[#0E1A14] text-[#123524] dark:text-green-100 border-[#D8D4C8] dark:border-[#2A4B3A] hover:border-[#1C6B45] hover:bg-[#F5F2E9] dark:hover:bg-[#16281F]' }}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M17 20h5v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2h5"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                                Faculty
                            </button>

                            <button type="button"
                                wire:click="$set('role', 'program head')"
                                class="flex flex-col items-center gap-2 px-3 py-4 rounded-xl border-2 text-sm font-medium transition
                                    {{ $role === 'program head'
                                        ? 'bg-[#123524] text-white border-[#123524] shadow-md'
                                        : 'bg-white dark:bg-[#0E1A14] text-[#123524] dark:text-green-100 border-[#D8D4C8] dark:border-[#2A4B3A] hover:border-[#1C6B45] hover:bg-[#F5F2E9] dark:hover:bg-[#16281F]' }}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
                                    <rect x="9" y="3" width="6" height="4" rx="1"/>
                                    <path d="M9 12h6M9 16h4"/>
                                </svg>
                                Program Head
                            </button>

                        </div>
                        @error('role')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Department --}}
                    <div>
                        <label class="block text-sm font-medium text-[#123524] dark:text-[#7FBF8E] mb-2">Department</label>
                        <select wire:model="department_id"
                                class="w-full px-4 py-3 rounded-lg border border-[#D8D4C8] focus:ring-2 focus:ring-[#1C6B45] focus:border-[#1C6B45] dark:bg-[#0E1A14] dark:border-[#2A4B3A] dark:text-green-100 transition">
                            <option value="">Select your department</option>
                            @foreach($this->departments as $dept)
                                <option value="{{ $dept->id }}">College of {{ $dept->department_name }}</option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- ✅ Password with show/hide --}}
                    <div x-data="{ show: false }">
                        <label class="block text-sm font-medium text-[#123524] dark:text-[#7FBF8E]">Password</label>
                        <div class="relative mt-2">
                            <input :type="show ? 'text' : 'password'"
                                   wire:model="password"
                                   class="w-full px-4 py-3 pr-12 rounded-lg border border-[#D8D4C8] focus:ring-2 focus:ring-[#1C6B45] focus:border-[#1C6B45] dark:bg-[#0E1A14] dark:border-[#2A4B3A] dark:text-green-100 transition"
                                   placeholder="••••••••">
                            <button type="button"
                                    @click="show = !show"
                                    class="absolute inset-y-0 right-0 flex items-center px-3.5 text-gray-400 hover:text-[#123524] dark:hover:text-green-300 transition"
                                    :aria-label="show ? 'Hide password' : 'Show password'">
                                {{-- Eye open (visible when password is hidden) --}}
                                <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                                </svg>
                                {{-- Eye slash (visible when password is shown) --}}
                                <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" x-cloak>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- ✅ Confirm Password with show/hide --}}
                    <div x-data="{ show: false }">
                        <label class="block text-sm font-medium text-[#123524] dark:text-[#7FBF8E]">Confirm Password</label>
                        <div class="relative mt-2">
                            <input :type="show ? 'text' : 'password'"
                                   wire:model="password_confirmation"
                                   class="w-full px-4 py-3 pr-12 rounded-lg border border-[#D8D4C8] focus:ring-2 focus:ring-[#1C6B45] focus:border-[#1C6B45] dark:bg-[#0E1A14] dark:border-[#2A4B3A] dark:text-green-100 transition"
                                   placeholder="••••••••">
                            <button type="button"
                                    @click="show = !show"
                                    class="absolute inset-y-0 right-0 flex items-center px-3.5 text-gray-400 hover:text-[#123524] dark:hover:text-green-300 transition"
                                    :aria-label="show ? 'Hide password' : 'Show password'">
                                <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                                </svg>
                                <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" x-cloak>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Terms --}}
                    <div class="flex items-start gap-2">
                        <input type="checkbox" wire:model="terms"
                               class="mt-1 rounded border-[#D8D4C8] text-[#1C6B45] focus:ring-[#1C6B45]">
                        <label class="text-sm text-gray-600 dark:text-neutral-300">
                            I agree to the
                            <a href="/terms" class="text-[#B8862A] hover:text-[#966E22] font-medium">Terms of Service</a>
                            and
                            <a href="/privacy" class="text-[#B8862A] hover:text-[#966E22] font-medium">Privacy Policy</a>
                        </label>
                    </div>
                    @error('terms')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror

                    {{-- Submit --}}
                    <button type="submit"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                            class="w-full px-6 py-3.5 rounded-lg bg-[#123524] text-white font-semibold shadow-lg hover:bg-[#0C2418] transition">
                        <span wire:loading.remove wire:target="register">Create Account</span>
                        <span wire:loading wire:target="register">Creating account...</span>
                    </button>

                </form>

                {{-- Divider --}}
                <div class="my-7 flex items-center">
                    <div class="flex-grow border-t border-[#E4E1D8] dark:border-[#2A4B3A]"></div>
                    <span class="px-3 text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500">Already have an account?</span>
                    <div class="flex-grow border-t border-[#E4E1D8] dark:border-[#2A4B3A]"></div>
                </div>

                {{-- Login Link --}}
                <div class="text-center">
                    <a href="/"
                       class="inline-block w-full px-6 py-3.5 rounded-lg bg-[#D4A537] text-white font-semibold shadow hover:bg-[#B8862A] transition">
                        Log In
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
