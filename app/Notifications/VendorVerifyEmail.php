<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\URL;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class VendorVerifyEmail extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    

    // public function toMail($notifiable): MailMessage
    // {
    //     // Add debugging
    //     \Log::info('Generating verification URL', [
    //         'vendor_id' => $notifiable->id,
    //         'email' => $notifiable->email
    //     ]);

    //     $baseUrl = config('app.url') . '/api/v1';
    //     $verificationUrl = URL::temporarySignedRoute(
    //         'vendor.verification.verify',
    //         now()->addMinutes(60),
    //         [
    //             'id' => $notifiable->getKey(),
    //             'hash' => sha1($notifiable->getEmailForVerification()),
    //         ],
    //         false
    //     );

    //     \Log::info('Verification URL generated', [
    //         'url' => $verificationUrl
    //     ]);

    //     return (new MailMessage)
    //         ->subject('Verify Your Vendor Email Address')
    //         ->line('Please click the button below to verify your email address.')
    //         ->action('Vendor Verify Email Address', $verificationUrl)
    //         ->line('If you did not create a vendor account, no further action is required.');
    // }

    public function toMail($notifiable): MailMessage
{
    \Log::info('Generating verification URL');

    // Get the full backend URL
    $verificationUrl = URL::temporarySignedRoute(
        'vendor.verification.verify',
        now()->addMinutes(60),
        [
            'id' => $notifiable->getKey(),
            'hash' => sha1($notifiable->getEmailForVerification()),
        ]
    );

    // Make sure URL is absolute
    if (!str_starts_with($verificationUrl, 'http')) {
        $verificationUrl = config('app.url') . $verificationUrl;
    }

    \Log::info('Verification URL generated', [
        'url' => $verificationUrl
    ]);

    return (new MailMessage)
        ->subject('Verify Your Vendor Email Address')
        ->line('Please click the button below to verify your email address.')
        ->action('Vendor Verify Email Address', $verificationUrl)
        ->line('If you did not create a vendor account, no further action is required.');
}

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
