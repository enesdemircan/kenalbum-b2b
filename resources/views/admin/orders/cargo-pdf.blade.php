<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Kargo Etiketi - {{ $order->order_number }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { 
            margin: 0; 
            padding: 6px; 
            font-size: 10px; 
            line-height: 1.2;
            
            font-family: 'DejaVu Sans', Arial, sans-serif;
            @font-face {
            font-family: 'DejaVu Sans';
            src: url('{{ storage_path('fonts/DejaVuSans.ttf') }}') format('truetype');
            }
         
        }
        
        .header {   
            text-align: center; 
            border-bottom: 1px solid #000;
            padding-bottom: 4px; 
            margin-bottom: 4px; 
        }
        
        .company-name { 
         

            font-weight: bold; 
            margin-bottom: 3px; 
            color: #000;
        }
        
        .order-info
            margin-bottom: 4px; 
            color: #333;
        }
        
        .customer-info { 
            margin-bottom: 4px; 
        }
        
        .customer-info div { 
            margin-bottom: 2px; 
        }
        
        .label { 
            font-weight: bold; 
            display: inline-block; 
            width: 30px;
            color: #000;
        }
        
        .value { 
          
            display: inline-block;
            color: #333;
        }
         
        .barcode-section { 
            text-align: center; 
            margin-top: 4px; 
        }
        
        .barcode { 

            font-weight: bold; 
            letter-spacing: 1px; 
            color: #000;
        } 
        
        .footer { 
            text-align: center; 
            font-size: 7px; 
            margin-top: 4px; 
            border-top: 1px solid #000; 
            padding-top: 2px; 
            color: #666;
        }
        
        .urgent-badge {
            background-color: #ff4444;
            color: white;
            padding: 2px 4px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            margin-left: 5px;
        }
    </style>
</head>
<body>

    
    <!-- Müşteri bilgileri -->
    <div class="customer-info">
        <div><span class="value">{{ $order->customer_name }} {{ $order->customer_surname }}</span></div>
        <div><span class="value">{{ $order->customer_phone }}</span></div>
        <div><span class="value">{{ $order->shipping_address }}</span></div>
        <div><span class="value">{{ $order->city ?? 'Belirtilmemiş' }}/{{ $order->district ?? 'Belirtilmemiş' }}</span></div>
        @if($cart->cargo_customer)
        <div class="customer-info">
            <div><span class="value">{{ $cart->cargo_customer }}</span></div>
        </div>
    @endif
        <!-- Kargo barkodu -->
        @if($cart->cargo_barcode)
            <div class="barcode-section">
                <div class="barcode">{{ $cart->cargo_barcode }}</div>
            </div>
        @endif
    </div>
  

   

</body>
</html> 