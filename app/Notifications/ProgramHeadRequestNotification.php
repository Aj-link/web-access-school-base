<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ProgramHeadRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected string $programHeadName,
        protected string $requestKind,
        protected string $details,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $html = <<<HTML
        <div style="background-color:#f9fafb;padding:40px 20px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
            ...
        </div>
        HTML;

        return (new MailMessage)
            ->subject("New {$this->requestKind} from {$this->programHeadName}")
            ->view('mail.raw', ['html' => $html]);
    }

    protected function buildUrl(): string
    {
        return url('/admin/requests');
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
