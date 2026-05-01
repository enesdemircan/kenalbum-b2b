<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Yazdır - {{ $order->order_number }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
                @font-face {
            font-family: 'DejaVu Sans';
            src: url('{{ storage_path('fonts/DejaVuSans.ttf') }}') format('truetype');
        }
            

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;

            margin: 0;
            padding: 0;
            font-size: 10px;
            line-height: 1.1;
        }
        .page {
            page-break-after: always;
            margin-bottom: 5px;
        }
        .page:last-child {
            page-break-after: avoid;
        }
        .header {
            text-align: center;
            margin-bottom: 5px;
            border-bottom: 1px solid #333;
            padding-bottom: 3px;
        }
        .header h2 {
            margin: 0 0 3px 0;
            font-size: 14px;
        }
        .header p {
            margin: 0;
            font-size: 10px;
        }
        .barcode-section {
            text-align: left;
            margin: 5px 0;
        }
        .barcode-image {
            max-width: 250px;
            height: auto;
            display: block;
            margin: 5px 0;
        }
        .barcode-text {
            font-family: monospace;
            font-size: 10px;
            margin-top: 3px;
            font-weight: bold;
        }
        .cart-id-text {
            font-family: monospace;
            font-size: 18px;
            margin: 5px 0;
            font-weight: bold;
            color: #000;
            border: 1px solid #000;
            padding: 6px 8px;
            letter-spacing: 0.5px;
            background-color: #f9f9f9;
        }
        
        .cart-images-section {
            margin: 10px 0;
            border: 1px solid #333;
            padding: 8px;
            background-color: #f8f9fa;
        }
        
        .cart-images-section h4 {
            margin: 0 0 8px 0;
            font-size: 11px;
            color: #333;
        }
        
        .images-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        
        .image-item {
            text-align: center;
            border: 1px solid #333;
            padding: 4px;
            background-color: white;
        }
        
        .cart-image {
            max-width: 80px;
            max-height: 60px;
            width: 80px;
            height: 60px;
            object-fit: cover;
            border: 1px solid #eee;
            display: block;
        }
        
        .image-info {
            margin-top: 3px;
            font-size: 8px;
            color: #666;
        }
        .info-section {
            margin: 5px 0;
            display: flex;
            flex-direction: column;
        }
        .info-row {
            display: flex !important;
            margin-bottom: 3px;
            align-items: center;
            flex-direction: row;
        }
        .info-label {
            font-weight: bold;
            width: 120px;
            font-size: 10px;
            flex-shrink: 0;
            display: inline-block;
        }
        .info-value {
            flex: 1;
            font-size: 10px;
            display: inline-block;
            margin-left: 5px;
        }
        .info-row span {
            display: inline-block;
        }
        .customization-section {
            margin: 5px 0;
           
            padding: 5px;
           
        }
        .customization-title {
            font-weight: bold;
            margin-bottom: 3px;
            color: #333;
            font-size: 9px; 
        }
        .customization-item {
            margin-bottom: 2px;
            padding: 1px 0;
            font-size: 10px;
        }
        .shipping-info {
            margin: 5px 0;
          
            padding: 5px;
            background-color: #f0f0f0;
        }
        .shipping-title {
            font-weight: bold;
            margin-bottom: 3px;
            color: #333;
            font-size: 9px;
        }
        .footer {
            margin-top: 10px;
            text-align: center;
            font-size: 7px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 3px;
        }
    </style>
</head>
<body>
    @foreach($order->cartItems as $index => $item)
        <div class="page">

            <div class="row">
                <div class="col-md-6">
                    <!-- Cart ID Section -->
                    <div class="barcode-section">
                        @if($item->cart_id)
                            <div class="cart-id-text">{{ $item->cart_id }}</div>
                        @endif
                    </div>
                     <!-- Barcode Section -->
                     <div class="barcode-section">
                        @if($item->barcode)
                            <img src="{{ \App\Helpers\BarcodeHelper::generateBarcodeImage($item->barcode) }}" 
                                alt="Barcode" class="barcode-image">
                            <div class="barcode-text">{{ $item->barcode }}</div>
                            <br>
                        @endif
                    </div>
                             
                                        <!-- Cart Images Section -->
                                        @if($item->cart_id)
                                        @php
                                            $cart = \App\Models\Cart::find($item->cart_id);
                                            $images = [];
                                            if ($cart && !empty($cart->images)) {
                                                $images = explode(',', $cart->images);
                                            }
                                        @endphp
                                        
                                        @if(count($images) > 0)
                                     
                                                    @foreach(array_slice($images, 0, 3) as $index => $imagePath)
                                                        @php
                                                            $fullPath = storage_path('app/public/' . $imagePath);
                                                            if (file_exists($fullPath)) {
                                                                $imageData = base64_encode(file_get_contents($fullPath));
                                                                $mimeType = mime_content_type($fullPath);
                                                            }
                                                        @endphp
                                                        @if(isset($imageData))
                                                          
                                                                <img src="data:{{ $mimeType }};base64,{{ $imageData }}" 
                                                                    alt="Cart Image {{ $index + 1 }}" 
                                                                    class="cart-image">
                                                         
                                                    
                                                        @endif
                                                    @endforeach
                                   
                                           
                                        @endif
                                    @endif
                </div>
                <div class="col-md-6">
                       

                           <!-- Company and Product Info -->
                            <div class="info-section">
                                <div class="info-row">
                                    <span class="info-label">Firma Ünvanı:</span>
                                    <span class="info-value">
                                        @if($item->user && $item->user->customer)
                                            {{ $item->user->customer->unvan }}
                                        @else 
                                            -
                                        @endif
                                    </span>
                                </div>
 
                                <div class="info-row">
                                    <span class="info-label">Sipariş No:</span>
                                    <span class="info-value">{{ $order->order_number }}</span>
                                </div>
                                @if(!empty($order->api_archive_code))
                                    <div class="info-row">
                                        <span class="info-label">D.Sipariş No:</span>
                                        <span class="info-value">{{ $order->api_archive_code }}</span>
                                    </div>
                                @endif
                                <div class="info-row">
                                    <span class="info-label">Tarih:</span>
                                    <span class="info-value">{{ $order->created_at->format('d.m.Y H:i') }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Adet ve Sayfa Sayısı</span>
                                    <span class="info-value">{{ $item->quantity }} - {{ $item->page_count ?? '-' }} </span>
                                </div>

                            </div>

                </div>
           
           
          
         
            <!-- Customization Details -->
            @if($item->notes)
                @php
                    $notes = json_decode($item->notes, true);
                    $customizations = $notes['customizations'] ?? [];
                @endphp
                @if($customizations)
                    <div style="width: 50%; float: left;" class="customization-section">
                        <div class="customization-title">Albüm Üretim Detayları:</div>
                        <div class="customization-item">
                            <strong>Albüm Modeli:</strong>
                                    <span class="text-muted">{{ $item->product->title }}</span>
                        </div> 
                        @foreach($customizations as $categoryId => $customization)
                            @php
                                $category = \App\Models\CustomizationCategory::find($categoryId);
                                $type = $customization['type'] ?? '';
                                $value = $customization['value'] ?? '';
                                $values = $customization['values'] ?? [];
                            @endphp
                            @if($category && $type !== 'files') 
                                <div class="customization-item">
                                    <strong>{{ $category->title }}:</strong>
                                    @if($type == 'radio' || $type == 'select' || $type == 'hidden')
                                        @if(is_numeric($value))
                                            @php
                                                $pivotParam = \App\Models\CustomizationPivotParam::with('param')->find($value);
                                            @endphp
                                            @if($pivotParam && $pivotParam->param)
                                                <span class="text-muted">{{ $pivotParam->param->key }}</span>
                                            @else
                                                <span class="text-muted">Seçim yapılmadı</span>
                                            @endif
                                        @else
                                            <span class="text-muted">{{ $value }}</span>
                                        @endif
                                    @elseif($type == 'checkbox')
                                        @if(is_array($values) && count($values) > 0)
                                            @php
                                                $selectedOptions = [];
                                                foreach($values as $pivotId) {
                                                    $pivotParam = \App\Models\CustomizationPivotParam::find($pivotId);
                                                    if($pivotParam && $pivotParam->param) {
                                                        $selectedOptions[] = $pivotParam->param->key;
                                                    }
                                                }
                                            @endphp
                                            <span class="text-muted">{{ implode(', ', $selectedOptions) }}</span>
                                        @else
                                            <span class="text-muted">Seçim yapılmadı</span>
                                        @endif
                                    @elseif($type == 'input')
                                        @php
                                            $inputValue = $value;
                                            if (is_array($inputValue)) {
                                                $inputValue = json_encode($inputValue);
                                            }
                                            $isEmpty = empty($inputValue) || $inputValue === 'null' || $inputValue === '{"data":null}';
                                        @endphp
                                        @if(!$isEmpty)
                                            <span class="text-muted">{{ $inputValue }}</span>
                                        @else
                                            <span class="text-muted">Girilmedi</span>
                                        @endif
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            @endif



            <!-- Shipping Information -->
            <div style="width: 50%; float: left;" class="shipping-info">
                <div class="shipping-title">Teslimat Bilgileri:</div>
                <div class="info-row">
                    <div class="info-label">Müşteri Adı:</div>
                    <div class="info-value">{{ $order->customer_name }} {{ $order->customer_surname }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Telefon:</div>
                    <div class="info-value">{{ $order->customer_phone }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">İl/İlçe:</div>
                    <div class="info-value">{{ $order->city ?? 'Belirtilmemiş' }} / {{ $order->district ?? 'Belirtilmemiş' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Teslimat Adresi:</div>
                    <div class="info-value">{{ $order->shipping_address }}</div>
                </div>

            </div>

            @php
                $cartNotes = null;
                $urgentProduction = false;
                $designService = null;
                if ($item->notes) {
                    $parsedNotes = json_decode($item->notes, true);
                    $cartNotes = $parsedNotes['order_note'] ?? null;
                    $urgentProduction = !empty($parsedNotes['urgent_production']);
                    $designService = $parsedNotes['design_service'] ?? null;
                }
            @endphp
            @if(!empty($cartNotes) || $urgentProduction || $designService)
                <div style="width: 100%; clear: both; margin-top: 10px;" class="cart-notes-section">
                    <div class="customization-title">Sipariş Notu:</div>
                    @if($urgentProduction)
                        <div class="customization-item">
                            <strong>Acil Üretim:</strong>
                            <span class="text-muted">Evet</span>
                        </div>
                    @endif
                    @if($designService === 'with_design')
                        <div class="customization-item">
                            <strong>Tasarım Hizmeti:</strong>
                            <span class="text-muted">Tasarımı bize yaptır (+{{ number_format($item->product->design_service_price ?? 0, 2) }} ₺)</span>
                        </div>
                    @elseif($designService === 'self_design')
                        <div class="customization-item">
                            <strong>Tasarım Hizmeti:</strong>
                            <span class="text-muted">Müşteri kendi tasarımını yükleyecek</span>
                        </div>
                    @endif
                    @if(!empty($cartNotes))
                        <div class="customization-item">
                            <span class="text-muted">{{ $cartNotes }}</span>
                        </div>
                    @endif
                </div>
            @endif


        </div>
        </div>
    @endforeach
</body>
</html> 