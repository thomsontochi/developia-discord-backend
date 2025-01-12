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
    //     $url = URL::temporarySignedRoute(
    //         'vendor.verification.verify',
    //         now()->addMinutes(60),
    //         [
    //             'id' => $notifiable->getKey(),
    //             'hash' => sha1($notifiable->getEmailForVerification()),
    //         ]
    //     );

    //     return (new MailMessage)
    //         ->subject('Verify Your Vendor Email Address')
    //         ->line('Please click the button below to verify your email address.')
    //         ->action('Verify Email Address', $url)
    //         ->line('If you did not create a vendor account, no further action is required.');
    // }

    public function toMail($notifiable): MailMessage
    {
        // Generate signed URL for API verification
        $verificationUrl = URL::temporarySignedRoute(
            'vendor.verification.verify',
            now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        // Extract query parameters from the signed URL
        $parsedUrl = parse_url($verificationUrl);
        parse_str($parsedUrl['query'] ?? '', $params);

        // Construct frontend URL with necessary parameters
        // $frontendUrl = config('app.frontend_url') . '/vendor/verify-email?' . http_build_query([
        //     'id' => $params['id'],
        //     'hash' => $params['hash'],
        //     'signature' => $params['signature'],
        //     'expires' => $params['expires'],
        // ]);

         // Use env directly
        // $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000') . '/vendor/verify-email?' . http_build_query([
        //     'id' => $params['id'],
        //     'hash' => $params['hash'],
        //     'signature' => $params['signature'],
        //     'expires' => $params['expires'],
        // ]);

        // Using full frontend URL
        $frontendUrl = 'http://localhost:3000/vendor/verify-email?' . http_build_query([
            'id' => $notifiable->getKey(),
            'hash' => sha1($notifiable->getEmailForVerification()),
            'signature' => $params['signature'],
            'expires' => $params['expires']
        ]);

        return (new MailMessage)
            ->subject('Verify Your Vendor Email Address')
            ->line('Please click the button below to verify your email address.')
            ->action('Vendor Verify Email Address', $frontendUrl)
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
