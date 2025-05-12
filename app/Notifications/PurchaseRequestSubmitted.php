<?php

namespace App\Notifications;

use App\Models\PurchaseRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class PurchaseRequestSubmitted extends Notification implements ShouldQueue
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
            ->subject('Purchase Request Submitted: ' . $this->purchaseRequest->pr_number)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('A new purchase request has been submitted and requires your attention.')
            ->line('PR Number: ' . $this->purchaseRequest->pr_number)
            ->line('Title: ' . $this->purchaseRequest->title)
            ->line('Requestor: ' . $this->purchaseRequest->user->name)
            ->line('Department: ' . $this->purchaseRequest->department->name)
            ->line('Estimated Amount: ₱' . number_format($this->purchaseRequest->estimated_amount, 2))
            ->action('View Request', url('/purchase-requests/' . $this->purchaseRequest->id))
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
            'requestor' => $this->purchaseRequest->user->name,
            'message' => 'A new purchase request has been submitted',
            'action_url' => '/purchase-requests/' . $this->purchaseRequest->id,
        ];
    }
    
    /**
     * Get the WebPush representation of the notification.
     */
    public function toWebPush(object $notifiable, $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('New Purchase Request Submitted')
            ->icon('/images/notification-icon.png')
            ->body('PR Number: ' . $this->purchaseRequest->pr_number . ' has been submitted by ' . $this->purchaseRequest->user->name)
            ->action('View Request', '/purchase-requests/' . $this->purchaseRequest->id)
            ->data(['id' => $notification->id]);
    }
}
