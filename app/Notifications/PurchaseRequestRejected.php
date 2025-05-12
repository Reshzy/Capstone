<?php

namespace App\Notifications;

use App\Models\PurchaseRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class PurchaseRequestRejected extends Notification implements ShouldQueue
{
    use Queueable;

    protected $purchaseRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(PurchaseRequest $purchaseRequest)
    {
        $this->purchaseRequest = $purchaseRequest;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database', WebPushChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Purchase Request Rejected: ' . $this->purchaseRequest->pr_number)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your purchase request has been rejected.')
            ->line('PR Number: ' . $this->purchaseRequest->pr_number)
            ->line('Title: ' . $this->purchaseRequest->title)
            ->line('Rejection Reason: ' . $this->purchaseRequest->rejection_reason)
            ->line('Rejected By: ' . $this->purchaseRequest->approver->name)
            ->line('Rejected Date: ' . $this->purchaseRequest->updated_at->format('M d, Y h:i A'))
            ->action('View Request', url('/purchase-requests/' . $this->purchaseRequest->id))
            ->line('You can make the necessary changes and resubmit your request.')
            ->line('Thank you for using the SVP System!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'purchase_request_id' => $this->purchaseRequest->id,
            'pr_number' => $this->purchaseRequest->pr_number,
            'title' => $this->purchaseRequest->title,
            'rejection_reason' => $this->purchaseRequest->rejection_reason,
            'message' => 'Your purchase request has been rejected',
            'action_url' => '/purchase-requests/' . $this->purchaseRequest->id,
        ];
    }
    
    /**
     * Get the WebPush representation of the notification.
     */
    public function toWebPush(object $notifiable, $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('Purchase Request Rejected')
            ->icon('/images/notification-icon.png')
            ->body('PR Number: ' . $this->purchaseRequest->pr_number . ' has been rejected. Reason: ' . substr($this->purchaseRequest->rejection_reason, 0, 80) . '...')
            ->action('View Request', '/purchase-requests/' . $this->purchaseRequest->id)
            ->data(['id' => $notification->id]);
    }
}
