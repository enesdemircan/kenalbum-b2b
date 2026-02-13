<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CargoShippedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;
    public $cargoCompany;
    public $cargoBarcode;

    /**
     * Create a new notification instance.
     */
    public function __construct($order, $cargoCompany, $cargoBarcode)
    {
        $this->order = $order;
        $this->cargoCompany = $cargoCompany;
        $this->cargoBarcode = $cargoBarcode;
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
    public function toMail(object $notifiable): MailMessage
    {
        $cargoCompanyNames = [
            'everest' => 'Everest Kargo',
            'yurtici' => 'Yurtiçi Kargo',
            'kolay_gelsin' => 'Kolay Gelsin Kargo'
        ];

        $cargoName = $cargoCompanyNames[$this->cargoCompany] ?? ucfirst($this->cargoCompany);

        return (new MailMessage)
            ->subject('Siparişiniz Kargoya Verildi - ' . $this->order->order_number)
            ->view('emails.cargo-shipped', [
                'order' => $this->order,
                'cargoName' => $cargoName,
                'cargoBarcode' => $this->cargoBarcode
            ]);
    }

    /**
     * Get the array representation of the notification.
     * Database kanalı kullanılmadığı için bu metod artık gerekli değil.
     * Sadece mail gönderimi yapılıyor.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'cargo_company' => $this->cargoCompany,
            'cargo_barcode' => $this->cargoBarcode,
            'message' => 'Siparişiniz kargoya verildi',
            'type' => 'cargo_shipped'
        ];
    }

    /**
     * Get the SMS representation of the notification.
     * TODO: Vatna SMS entegrasyonu eklenecek
     */
    public function toSms(object $notifiable): string
    {
        $cargoCompanyNames = [
            'everest' => 'Everest Kargo',
            'yurtici' => 'Yurtiçi Kargo',
            'kolay_gelsin' => 'Kolay Gelsin Kargo'
        ];

        $cargoName = $cargoCompanyNames[$this->cargoCompany] ?? ucfirst($this->cargoCompany);

        return 'Siparisiniz kargoya verildi. Siparis No: ' . $this->order->order_number . ' Kargo: ' . $cargoName . ' Barkod: ' . $this->cargoBarcode;
    }
}
