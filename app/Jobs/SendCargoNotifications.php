<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use App\Models\User;
use App\Notifications\CargoShippedNotification;
use Illuminate\Support\Facades\Log;

class SendCargoNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $order;
    public $cargoCompany;
    public $cargoBarcode;

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order, $cargoCompany, $cargoBarcode)
    {
        $this->order = $order;
        $this->cargoCompany = $cargoCompany;
        $this->cargoBarcode = $cargoBarcode;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Siparişi yeniden yükle
            $order = Order::with('user')->find($this->order->id);
            
            if (!$order || !$order->user) {
                Log::error('SendCargoNotifications: Order veya User bulunamadı', [
                    'order_id' => $this->order->id,
                    'cargo_company' => $this->cargoCompany,
                    'cargo_barcode' => $this->cargoBarcode
                ]);
                return;
            }

            // Email notification gönder
            $order->user->notify(new CargoShippedNotification(
                $order,
                $this->cargoCompany,
                $this->cargoBarcode
            ));

            // SMS gönderimi - şimdilik devre dışı, ileride Vatna SMS ile eklenecek
            // TODO: Vatna SMS entegrasyonu eklenecek

            // Push notification için database'e kaydet
            // Bu veri frontend'de push notification olarak kullanılabilir
            
            Log::info('Cargo notifications başarıyla gönderildi', [
                'order_id' => $order->id,
                'user_id' => $order->user->id,
                'cargo_company' => $this->cargoCompany,
                'cargo_barcode' => $this->cargoBarcode
            ]);

        } catch (\Exception $e) {
            Log::error('SendCargoNotifications job hatası', [
                'order_id' => $this->order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Job'ı tekrar dene
            $this->fail($e);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SendCargoNotifications job başarısız', [
            'order_id' => $this->order->id,
            'error' => $exception->getMessage()
        ]);
    }
}
