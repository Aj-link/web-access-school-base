<div class="min-h-screen bg-gradient-to-br from-green-100 via-white to-yellow-100 flex items-center justify-center px-6 select-none"
    wire:poll.5s="checkStatus">
    <div class="max-w-md w-full text-center">

        {{-- Animated Clock Icon --}}
        <div class="flex justify-center mb-6">
            <div class="w-24 h-24 rounded-full bg-yellow-100 border-4 border-yellow-400 flex items-center justify-center animate-pulse">
                <svg class="w-12 h-12 text-yellow-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 1 1-20 0 10 10 0 0 1 20 0z"/>
                </svg>
            </div>
        </div>

        {{-- Title --}}
        <h1 class="text-3xl font-extrabold text-green-900 mb-2">Account Under Review</h1>
        <p class="text-gray-500 text-sm mb-6">
            Hi <span class="font-semibold text-green-700">{{ Auth::user()->name }}</span>, your account is currently being reviewed by our team.
        </p>

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-lg border border-green-200 p-6 mb-6 text-left space-y-4">

            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-green-700 text-white flex items-center justify-center font-bold text-sm">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-400">{{ Auth::user()->email }}</p>
                </div>
                <span class="ml-auto px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-700 rounded-full">
                    Pending
                </span>
            </div>

            <div class="border-t border-gray-100 pt-4 space-y-2 text-sm text-gray-600">
                <div class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-green-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Your registration has been received.
                </div>
                <div class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-yellow-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 1 1-20 0 10 10 0 0 1 20 0z"/>
                    </svg>
                    Our team is reviewing your account.
                </div>
                <div class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-blue-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    You will receive an email once approved.
                </div>
            </div>

        </div>

        {{-- Message --}}
        <div class="bg-green-50 border border-green-200 rounded-xl px-5 py-4 text-sm text-green-800 mb-6">
            ⏳ This usually takes <span class="font-semibold">5–10 minutes</span>. Please check back shortly or wait for our email notification.
        </div>


    </div>
</div>
