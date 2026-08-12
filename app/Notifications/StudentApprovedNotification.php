<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class StudentApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $department = $notifiable->department?->department_name ?? 'your department';
        return (new MailMessage)
            ->subject('CSAV Account Approved')
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line('Your account has been approved. You can now log in and access your account.')
            ->line('You are Student at Colegio De Sta. Ana de Victorias under the Department of  ' . $department . '.')
            ->line('Thank you for being part of Colegio De Sta. Ana de Victorias!');
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
