<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AccountCreatedByAdminNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected string $userName,
        protected string $userEmail,
        protected string $defaultPassword,
        protected string $role,          // 'Program Head', 'Faculty', 'Student'
        protected ?string $department = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $loginUrl = url('/login');

        $deptLine = $this->department
            ? "<p style=\"margin:0 0 20px 0;font-size:14px;line-height:1.6;color:#374151;\">Department: <strong>{$this->department}</strong></p>"
            : '';

        $html = <<<HTML
        <div style="background-color:#f9fafb;padding:40px 20px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;margin:0 auto;">
                <tr>
                    <td style="padding-bottom:24px;text-align:center;">
                        <span style="display:inline-block;background-color:#123524;color:#ffffff;font-size:12px;font-weight:600;letter-spacing:0.05em;text-transform:uppercase;padding:6px 14px;border-radius:9999px;">
                            Colegio de Sta. Ana de Victorias
                        </span>
                    </td>
                </tr>
                <tr>
                    <td style="background-color:#ffffff;border:1px solid #e5e7eb;border-radius:16px;padding:36px 32px;box-shadow:0 1px 3px rgba(0,0,0,0.06);">

                        <div style="width:48px;height:48px;background-color:#dbeafe;border-radius:9999px;display:flex;align-items:center;justify-content:center;margin-bottom:20px;">
                            <span style="color:#2563eb;font-size:22px;line-height:48px;">&#128100;</span>
                        </div>

                        <h1 style="margin:0 0 4px 0;font-size:20px;font-weight:700;color:#111827;">
                            Your account has been created
                        </h1>
                        <p style="margin:0 0 24px 0;font-size:14px;color:#6b7280;">
                            Hi {$this->userName}, an Admin has set up an account for you.
                        </p>

                        <div style="height:1px;background-color:#f3f4f6;margin:0 0 24px 0;"></div>

                        <p style="margin:0 0 20px 0;font-size:15px;line-height:1.6;color:#374151;">
                            You can now log in to the CSAV Campus Portal using the credentials below.
                            For security, please change your password after your first login.
                        </p>

                        <div style="background-color:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:20px;margin:0 0 24px 0;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding:0 0 12px 0;">
                                        <p style="margin:0;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#9ca3af;">Email</p>
                                        <p style="margin:4px 0 0 0;font-size:14px;font-weight:600;color:#111827;">{$this->userEmail}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 0;border-top:1px solid #e5e7eb;">
                                        <p style="margin:0;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#9ca3af;">Default Password</p>
                                        <p style="margin:4px 0 0 0;font-size:14px;font-family:Consolas,Monaco,monospace;font-weight:600;color:#111827;background-color:#ffffff;border:1px solid #e5e7eb;padding:6px 10px;border-radius:6px;display:inline-block;">
                                            {$this->defaultPassword}
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 0 0 0;border-top:1px solid #e5e7eb;">
                                        <p style="margin:0;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#9ca3af;">Role</p>
                                        <p style="margin:4px 0 0 0;font-size:14px;font-weight:600;color:#111827;">{$this->role}</p>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        {$deptLine}

                        <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 8px 0;">
                            <tr>
                                <td style="background-color:#123524;border-radius:8px;">
                                    <a href="{$loginUrl}"
                                       style="display:inline-block;padding:12px 24px;font-size:14px;font-weight:600;color:#ffffff;text-decoration:none;border-radius:8px;">
                                        Log In to Your Account
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <p style="margin:24px 0 0 0;font-size:12px;line-height:1.6;color:#9ca3af;">
                            If you did not request this account, please contact the CSAV administration office immediately.
                        </p>

                    </td>
                </tr>
                <tr>
                    <td style="padding-top:24px;text-align:center;">
                        <p style="margin:0;font-size:12px;color:#9ca3af;">
                            Colegio de Sta. Ana de Victorias — Resource Management System
                        </p>
                    </td>
                </tr>
            </table>
        </div>
        HTML;

        return (new MailMessage)
            ->subject('Your CSAV Account Has Been Created')
            ->view('mail.raw', ['html' => $html]);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
