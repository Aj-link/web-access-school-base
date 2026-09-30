<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

new class extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;
    public bool $terms = false;

    protected array $rules = [
        'email' => 'required|email',
        'password' => 'required|string|min:6',
        'terms' => 'accepted',
    ];

    protected array $messages = [
        'terms.accepted' => 'You must agree to the Privacy Policy before logging in.',
    ];

    public function login()
    {
        $this->validate();

        $throttleKey = Str::lower($this->email) . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('email', "Too many failed attempts. Please try again in {$seconds} seconds.");
            return;
        }

        // ✅ FIX: normalize email to lowercase for case-insensitive login
        if (Auth::attempt([
            'email'    => strtolower($this->email),
            'password' => $this->password,
        ], $this->remember)) {
            RateLimiter::clear($throttleKey);
            session()->regenerate();

            $user = Auth::user();

            if ($user->status === 'rejected') {
                Auth::logout();
                $this->addError('email', 'Your account has been rejected. Please contact the registrar.');
                return;
            }

            if ($user->status !== 'approved') {
                return redirect()->route('waiting');
            }

            if ($user->hasRole('admin')) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->hasRole('program head')) {
                return redirect()->route('coordinator.dashboard');
            }

            return redirect()->route('portal.dashboard');
        }

        $attempts = RateLimiter::attempts($throttleKey);
        $decaySeconds = match (true) {
            $attempts >= 6 => 300,
            $attempts >= 3 => 120,
            default        => 60,
        };

        RateLimiter::hit($throttleKey, $decaySeconds);

        $this->addError('email', 'Invalid credentials.');
    }
};
