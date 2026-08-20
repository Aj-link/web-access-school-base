<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

new class extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    protected array $rules = [
        'email' => 'required|email',
        'password' => 'required|string|min:6',
    ];

    public function login()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();

            $user = Auth::user();

            // Check approval status FIRST, before any role check
            if ($user->status === 'rejected') {
                Auth::logout();
                $this->addError('email', 'Your account has been rejected. Please contact the registrar.');
                return;
            }

            if ($user->status !== 'approved') {
                // Do NOT logout — waiting page needs Auth::user() to work
                return redirect()->route('waiting');
            }

            // Only reached if approved
            if ($user->hasRole('admin')) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->hasRole('program head')) {
                return redirect()->route('coordinator.dashboard');
            }

            return redirect()->route('portal.dashboard');
        }

        $this->addError('email', 'Invalid credentials.');
    }
};
