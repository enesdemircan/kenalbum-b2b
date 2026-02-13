<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CargoService
{
    protected $config;

    public function __construct()
    {
        $this->config = config('cargo');
    }

    /**
     * Kargo firmasına göre kargo oluştur
     */
    public function createCargo($cargoCompany, $orderData)
    {
        switch ($cargoCompany) {
            case 'everest':
                return $this->createEverestCargo($orderData);
            case 'yurtici':
                return $this->createYurticiCargo($orderData);
            case 'kolay_gelsin':
                return $this->createKolayGelsinCargo($orderData);
            default:
                throw new \Exception('Geçersiz kargo firması: ' . $cargoCompany);
        }
    }

    /**
     * Everest Kargo entegrasyonu
     */
    protected function createEverestCargo($orderData)
    { 
        $url = 'https://webpostman.everestkargo.com/restapi/client/consignment/add';
       
        //payment type = 1 ise alıcı 2 ise satıcı öder
        $data = [
            'customer' => $orderData['customer_name'] . ' ' . $orderData['customer_surname'],
            'province_name' => $orderData['city'],
            'county_name' => $orderData['district'],
            'district' => 'Gülbahçesi',
            'address' => $orderData['shipping_address'],
            'telephone' => $orderData['customer_phone'],
            'branch_code' => 'IST', // İstanbul şube kodu
            'total_bulk' => 1, // Standart desi değeri
            'summary' => 'Albüm siparişi - ' . $orderData['order_number'] .' - '.$orderData['barcode'],
            'quantity' => 1,
            'consignment_type_id' => 2,
            'amount_type_id' =>  $orderData['payment_type'] == 2 ? 3 : 2, // Varsayılan değer 3 ise gönderici ödemeli 2 ise alıcıdan alınır 
            'add_service_type_id' => 2, // Varsayılan değer
            'order_number' => $orderData['order_number'],
            'output_number' => 1,
            'send_sms' => 1,
            'seller' => config('app.name', 'Albüm Satış Sistemi')
        ];
      
        if($orderData['payment_type'] == 2){
            $data['amount'] = $orderData['total_price'];
        }

        return $this->callEverestAPI('POST', $url, $data);
    }

    /**
     * Yurtiçi Kargo entegrasyonu
     */
    protected function createYurticiCargo($orderData)
    {
        try {
            // SOAP extension kontrolü
            if (!extension_loaded('soap')) {
                Log::error('SOAP extension yüklü değil. Yurtiçi Kargo API kullanılamıyor.');
                return [
                    'success' => false,
                    'message' => 'SOAP extension yüklü değil. Lütfen PHP SOAP extension\'ını etkinleştirin.',
                    'barcode' => null
                ];
            }
            if($orderData['payment_type'] == 1){
            // Yurtiçi Kargo kütüphanesini kullan
            $yurtici = new \yurticiKargo\yurticiKargo([
                'username' => config('cargo.yurticiAliciOdemeli.username'),
                'password' => config('cargo.yurticiAliciOdemeli.password'),
                'test'     => config('cargo.yurticiAliciOdemeli.test_mode', false)
            ]);
            }else{
            // Yurtiçi Kargo kütüphanesini kullan
            $yurtici = new \yurticiKargo\yurticiKargo([
                    'username' => config('cargo.yurticiGondericiOdemeli.username'),
                    'password' => config('cargo.yurticiGondericiOdemeli.password'),
                    'test'     => config('cargo.yurticiGondericiOdemeli.test_mode', false)
                ]);
            }
          
            // Kargo oluştur
            $cargoResult = $yurtici->createCargo([
                'cargoKey'         =>  $orderData['barcode'],
                'invoiceKey'       => 'ALBUM-' . $orderData['order_number'],
                'receiverCustName' => $orderData['customer_name'] . ' ' . $orderData['customer_surname'],
                'receiverAddress'  => $orderData['shipping_address'],
                'receiverPhone1'   => $orderData['customer_phone'],
                'cityName'          => $orderData['city'],
                'townName'          => $orderData['district'],
                'desi'             => 1, // Standart desi
                'piece'            => 1, // Adet
                'cargoType'        => 'KARGO', // Kargo türü
                'paymentType'      => $orderData['payment_type'] == 1 ? 'RECEIVER' : 'SENDER', // Gönderici ödemeli
                'isCod'            => 0, // Kapıda ödeme yok
                'codAmount'        => 0,
                'insuranceAmount'  => $orderData['total_price'], // Sigorta tutarı
                'description'      => 'Albüm siparişi - ' . $orderData['order_number']
            ]);
            
            Log::info('Yurtiçi Kargo API Response', [
                'request' => $orderData,
                'response' => $cargoResult
            ]);
            
            // Response'ı kontrol et - Object olarak geldiği için object notation kullan
            $cargoResult = (array) $cargoResult; // Object'i array'e çevir
            
            Log::info('Yurtiçi Kargo Response Array', [
                'response_array' => $cargoResult,
                'response_keys' => array_keys($cargoResult)
            ]);
            
            // Response'ı kontrol et - Yurtiçi Kargo'nun özel response yapısını parse et
            // Log'dan görüldüğü üzere çok nested bir yapı var
            $shippingResult = null;
            $shippingDetail = null;
            
            // Güvenli şekilde nested object'leri parse et
            if (isset($cargoResult['ShippingOrderResultVO'])) {
                $shippingResult = (array) $cargoResult['ShippingOrderResultVO'];
                
                if (isset($shippingResult['stdClass'])) {
                    $shippingResult = (array) $shippingResult['stdClass'];
                    
                    if (isset($shippingResult['shippingOrderDetailVO'])) {
                        $shippingDetail = (array) $shippingResult['shippingOrderDetailVO'];
                    }
                }
            }
            
            Log::info('Parsed Yurtiçi Kargo Response', [
                'shipping_result' => $shippingResult,
                'shipping_detail' => $shippingDetail
            ]);
            
            if ($shippingResult && isset($shippingResult['outFlag']) && $shippingResult['outFlag'] === '0') {
                // Başarılı - outFlag: "0" = Başarılı
                if (isset($shippingResult['shippingOrderDetailVO'])) {
                    $shippingDetail = (array) $shippingResult['shippingOrderDetailVO'];
                    $cargoKey = $shippingDetail['cargoKey'] ?? null;
                    $invoiceKey = $shippingDetail['invoiceKey'] ?? null;
                    
                    return [
                        'success' => true,
                        'barcode' => $cargoKey,
                        'record_id' => $invoiceKey,
                        'message' => 'Yurtiçi Kargo başarıyla oluşturuldu'
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Yurtiçi Kargo yanıtında gerekli bilgiler bulunamadı',
                        'barcode' => null
                    ];
                }
            } else {
                // Hata durumu - outFlag: "1" = Hata
                $errorMessage = 'Yurtiçi Kargo oluşturulamadı';
                
                // Hata mesajını kontrol et
                if (isset($shippingResult['outResult'])) {
                    $errorMessage = 'Yurtiçi Kargo Hatası: ' . $shippingResult['outResult'];
                }
                
                // shippingOrderDetailVO varsa içindeki hata mesajını da kontrol et
                if (isset($shippingResult['shippingOrderDetailVO'])) {
                    $shippingDetail = (array) $shippingResult['shippingOrderDetailVO'];
                    if (isset($shippingDetail['errMessage'])) {
                        $errorMessage = 'Yurtiçi Kargo Hatası: ' . $shippingDetail['errMessage'];
                    }
                }
                
                return [
                    'success' => false,
                    'message' => $errorMessage,
                    'barcode' => null
                ];
            }
            
        } catch (\Exception $e) {
            Log::error('Yurtiçi Kargo API hatası: ' . $e->getMessage(), [
                'order_data' => $orderData,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Yurtiçi Kargo API hatası: ' . $e->getMessage(),
                'barcode' => null
            ];
        }
    }

    /**
     * Kolay Gelsin Kargo entegrasyonu
     */
    protected function createKolayGelsinCargo($orderData)
    {
        try {
            Log::info('Kolay Gelsin Kargo API başlatılıyor', [
                'order_data' => $orderData
            ]);

            // 1. Token al
            $token = $this->getKolayGelsinToken();
            if (!$token) {
                return [
                    'success' => false,
                    'message' => 'Kolay Gelsin Kargo token alınamadı',
                    'barcode' => null
                ];
            }

            // 2. Kargo oluştur
            $cargoResult = $this->createKolayGelsinDelivery($token, $orderData);
            
            Log::info('Kolay Gelsin Kargo API Response', [
                'request' => $orderData,
                'response' => $cargoResult
            ]);

            // Response'ı kontrol et - API'den gelen field names'e göre parse et
            if (isset($cargoResult['StatusCode']) && $cargoResult['StatusCode'] === 200) {
                $result = $cargoResult['result'] ?? [];
                
                return [
                    'success' => true,
                    'barcode' => $result['BarcodeNumbers'][0] ?? null, // Array'in ilk elemanı
                    'tracking_number' => $result['TrackingNumber'] ?? null,
                    'tracking_url' => $result['TrackingUrl'] ?? null,
                    'barcode_zpl' => $result['BarcodeZpl'] ?? null, // Base64 barcode image
                    'record_id' => $result['ReferenceNo'] ?? null,
                    'message' => 'Kolay Gelsin Kargo başarıyla oluşturuldu'
                ];
            } else {
                // Hata durumu
                $errorMessage = 'Kolay Gelsin Kargo oluşturulamadı';
                
                if (isset($cargoResult['exceptionMessage'])) {
                    $errorMessage = 'Kolay Gelsin Kargo Hatası: ' . $cargoResult['exceptionMessage'];
                }
                
                return [
                    'success' => false,
                    'message' => $errorMessage,
                    'barcode' => null
                ];
            }

        } catch (\Exception $e) {
            Log::error('Kolay Gelsin Kargo API hatası: ' . $e->getMessage(), [
                'order_data' => $orderData,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Kolay Gelsin Kargo API hatası: ' . $e->getMessage(),
                'barcode' => null
            ];
        }
    }

    /**
     * Kolay Gelsin Kargo token alma
     */
    protected function getKolayGelsinToken()
    {
        try {
            $url = config('cargo.kolay_gelsin.base_url') . '/Token/LoginAES';
            
            $data = [
                'musteri' => config('cargo.kolay_gelsin.musteri'),
                'sifre' => config('cargo.kolay_gelsin.sifre')
            ];

            Log::info('Kolay Gelsin Token Request', [
                'url' => $url,
                'data' => $data
            ]);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post($url, $data);

            Log::info('Kolay Gelsin Token Response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $result = $response->json();
                
                // Token varsa kullan, IsSuccessful false olsa bile
                if (isset($result['result']['Token'])) {
                    Log::info('Kolay Gelsin Token alındı', [
                        'token' => substr($result['result']['Token'], 0, 50) . '...',
                        'customer_id' => $result['result']['CustomerId'] ?? null
                    ]);
                    return $result['result']['Token'];
                }
                
                Log::warning('Kolay Gelsin Token response\'da token bulunamadı', $result);
                return null;
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Kolay Gelsin Token hatası: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Sendeo API'den il ve ilçe ID'lerini çek
     */
    protected function getCityDistrictIds($cityName, $districtName)
    {
        try {
            // İl adını normalize et
            $normalizedCityName = $this->normalizeCityName($cityName);
            
            $url = 'https://api.sendeo.com.tr/api/Cargo/GetCityDistricts';
            $params = ['CityName' => $normalizedCityName];
            
            $response = Http::timeout(30)->get($url, $params);
            
            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['IsSuccessful'] && isset($data['result'])) {
                    $cityId = $data['result']['CityId'];
                    $districts = $data['result']['Districts'];
                    
                    // İlçe ID'sini bul
                    $districtId = null;
                    foreach ($districts as $district) {
                        // Türkçe karakterleri normalize et ve büyük/küçük harf farkını kaldır
                        $apiDistrictName = $this->normalizeTurkishText($district['DistrictName']);
                        $searchDistrictName = $this->normalizeTurkishText($districtName);
                        
                        if ($apiDistrictName === $searchDistrictName) {
                            $districtId = $district['DistrictId'];
                            break;
                        }
                    }
                    
                    Log::info('Sendeo API City/District Response', [
                        'original_city_name' => $cityName,
                        'normalized_city_name' => $normalizedCityName,
                        'district_name' => $districtName,
                        'city_id' => $cityId,
                        'district_id' => $districtId,
                        'response' => $data
                    ]);
                    
                    return [
                        'city_id' => $cityId,
                        'district_id' => $districtId
                    ];
                }
            }
            
            Log::warning('Sendeo API City/District hatası', [
                'city_name' => $cityName,
                'district_name' => $districtName,
                'response_status' => $response->status(),
                'response_body' => $response->body()
            ]);
            
            // Varsayılan değerleri döndür
            return [
                'city_id' => 34, // İstanbul
                'district_id' => 34139 // Varsayılan ilçe
            ];
            
        } catch (\Exception $e) {
            Log::error('Sendeo API City/District exception: ' . $e->getMessage());
            
            // Hata durumunda varsayılan değerleri döndür
            return [
                'city_id' => 34, // İstanbul
                'district_id' => 34139 // Varsayılan ilçe
            ];
        }
    }

    /**
     * Kolay Gelsin Kargo delivery oluşturma
     */
    protected function createKolayGelsinDelivery($token, $orderData)
    {
        try {
            // Kolay Gelsin'in kendi API endpoint'ini kullan
            $url = config('cargo.kolay_gelsin.api_url') . '/Cargo/SetDelivery';
            
            // İl ve ilçe ID'lerini dinamik olarak çek
            $cityDistrictIds = $this->getCityDistrictIds(
                $orderData['city'] ?? 'İstanbul', 
                $orderData['district'] ?? 'Kadıköy'
            );
            
            Log::info('Kolay Gelsin City/District IDs', [
                'order_city' => $orderData['city'] ?? 'İstanbul',
                'order_district' => $orderData['district'] ?? 'Kadıköy',
                'api_city_id' => $cityDistrictIds['city_id'],
                'api_district_id' => $cityDistrictIds['district_id']
            ]);
            
            $data = [
                'DeliveryType' => 1,
                'ReferenceNo' => $orderData['barcode'],
                'Description' => 'Albüm Siparişi - ' . $orderData['order_number'] . ' - ' . $orderData['barcode'],
                'Receiver' => $orderData['customer_name'] . ' ' . $orderData['customer_surname'],
                'ReceiverBranchCode' => 0,
                'ReceiverAuthority' => 'CUSTOMER',
                'ReceiverAddress' => $orderData['shipping_address'],
                'ReceiverCityId' => $cityDistrictIds['city_id'],
                'ReceiverDistrictId' => $cityDistrictIds['district_id'],
                'ReceiverPhone' => $orderData['customer_phone'],
                'ReceiverGSM' => $orderData['customer_phone'],
                'ReceiverEmail' => $orderData['customer_email'] ?? 'customer@example.com',
                'PaymentType' => 1,
                'CollectionType' => 0,
                'CollectionPrice' => 0,
                'DispatchNoteNumber' => $orderData['order_number'],
                'ServiceType' => 1,
                'BarcodeLabelType' => 2,
                'Products' => [
                    [
                        'Count' => 1,
                        'ProductCode' => '',
                        'Description' => 'Albüm Siparişi - ' . $orderData['order_number'],
                        'Deci' => 1,
                        'Price' => (int) $orderData['total_price']
                    ]
                ],
                'CustomerReferenceType' => 'ORDER-' . $orderData['order_number']
            ];

            // JSON formatını kontrol et
            $jsonData = json_encode($data, JSON_PRETTY_PRINT);
            
            Log::info('Kolay Gelsin Delivery Request', [
                'url' => $url,
                'data' => $data,
                'json_data' => $jsonData,
                'json_error' => json_last_error_msg()
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            ])->timeout(30)->post($url, $data);

            Log::info('Kolay Gelsin Delivery Response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'statusCode' => $response->status(),
                'exceptionMessage' => 'HTTP ' . $response->status() . ' hatası'
            ];

        } catch (\Exception $e) {
            Log::error('Kolay Gelsin Delivery hatası: ' . $e->getMessage());
            return [
                'statusCode' => 500,
                'exceptionMessage' => $e->getMessage()
            ];
        }
    }

    /**
     * Everest Kargo API çağrısı
     */
    protected function callEverestAPI($method, $url, $data = false)
    {
        // Config değerlerini debug için logla
        $auth = config('cargo.everest.authorization');
        $email = config('cargo.everest.from_email');
        
        Log::info('Everest Kargo Config Values', [
            'authorization' => $auth,
            'email' => $email,
            'config_path' => 'cargo.everest'
        ]);
        
        $headers = [
            'Authorization' => 'gjBTnR13tEwZ5XyLS2FmHJpWhOk6xUGdQP0YMqbN',
            'From' => 'mihra@everestkargo.com',
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];

        $userAgent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; tr; rv:1.9.0.6) Gecko/2009011913 Firefox/3.0.6';

        try {
            // Debug: API'ye gönderilen verileri logla
            Log::info('Everest Kargo API Request', [
                'url' => $url,
                'headers' => $headers,
                'data' => $data
            ]);
            
            // Everest Kargo API'si form data bekliyor, http_build_query kullan
            $formData = http_build_query($data);
            
            $response = Http::withHeaders($headers)
                ->withUserAgent($userAgent)
                ->withOptions([
                    'verify' => false,
                    'timeout' => 30
                ])
                ->withBody($formData, 'application/x-www-form-urlencoded')
                ->post($url);

            // Debug: API yanıtını logla
            Log::info('Everest Kargo API Response', [
                'status' => $response->status(),
                'body' => $response->body(),
                'headers' => $response->headers()
            ]);

            if ($response->successful()) {
                try {
                    $result = $response->json();
                    
                    // Debug: Response result'ı logla
                    Log::info('Everest Kargo Response Result', [
                        'result' => $result,
                        'result_type' => gettype($result)
                    ]);
                    
                    if (isset($result['error']) && $result['error'] === 'false') {
                        return [
                            'success' => true,
                            'barcode' => $result['barcode'] ?? null,
                            'record_id' => $result['record_id'] ?? null,
                            'message' => $result['result'] ?? 'Kargo başarıyla oluşturuldu'
                        ];
                    } else {
                        return [
                            'success' => false,
                            'message' => $result['result'] ?? 'Kargo oluşturulamadı',
                            'barcode' => null
                        ];
                    }
                } catch (\Exception $jsonError) {
                    Log::error('JSON Parse Error', [
                        'response_body' => $response->body(),
                        'error' => $jsonError->getMessage()
                    ]);
                    
                    return [
                        'success' => false,
                        'message' => 'API yanıtı işlenemedi: ' . $jsonError->getMessage(),
                        'barcode' => null
                    ];
                }
            } else {
                // 400 hatası için detaylı bilgi
                $errorBody = $response->body();
                $errorMessage = 'API yanıt vermedi: ' . $response->status();
                
                if ($response->status() == 400) {
                    $errorMessage .= ' - Bad Request. API\'ye gönderilen veriler hatalı olabilir.';
                    if ($errorBody) {
                        $errorMessage .= ' Detay: ' . $errorBody;
                    }
                }
                
                return [
                    'success' => false,
                    'message' => $errorMessage,
                    'barcode' => null
                ];
            }
        } catch (\Exception $e) {
            Log::error('Everest Kargo API hatası: ' . $e->getMessage(), [
                'url' => $url,
                'data' => $data,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Kargo API hatası: ' . $e->getMessage(),
                'barcode' => null
            ];
        }
    }

    /**
     * Adresten il adını çıkar
     */
    protected function extractProvince($address)
    {
        $provinces = [
            'Adana', 'Adıyaman', 'Afyonkarahisar', 'Ağrı', 'Amasya', 'Ankara', 'Antalya', 'Artvin', 'Aydın', 'Balıkesir',
            'Bilecik', 'Bingöl', 'Bitlis', 'Bolu', 'Burdur', 'Bursa', 'Çanakkale', 'Çankırı', 'Çorum', 'Denizli',
            'Diyarbakır', 'Edirne', 'Elazığ', 'Erzincan', 'Erzurum', 'Eskişehir', 'Gaziantep', 'Giresun', 'Gümüşhane',
            'Hakkari', 'Hatay', 'Isparta', 'Mersin', 'İstanbul', 'İzmir', 'Kars', 'Kastamonu', 'Kayseri', 'Kırklareli',
            'Kırşehir', 'Kocaeli', 'Konya', 'Kütahya', 'Malatya', 'Manisa', 'Kahramanmaraş', 'Mardin', 'Muğla', 'Muş',
            'Nevşehir', 'Niğde', 'Ordu', 'Rize', 'Sakarya', 'Samsun', 'Siirt', 'Sinop', 'Sivas', 'Tekirdağ', 'Tokat',
            'Trabzon', 'Tunceli', 'Şanlıurfa', 'Uşak', 'Van', 'Yozgat', 'Zonguldak', 'Aksaray', 'Bayburt', 'Karaman',
            'Kırıkkale', 'Batman', 'Şırnak', 'Bartın', 'Ardahan', 'Iğdır', 'Yalova', 'Karabük', 'Kilis', 'Osmaniye', 'Düzce'
        ];

        foreach ($provinces as $province) {
            if (stripos($address, $province) !== false) {
                return $province;
            }
        }

        return 'İstanbul'; // Varsayılan
    }

    /**
     * Adresten ilçe adını çıkar
     */
    protected function extractCounty($address)
    {
        // Basit ilçe çıkarma - gerçek uygulamada daha gelişmiş olabilir
        $counties = [
            'Kadıköy', 'Beşiktaş', 'Şişli', 'Beyoğlu', 'Fatih', 'Üsküdar', 'Maltepe', 'Pendik', 'Kartal', 'Tuzla',
            'Çekmeköy', 'Sancaktepe', 'Sultanbeyli', 'Ataşehir', 'Ümraniye', 'Beykoz', 'Çatalca', 'Silivri', 'Büyükçekmece',
            'Küçükçekmece', 'Avcılar', 'Esenyurt', 'Başakşehir', 'Sultangazi', 'Gaziosmanpaşa', 'Kağıthane', 'Sarıyer'
        ];

        foreach ($counties as $county) {
            if (stripos($address, $county) !== false) {
                return $county;
            }
        }

        return 'Kadıköy'; // Varsayılan
    }

    /**
     * Adresten mahalle adını çıkar
     */
    protected function extractDistrict($address)
    {
        // Basit mahalle çıkarma - gerçek uygulamada daha gelişmiş olabilir
        $districts = [
            'Fenerbahçe', 'Caddebostan', 'Göztepe', 'Erenköy', 'Suadiye', 'Bostancı', 'Küçükyalı', 'Maltepe', 'Pendik',
            'Kartal', 'Tuzla', 'Ataşehir', 'Ümraniye', 'Beykoz', 'Çekmeköy', 'Sancaktepe', 'Sultanbeyli'
        ];

        foreach ($districts as $district) {
            if (stripos($address, $district) !== false) {
                return $district;
            }
        }

        return 'Fenerbahçe'; // Varsayılan mahalle
    }

    /**
     * Türkçe metni normalize et
     */
    protected function normalizeTurkishText($text)
    {
        // Türkçe karakterleri küçük harfe çevir
        $text = mb_strtolower($text, 'UTF-8');
        // Boşlukları ve özel karakterleri temizle
        $text = preg_replace('/[^a-z0-9]/', '', $text);
        return $text;
    }

    /**
     * İl adını normalize et
     */
    protected function normalizeCityName($cityName)
    {
        $normalizedCityName = $this->normalizeTurkishText($cityName);
        // Özel durumları kontrol et
        if (stripos($cityName, 'istanbul') !== false) {
            return 'istanbul';
        }
        if (stripos($cityName, 'izmir') !== false) {
            return 'izmir';
        }
        if (stripos($cityName, 'ankara') !== false) {
            return 'ankara';
        }
        if (stripos($cityName, 'adana') !== false) {
            return 'adana';
        }
        if (stripos($cityName, 'antalya') !== false) {
            return 'antalya';
        }
        if (stripos($cityName, 'bursa') !== false) {
            return 'bursa';
        }
        if (stripos($cityName, 'gaziantep') !== false) {
            return 'gaziantep';
        }
        if (stripos($cityName, 'kayseri') !== false) {
            return 'kayseri';
        }
        if (stripos($cityName, 'konya') !== false) {
            return 'konya';
        }
        if (stripos($cityName, 'mersin') !== false) {
            return 'mersin';
        }
        if (stripos($cityName, 'samsun') !== false) {
            return 'samsun';
        }
        if (stripos($cityName, 'siirt') !== false) {
            return 'siirt';
        }
        if (stripos($cityName, 'trabzon') !== false) {
            return 'trabzon';
        }
        if (stripos($cityName, 'van') !== false) {
            return 'van';
        }
        if (stripos($cityName, 'yozgat') !== false) {
            return 'yozgat';
        }
        if (stripos($cityName, 'zonguldak') !== false) {
            return 'zonguldak';
        }
        return $normalizedCityName;
    }
} 