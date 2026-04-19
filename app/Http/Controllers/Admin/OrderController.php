<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\MailService;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function index(Request $request)
    {

        $query = Order::with(['user.customer', 'cartItems.product']);

        // Sipariş no'ya göre filtreleme
        if ($request->filled('order_number')) {
            $query->where('order_number', 'like', '%' . $request->order_number . '%');
        }

        // Müşteri adı soyadına göre filtreleme
        if ($request->filled('customer_name')) {
            $query->where(function($q) use ($request) {
                $q->where('customer_name', 'like', '%' . $request->customer_name . '%')
                    ->orWhere('customer_surname', 'like', '%' . $request->customer_name . '%');
            });
        }

        // Firma adına göre filtreleme
        if ($request->filled('company_name')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->whereHas('customer', function($customerQuery) use ($request) {
                    $customerQuery->where('unvan', 'like', '%' . $request->company_name . '%');
                });
            });
        }

        // Barcode'a göre filtreleme (hem cart_id hem barcode)
        if ($request->filled('barcode')) {
            $query->whereHas('cartItems', function($q) use ($request) {
                $q->where(function($subQ) use ($request) {
                    $subQ->where('cart_id', 'like', '%' . $request->barcode . '%')
                        ->orWhere('barcode', 'like', '%' . $request->barcode . '%');
                });
            });
        }

        // Toplam fiyata göre filtreleme
        if ($request->filled('total_price')) {
            $query->where('total_price', '>=', $request->total_price);
        }

        // Genel duruma göre filtreleme
        if ($request->filled('overall_status_id')) {
            $query->where('status', $request->overall_status_id);
        }

        // Tarihe göre filtreleme
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15);

        // Filtre parametrelerini view'a gönder
        $filters = [
            'order_number' => $request->order_number,
            'customer_name' => $request->customer_name,
            'company_name' => $request->company_name,
            'barcode' => $request->barcode,
            'total_price' => $request->total_price,
            'overall_status_id' => $request->overall_status_id,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to
        ];

        return view('admin.orders.index', compact('orders', 'filters'));
    }

    public function show($id)
    {

        $order = Order::with([
            'user',
            'cartItems.product'
        ])->findOrFail($id);
        // Customer bilgisini yükle
        $customer = null;
        if ($order->user && $order->user->customer_id) {
            $customer = \App\Models\Customer::find($order->user->customer_id);
        }

        // Kullanıcının rolüne göre sipariş durumlarını filtrele
        $user = Auth::user();
        $orderStatuses = OrderStatus::query();

        // Administrator tüm durumları görebilir
        $isAdministrator = $user->roles()->whereIn('name', ['administrator', 'Administrator'])->exists();

        if (!$isAdministrator) {
            // Kullanıcının rollerine göre filtrele
            $userRoleIds = $user->roles()->pluck('roles.id');
            $orderStatuses->whereHas('roles', function($query) use ($userRoleIds) {
                $query->whereIn('roles.id', $userRoleIds);
            });
        }

        $orderStatuses = $orderStatuses->orderBy('id')->get();

        return view('admin.orders.show', compact('order', 'orderStatuses', 'customer'));
    }

    public function updateOrderMainStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:0,1,2,3'
        ]);

        $order = Order::findOrFail($id);
        $oldStatus = $order->status;

        // Debug: Sipariş bilgilerini logla
        \Log::info('Order Status Update', [
            'order_id' => $order->id,
            'old_status' => $oldStatus,
            'new_status' => $request->status,
            'total_price' => $order->total_price,
            'user_id' => $order->user_id,
            'user_customer_id' => $order->user->customer_id ?? 'null'
        ]);

        $order->update([
            'status' => $request->status
        ]);

        // Bakiye işlemleri
        if ($request->status == 1 && $oldStatus != 1) {
            // Durum "işlemde" olduğunda bakiyeden düş
            \Log::info('Processing payment for order', ['order_id' => $order->id]);

            if (!$order->processPayment()) {
                \Log::error('Payment processing failed for order', ['order_id' => $order->id]);
                return redirect()->back()->with('error', 'Bakiye yetersiz veya işlem yapılamadı!');
            }

            \Log::info('Payment processed successfully for order', ['order_id' => $order->id]);
        } elseif ($request->status == 3 && $oldStatus != 3) {
            // Durum "iptal" olduğunda bakiyeyi geri ekle
            \Log::info('Processing refund for order', ['order_id' => $order->id]);
            $order->refundPayment();
            \Log::info('Refund processed successfully for order', ['order_id' => $order->id]);
        }

        switch($request->status) {
            case 0:
                $statusText = 'Onay Bekliyor';
                break;
            case 1:
                $statusText = 'İşlemde';
                break;
            case 2:
                $statusText = 'Teslim Edildi';
                break;
            case 3:
                $statusText = 'İptal';
                break;
            default:
                $statusText = 'Bilinmiyor';
        }

        return redirect()->back()->with('success', "Sipariş durumu '{$statusText}' olarak güncellendi!");
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'order_status_id' => 'required|exists:order_statuses,id'
        ]);

        // Kullanıcının bu order status'e erişim izni var mı kontrol et
        $user = Auth::user();
        $orderStatus = OrderStatus::find($request->order_status_id);

        if (!$orderStatus) {
            return redirect()->back()->with('error', 'Geçersiz sipariş durumu!');
        }

        // Administrator tüm durumları kullanabilir
        $isAdministrator = $user->roles()->whereIn('name', ['administrator', 'Administrator'])->exists();

        if (!$isAdministrator) {
            // Kullanıcının rollerine göre kontrol et
            $userRoleIds = $user->roles()->pluck('roles.id');
            $hasAccess = $orderStatus->roles()->whereIn('roles.id', $userRoleIds)->exists();
            if (!$hasAccess) {
                return redirect()->back()->with('error', 'Bu sipariş durumunu kullanma yetkiniz yok!');
            }
        }

        // Aynı durumun son eklenen durum olup olmadığını kontrol et
        $lastHistory = $order->statusHistories()->latest()->first();
        if ($lastHistory && $lastHistory->order_status_id == $request->order_status_id) {
            return redirect()->back()->with('error', 'Bu durum zaten mevcut!');
        }

        // Önceki durumu al
        $previousStatus = null;
        if ($lastHistory) {
            $previousStatus = $lastHistory->orderStatus;
        }

        // Yeni durumu al
        $newStatus = OrderStatus::find($request->order_status_id);

        // Yeni durum geçmişi kaydı oluştur
        \App\Models\OrderStatusHistory::create([
            'order_id' => $order->id,
            'order_status_id' => $request->order_status_id,
            'user_id' => Auth::id()
        ]);

        // Sipariş durumu güncelleme e-postası gönder
        $mailService = new MailService();
        $mailService->sendOrderStatusUpdateEmail($order->load('user'), $newStatus, $previousStatus);

        return redirect()->back()->with('success', 'Sipariş durumu güncellendi!');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:0,1,2,3'
        ]);

        $oldStatus = $order->status;

        $order->update([
            'status' => $request->status
        ]);

        // Bakiye işlemleri
        if ($request->status == 1 && $oldStatus != 1) {
            // Durum "işlemde" olduğunda bakiyeden düş
            if (!$order->processPayment()) {
                return redirect()->back()->with('error', 'Bakiye yetersiz veya işlem yapılamadı!');
            }
        } elseif ($request->status == 3 && $oldStatus != 3) {
            // Durum "iptal" olduğunda bakiyeyi geri ekle
            $order->refundPayment();
        }

        switch($request->status) {
            case 0:
                $statusText = 'Onay Bekliyor';
                break;
            case 1:
                $statusText = 'İşlemde';
                break;
            case 2:
                $statusText = 'Teslim Edildi';
                break;
            case 3:
                $statusText = 'İptal Edildi';
                break;
            default:
                $statusText = 'Bilinmiyor';
        }

        return redirect()->back()->with('success', 'Sipariş durumu "' . $statusText . '" olarak güncellendi.');
    }

    public function updateCartStatus(Request $request, Order $order)
    {

        $request->validate([
            'cart_id' => 'required|exists:carts,id',
            'order_status_id' => 'required|exists:order_statuses,id',
            'cargo_company' => 'nullable|string|in:everest,yurtici,kolay_gelsin'
        ]);

        // Kullanıcının bu order status'e erişim izni var mı kontrol et
        $user = Auth::user();
        $orderStatus = OrderStatus::find($request->order_status_id);

        if (!$orderStatus) {
            return redirect()->back()->with('error', 'Geçersiz sipariş durumu!');
        }

        // Administrator tüm durumları kullanabilir
        $isAdministrator = $user->roles()->whereIn('name', ['administrator', 'Administrator'])->exists();

        if (!$isAdministrator) {
            // Kullanıcının rollerine göre kontrol et
            $userRoleIds = $user->roles()->pluck('roles.id');
            $hasAccess = $orderStatus->roles()->whereIn('roles.id', $userRoleIds)->exists();
            if (!$hasAccess) {
                return redirect()->back()->with('error', 'Bu sipariş durumunu kullanma yetkiniz yok!');
            }
        }

        // Aynı cart ID için son durumu kontrol et
        $lastHistory = \App\Models\OrderStatusHistory::where('cart_id', $request->cart_id)
            ->latest()
            ->first();

        if ($lastHistory && $lastHistory->order_status_id == $request->order_status_id) {
            return redirect()->back()->with('error', 'Bu durum zaten mevcut!');
        }

        // Eğer kargo firması seçildiyse ve durum "kargoya verildi" ise kargo oluştur
        if ($request->cargo_company && $orderStatus->id == 17) {
            try {
                $cargoService = new \App\Services\CargoService();

                // Cart'ı bul
                $cart = Cart::find($request->cart_id);
                if (!$cart) {
                    return redirect()->back()->with('error', 'Cart bulunamadı!');
                }

                // Sipariş bilgilerini al (zaten elimizde var)
                $orderData = [
                    'customer_name' => $order->customer_name,
                    'customer_surname' => $order->customer_surname,
                    'customer_phone' => $order->customer_phone,
                    'shipping_address' => $order->shipping_address,
                    'total_price' => $order->total_price,
                    'order_number' => $order->order_number,
                    'payment_type' => $request->payment_type,
                    'barcode' => $cart->barcode,
                    'city' => $cart->city ?? $order->city ?? 'İstanbul',
                    'district' => $cart->district ?? $order->district ?? 'Kadıköy',
                ];

                // Kargo oluştur
                $cargoResult = $cargoService->createCargo($request->cargo_company, $orderData);

                // Debug için log ekle
                \Log::info('Cargo service result:', [
                    'cargo_company' => $request->cargo_company,
                    'cart_id' => $request->cart_id,
                    'cargo_result' => $cargoResult,
                    'result_type' => gettype($cargoResult),
                    'success_key_exists' => isset($cargoResult['success']),
                    'barcode_key_exists' => isset($cargoResult['barcode'])
                ]);

                if ($cargoResult['success'] && $cargoResult['barcode']) {
                    // Cart'a kargo barkodunu ve tracking number'ı kaydet
                    $cargoBarcode = $cargoResult['barcode'];
                    $trackingNumber = $cargoResult['tracking_number'] ?? null;
                    $trackingUrl = $cargoResult['tracking_url'] ?? null;
                    $barcodeZpl = $cargoResult['barcode_zpl'] ?? null;

                    // Eğer tracking number varsa, barcode ile birlikte kaydet
                    if ($trackingNumber) {
                        $cargoBarcode = $cargoBarcode . ' (Takip: ' . $trackingNumber . ')';
                    }

                    // Tracking URL ve Barcode ZPL'i de kaydet
                    $cart->update([
                        'cargo_barcode' => $cargoBarcode,
                        'tracking_url' => $trackingUrl,
                        'barcode_zpl' => $barcodeZpl,
                        'cargo_customer' => $request->cargo_company
                    ]);

                    // Status history oluştur
                    \App\Models\OrderStatusHistory::create([
                        'cart_id' => $request->cart_id,
                        'order_status_id' => $request->order_status_id,
                        'user_id' => Auth::id(),
                        'notes' => 'Kargo firması: ' . ucfirst($request->cargo_company) . ', Barkod: ' . $cargoResult['barcode'] . ($trackingNumber ? ', Takip: ' . $trackingNumber : '')
                    ]);

                    // Kargo notification'larını kuyruğa ekle
                    \App\Jobs\SendCargoNotifications::dispatch(
                        $order,
                        $request->cargo_company,
                        $cargoResult['barcode']
                    );

                    return redirect()->route('admin.orders.cargo-pdf', [
                        'order' => $order->id,
                        'cart' => $request->cart_id
                    ])->with('success', 'Ürün durumu güncellendi ve kargo oluşturuldu! Barkod: ' . $cargoResult['barcode'] . ($trackingNumber ? ', Takip: ' . $trackingNumber : ''));
                } else {
                    $errorMessage = '';
                    if (isset($cargoResult['message'])) {
                        if (is_array($cargoResult['message'])) {
                            $errorMessage = implode(', ', $cargoResult['message']);
                        } else {
                            $errorMessage = $cargoResult['message'];
                        }
                    } else {
                        $errorMessage = 'Bilinmeyen kargo hatası';
                    }
                    return redirect()->back()->with('error', 'Kargo oluşturulamadı: ' . $errorMessage);
                }

            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Kargo işlemi sırasında hata: ' . $e->getMessage());
            }
        }

        // Status history oluştur (cart_id alanına cart ID kaydet)
        \App\Models\OrderStatusHistory::create([
            'cart_id' => $request->cart_id, // Cart ID'yi cart_id alanına kaydet
            'order_status_id' => $request->order_status_id,
            'user_id' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Ürün durumu güncellendi!');
    }

    public function deleteStatusHistory(Request $request, $historyId)
    {
        // Sadece administrator rolü silme işlemi yapabilir
        $user = Auth::user();
        $isAdministrator = $user->roles()->where('roles.id', 1)->exists();

        if (!$isAdministrator) {
            return redirect()->back()->with('error', 'Bu işlem için yetkiniz yok!');
        }

        $statusHistory = \App\Models\OrderStatusHistory::findOrFail($historyId);
        $statusHistory->delete();

        return redirect()->back()->with('success', 'Durum geçmişi kaydı silindi!');
    }

    /**
     * Generate ZPL PDF for cart item
     */
    public function generateZplPdf(Order $order, Cart $cart)
    {
        try {
            // Cart'ın bu order'a ait olduğunu kontrol et
            if ($cart->order_id !== $order->id) {
                abort(404, 'Cart bu order\'a ait değil');
            }

            // ZPL verisi var mı kontrol et
            if (!$cart->barcode_zpl) {
                return redirect()->back()->with('error', 'Bu cart item için ZPL verisi bulunamadı');
            }

            // ZPL to Image service'ini kullan
            $zplService = new \App\Services\ZplToImageService();

            // ZPL'i gerçek barcode görseline çevir
            $barcodeImage = $zplService->convertZplToImage($cart->barcode_zpl);

            if ($barcodeImage !== null && !empty($barcodeImage)) {
                // Gerçek barcode görseli var - PDF'e ekle
                $pdf = \PDF::loadHTML($this->createSimpleBarcodePdf($barcodeImage, $cart->cargo_barcode));
            } else {
                // Görsel yok - sadece ZPL komutları ile PDF
                $pdf = \PDF::loadHTML($this->createZplOnlyPdf($cart->barcode_zpl, $cart->cargo_barcode));
            }

            $pdf->setPaper('a4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true
            ]);

            $pdfContent = $pdf->output();

            // Dosya adı
            $fileName = 'ZPL_Barcode_' . str_replace([' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $cart->cargo_barcode) . '_' . date('Y-m-d_H-i-s') . '.pdf';

            // PDF'i response olarak döndür
            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"')
                ->header('Content-Length', strlen($pdfContent));

        } catch (\Exception $e) {
            Log::error('ZPL PDF oluşturma hatası: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'cart_id' => $cart->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'PDF oluşturma sırasında hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Basit barcode PDF template (sadece görsel)
     */
    private function createSimpleBarcodePdf(string $barcodeImage, string $barcodeInfo): string
    {
        $html = '<!DOCTYPE html>';
        $html .= '<html lang="tr">';
        $html .= '<head>';
        $html .= '<meta charset="UTF-8">';
        $html .= '<title>Barcode - ' . $barcodeInfo . '</title>';
        $html .= '<style>';
        $html .= 'body { margin: 0; padding: 20px; text-align: center; }';
        $html .= 'img { max-width: 100%; height: auto; }';
        $html .= '</style>';
        $html .= '</head>';
        $html .= '<body>';
        $html .= '<img src="data:image/png;base64,' . base64_encode($barcodeImage) . '" alt="Barcode" />';
        $html .= '</body>';
        $html .= '</html>';

        return $html;
    }

    /**
     * Sadece ZPL komutları ile PDF (görsel yoksa)
     */
    private function createZplOnlyPdf(string $zplData, string $barcodeInfo): string
    {
        $html = '<!DOCTYPE html>';
        $html .= '<html lang="tr">';
        $html .= '<head>';
        $html .= '<meta charset="UTF-8">';
        $html .= '<title>ZPL - ' . $barcodeInfo . '</title>';
        $html .= '<style>';
        $html .= 'body { font-family: "Courier New", monospace; margin: 20px; font-size: 10px; }';
        $html .= 'pre { white-space: pre-wrap; word-wrap: break-word; }';
        $html .= '</style>';
        $html .= '</head>';
        $html .= '<body>';
        $html .= '<pre>' . htmlspecialchars($zplData) . '</pre>';
        $html .= '</body>';
        $html .= '</html>';

        return $html;
    }

    /**
     * Generate cargo PDF for shipping label (50mm x 110mm)
     */
    public function generateCargoPdf($orderId, $cartId)
    {
        $order = Order::with([
            'user.customer',
            'cartItems.product'
        ])->findOrFail($orderId);

        $cart = Cart::findOrFail($cartId);

        // Cart'ın bu order'a ait olduğunu kontrol et
        if ($cart->order_id != $order->id) {
            abort(404, 'Cart bu order\'a ait değil');
        }

        // PDF için Blade template kullan
        $pdf = Pdf::loadView('admin.orders.cargo-pdf', compact('order', 'cart'));

        // Özel boyut ayarla - Zebra etiket yazıcısı için 2" x 4" (50.8mm x 101.6mm)
        // 1 inch = 72 points, 2" = 144 points, 4" = 288 points
        // Yatay (landscape): genişlik 4", yükseklik 2"
        $pdf->setPaper([0, 0, 200, 300], 'landscape');

        // PDF ayarları - print method'u ile aynı ayarlar (Türkçe karakter desteği)
        $pdf->setOptions([
            'isHtml5ParserEnabled' => false,
            'isRemoteEnabled' => false,
            'defaultFont' => 'Arial',
            'chroot' => public_path(),
            'enable_remote' => false,
            'enable_local_file_access' => true,
            'dpi' => 72,
            'image_dpi' => 72,
            'image_cache_dir' => storage_path('app/pdf_cache'),
            'temp_dir' => storage_path('app/pdf_temp'),
            'log_output_file' => storage_path('logs/dompdf.log')
        ]);

        $filename = "kargo-etiketi-{$order->order_number}-{$cart->id}.pdf";
        return $pdf->stream($filename);
    }

    /**
     * Print order details for each cart item
     */
    public function print($id)
    {
        // Timeout artır
        set_time_limit(300); // 5 dakika
        ini_set('max_execution_time', 300);

        $order = Order::with([
            'user.customer',
            'cartItems.product',
            'cartItems.user.customer'
        ])->findOrFail($id);

        // Her cart item için resim kontrolü yap
        $hasImages = false;
        foreach ($order->cartItems as $cartItem) {
            if ($cartItem->cart_id) {
                $cart = Cart::find($cartItem->cart_id);
                if ($cart && !empty($cart->images)) {
                    $hasImages = true;
                    break;
                } 
            }
        }
 
        // Eğer hiç resim yoksa uyarı ver ama PDF oluşturmaya devam et
        if (!$hasImages) {
            \Log::warning('PDF oluşturulurken medya dosyaları bulunamadı', [
                'order_id' => $order->id,
                'order_number' => $order->order_number
            ]);
        }

        // PDF oluştur
        $pdf = Pdf::loadView('admin.orders.print', compact('order'));

        // A4 boyutunda ve her cart item için ayrı sayfa
        $pdf->setPaper('a5');

        // PDF ayarları - maksimum hız için optimize edildi
        $pdf->setOptions([
            'isHtml5ParserEnabled' => false, // Hız için false
            'isRemoteEnabled' => false, // Hız için false
            'defaultFont' => 'Arial',
            'chroot' => public_path(),
            'enable_remote' => false, // Hız için false
            'enable_local_file_access' => true,
            'dpi' => 72, // Düşük DPI - hız için
            'image_dpi' => 72, // Resim DPI'sı
            'image_cache_dir' => storage_path('app/pdf_cache'), // Cache dizini
            'temp_dir' => storage_path('app/pdf_temp'), // Temp dizini
            'log_output_file' => storage_path('logs/dompdf.log') // Log dosyası
        ]);

        return $pdf->stream("siparis-{$order->order_number}.pdf");
    }

    /**
     * Download cart files with custom filename
     */
    public function downloadCartFiles($orderId, $cartId)
    {
        try {
            $order = Order::findOrFail($orderId);
            $cart = Cart::findOrFail($cartId);

            // S3/R2 zip dosyası var mı kontrol et
            if (empty($cart->s3_zip)) {
                abort(404, 'No files found for this cart item');
            }
            
            if (isset($cart->s3_zip) && strpos($cart->s3_zip, 'sendgb') !== false) {
               return redirect()->away($cart->s3_zip);
            }

            // S3 URL'inden dosya yolunu çıkar (cart_id değişmiş olsa bile doğru dosyayı bulur)
            $s3Url = $cart->s3_zip;
            $parsedUrl = parse_url($s3Url);
            $r2Path = ltrim($parsedUrl['path'] ?? '', '/');

            \Log::info('Downloading from R2', [
                'cart_id' => $cartId,
                'cart_identifier' => $cart->cart_id,
                'r2_path' => $r2Path,
                's3_zip' => $s3Url
            ]);

            // R2'den dosya içeriğini al
            if (!$r2Path || !Storage::disk('s3')->exists($r2Path)) {
                abort(404, 'Dosya R2\'de bulunamadı');
            }

            $fileName = $cart->cart_id . '.zip';

            // Dosyayı stream olarak indir (bellek dostu)
            return Storage::disk('s3')->download($r2Path, $fileName);

        } catch (\Exception $e) {
            \Log::error('Cart file download failed', [
                'order_id' => $orderId,
                'cart_id' => $cartId,
                'error' => $e->getMessage()
            ]);

            abort(500, 'Dosya indirilemedi: ' . $e->getMessage());
        }
    }
}
