<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;


class VendorStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected $status;
    protected $reason;

    public function __construct($status, $reason = null)
    {
        $this->status = $status;
        $this->reason = $reason;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Vendor Account Status Updated')
            ->greeting('Hello ' . $notifiable->full_name);

        switch ($this->status) {
            case 'approved':
                $message->line('Congratulations! Your vendor account has been approved.')
                    ->line('You can now start selling on our platform.')
                    ->action('Visit Your Dashboard', url('/vendor/dashboard'));
                break;

            case 'rejected':
                $message->line('Your vendor account application has been rejected.')
                    ->line('Reason: ' . $this->reason)
                    ->line('If you believe this is a mistake, please contact our support team.');
                break;

            case 'suspended':
                $message->line('Your vendor account has been suspended.')
                    ->line('Reason: ' . $this->reason)
                    ->line('Your products have been temporarily hidden from the marketplace.')
                    ->line('Please contact our support team for more information.');
                break;
        }

        return $message->line('Thank you for using our platform.');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'status' => $this->status,
            'reason' => $this->reason,
            'timestamp' => now(),
        ];
    }
}
