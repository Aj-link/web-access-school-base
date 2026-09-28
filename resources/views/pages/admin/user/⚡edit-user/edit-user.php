<?php

use App\Models\Department;
use App\Models\User;
use App\Notifications\PasswordResetByAdminNotification;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.admin')] class extends Component
{
    /**
     * The default password that the "Reset to Default" button applies.
     * Change this to whatever you want the fallback password to be.
     */
    private const DEFAULT_PASSWORD = 'csav.csav';

    public $user;
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $department_id;
    public $role = 'student';

    public function mount($id)
    {
        $this->user              = User::with('roles')->findOrFail($id);
        $this->name              = $this->user->name;
        $this->email             = $this->user->email;
        $this->department_id     = $this->user->department_id;
        $this->role              = $this->user->roles->first()?->name ?? 'student';
    }

    #[Computed()]
    public function departments()
    {
        return Department::orderBy('department_name')->get(['id', 'department_name']);
    }

    #[Computed()]
    public function isAdmin()
    {
        return $this->user->hasRole('admin');
    }

    #[Computed()]
    public function roles()
    {
        return [
            'program head' => 'Program Head',
            'faculty'      => 'Faculty',
            'student'      => 'Student',
        ];
    }

    /**
     * Expose the default password to the blade so it can be shown
     * in the button label if you want.
     */
    #[Computed()]
    public function defaultPassword()
    {
        return self::DEFAULT_PASSWORD;
    }

    protected function rules()
    {
        $rules = [
            'name'  => 'required|string|min:3',
            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'password' => 'nullable|string|min:6|confirmed',
        ];

        if (! $this->isAdmin) {
            $rules['department_id'] = 'required|exists:departments,id';
            $rules['role']          = 'required|in:program head,faculty,student';
        }

        return $rules;
    }

    protected $messages = [
        'name.required'          => 'Please enter the user\'s full name.',
        'name.min'               => 'Name must be at least 3 characters.',
        'email.required'         => 'Please enter an email address.',
        'email.email'            => 'Please enter a valid email address.',
        'email.unique'           => 'This email address is already registered.',
        'password.min'           => 'Password must be at least 6 characters.',
        'password.confirmed'     => 'Password confirmation does not match.',
        'department_id.required' => 'Please select a department.',
        'department_id.exists'   => 'Selected department is invalid.',
        'role.required'          => 'Please select a role.',
        'role.in'                => 'Selected role is invalid.',
    ];

    public function save()
    {
        $this->validate();

        $passwordChanged = filled($this->password);
        $plainPassword   = $this->password;

        $data = [
            'name'  => $this->name,
            'email' => $this->email,
        ];

        if (! $this->isAdmin) {
            $data['department_id'] = $this->department_id;
        }

        if ($passwordChanged) {
            $data['password'] = Hash::make($plainPassword);
        }

        $this->user->update($data);

        if (! $this->isAdmin) {
            $this->user->syncRoles([$this->role]);
        }

        if ($passwordChanged) {
            $this->sendPasswordResetEmail($plainPassword);
        }

        $this->reset(['password', 'password_confirmation']);

        $message = $passwordChanged
            ? 'User updated successfully! A password reset email has been sent.'
            : 'User updated successfully!';

        session()->flash('success', $message);
    }

    /**
     * ── NEW: One-click password reset to the default ──
     */
    public function resetToDefaultPassword(): void
    {
        if ($this->isAdmin) {
            session()->flash('success', 'Cannot reset the admin password from this page.');
            return;
        }

        $default = self::DEFAULT_PASSWORD;

        $this->user->update([
            'password' => Hash::make($default),
        ]);

        $this->sendPasswordResetEmail($default);

        session()->flash('success', "Password reset to the default value. A reset email has been sent to {$this->user->email}.");
    }

    /**
     * Shared helper — sends the PasswordResetByAdminNotification
     * with the given plaintext password.
     */
    protected function sendPasswordResetEmail(string $plainPassword): void
    {
        try {
            $departmentName = Department::find($this->department_id)?->department_name;
            $roleName       = $this->user->roles->first()?->name;

            $roleLabel = $roleName
                ? ucwords(str_replace('_', ' ', $roleName))
                : null;

            $this->user->notify(new PasswordResetByAdminNotification(
                userName:    $this->user->name,
                userEmail:   $this->user->email,
                newPassword: $plainPassword,
                role:        $roleLabel,
                department:  $departmentName,
            ));
        } catch (\Throwable $e) {
            \Log::warning('PasswordResetByAdminNotification failed: ' . $e->getMessage());
        }
    }
};
