<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.student-faculty')] class extends Component
{
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    protected function rules(): array
    {
        return [
            'current_password' => 'required|current_password',
            'new_password'     => 'required|min:6|confirmed',
        ];
    }

    protected $messages = [
        'current_password.required'         => 'Please enter your current password.',
        'current_password.current_password' => 'Current password is incorrect.',
        'new_password.required'             => 'Please enter a new password.',
        'new_password.min'                  => 'New password must be at least 6 characters.',
        'new_password.confirmed'            => 'Password confirmation does not match.',
    ];

    public function updatePassword(): void
    {
        $this->validate();

        Auth::user()->update([
            'password' => $this->new_password,
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        session()->flash('password_message', 'Password updated successfully.');
    }
};
