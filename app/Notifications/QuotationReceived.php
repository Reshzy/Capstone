<?php

namespace App\Notifications;

use App\Models\SupplierQuotation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class QuotationReceived extends Notification implements ShouldQueue
{
    use Queueable;

    protected $supplierQuotation;

    /**
     * Create a new notification instance.
     */
    public function __construct(SupplierQuotation $supplierQuotation)
    {
        $this->supplierQuotation = $supplierQuotation;
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
        $rfq = $this->supplierQuotation->requestForQuotation;
        $supplier = $this->supplierQuotation->supplier;
        $total = $this->supplierQuotation->items->sum('quoted_price');
        
        return (new MailMessage)
            ->subject('New Quotation Received: RFQ-' . $rfq->rfq_number)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('A new quotation has been received for RFQ-' . $rfq->rfq_number)
            ->line('Supplier: ' . $supplier->name)
            ->line('Total Amount: ₱' . number_format($total, 2))
            ->line('Submitted Date: ' . $this->supplierQuotation->created_at->format('M d, Y h:i A'))
            ->action('View Quotation', url('/supplier-quotations/' . $this->supplierQuotation->id))
            ->line('Thank you for using the SVP System!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $rfq = $this->supplierQuotation->requestForQuotation;
        $supplier = $this->supplierQuotation->supplier;
        $total = $this->supplierQuotation->items->sum('quoted_price');
        
        return [
            'supplier_quotation_id' => $this->supplierQuotation->id,
            'rfq_number' => $rfq->rfq_number,
            'supplier_name' => $supplier->name,
            'total_amount' => $total,
            'message' => 'New quotation received from ' . $supplier->name,
            'action_url' => '/supplier-quotations/' . $this->supplierQuotation->id,
        ];
    }
    
    /**
     * Get the WebPush representation of the notification.
     */
    public function toWebPush(object $notifiable, $notification): WebPushMessage
    {
        $rfq = $this->supplierQuotation->requestForQuotation;
        $supplier = $this->supplierQuotation->supplier;
        
        return (new WebPushMessage)
            ->title('New Quotation Received')
            ->icon('/images/notification-icon.png')
            ->body('RFQ-' . $rfq->rfq_number . ': New quotation received from ' . $supplier->name)
            ->action('View Quotation', '/supplier-quotations/' . $this->supplierQuotation->id)
            ->data(['id' => $notification->id]);
    }
}
