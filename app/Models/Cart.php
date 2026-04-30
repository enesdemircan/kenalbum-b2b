<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\DiscountGroup;
use App\Models\User;
use App\Models\Product;

class Cart extends Model
{
    use HasFactory; 

    protected $fillable = [ 
        'user_id',
        'product_id',
        'quantity',
        'page_count',
        'price',
        'original_price',
        'notes',
        'images', 
        'local_zip',
        's3_zip',
        'cart_id',
        'barcode',
        'cargo_barcode',
        'tracking_url',
        'barcode_zpl',
        'status',
        'order_id',
        'discount_group_id',
        'cargo_customer'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
    ];

    /**
     * Notes alanını JSON olarak parse eder
     */
    public function getParsedNotesAttribute()
    {
        if ($this->notes) {
            return json_decode($this->notes, true);
        }
        return null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function discountGroup()
    {
        return $this->belongsTo(DiscountGroup::class);
    }



    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class, 'cart_id');
    }

    public function currentStatus()
    {
        return $this->hasOne(OrderStatusHistory::class, 'cart_id')->latest();
    }

    // cartFiles ilişkisi kaldırıldı - direkt veritabanı sorgusu kullanılacak

    /**
     * Images alanından thumbnail URL'lerini array olarak döndür
     */
    public function getThumbnailUrlsAttribute()
    {
        if (!$this->images) {
            return [];
        }
        
        return array_filter(array_map('trim', explode(',', $this->images)));
    }

    // Sepet toplam fiyatını hesaplar
    public function getTotalPriceAttribute()
    {
        return $this->price * $this->quantity;
    }

    // Kullanıcının geçerli indirimlerini hesaplar
    public function calculateDiscount()
    {
        // Eğer cart henüz save edilmemişse, user_id ve product_id kullanarak hesapla
        if (!$this->exists) {
            if (!$this->user_id || !$this->product_id) {
                return 0;
            }
            
            $user = User::find($this->user_id);
            $product = Product::find($this->product_id);
        } else {
            // Cart save edilmişse, ilişkileri kullan
            if (!$this->user || !$this->product) {
                return 0;
            }
            $user = $this->user;
            $product = $this->product;
        }
        
        if (!$user || !$product) {
            return 0;
        }
        
        // Kullanıcının customer_id'si var mı kontrol et
        if (!$user->customer_id) {
            return 0;
        }

        // Kullanıcının firmasına ait aktif indirim gruplarını bul
        $validDiscounts = \App\Models\DiscountGroup::getValidDiscountsForUser($user->id, $product->main_category_id);
        
        if ($validDiscounts->isEmpty()) {
            return 0;
        }

        // En yüksek indirim oranını al
        $maxDiscount = $validDiscounts->max('discount_percentage');
        
        return $maxDiscount;
    }

    // İndirimli fiyatı hesaplar
    public function getDiscountedPriceAttribute()
    {
        $discountPercentage = $this->calculateDiscount();
        
        if ($discountPercentage > 0) {
            return $this->original_price * (1 - $discountPercentage / 100);
        }
        
        return $this->original_price;
    }

    // İndirim tutarını hesaplar
    public function getDiscountAmountAttribute()
    {
        $discountPercentage = $this->calculateDiscount();
        
        if ($discountPercentage > 0) {
            return $this->original_price * ($discountPercentage / 100);
        }
        
        return 0;
    }

        /**
     * Generate cart identifier (special format for internal use)
     * Format: cart_id - date (YYMMDD) - customer_unvan - size - product_title - page_count
     */
    public function generateCartIdentifier($orderNumber = null)
    {
        // Cart ID (sipariş numarası varsa onu kullan, yoksa DB id)
        $cartId = $orderNumber ?? $this->id;

        // Date in YYMMDD format
        $date = $this->created_at->format('ymd');

        // Customer unvan (firma unvanı) - clean with Str::slug
        $customerUnvan = '';
        if ($this->user && $this->user->customer) {
            $customerUnvan = \Illuminate\Support\Str::slug($this->user->customer->unvan, '-');
        }

        // Notes'u bir kez decode et
        $notes = null;
        $customizations = [];
        if ($this->notes) {
            $notes = json_decode($this->notes, true);
            if (isset($notes['customizations']) && is_array($notes['customizations'])) {
                $customizations = $notes['customizations'];
            }
        }

        // Size (ebat) - TÜM kategorilerde "Ebat" başlığını ara
        $size = '';
        if (!empty($customizations)) {
            // Her kategoriyi kontrol et
            foreach ($customizations as $categoryId => $categoryData) {
                // Kategoriyi bul
                $category = \App\Models\CustomizationCategory::find($categoryId);
                
                // "Ebat" kategorisi mi?
                if ($category && $category->title === 'Ebat') {
                    // Type'a göre pivot_id'yi al
                    $pivotId = null;
                    if (is_array($categoryData) && isset($categoryData['type'])) {
                        if (($categoryData['type'] === 'radio' || $categoryData['type'] === 'select') && isset($categoryData['value'])) {
                            $pivotId = $categoryData['value'];
                        } elseif ($categoryData['type'] === 'checkbox' && isset($categoryData['values'][0])) {
                            $pivotId = $categoryData['values'][0];
                        }
                    }
                    
                    // Pivot param bulup param.key al
                    if ($pivotId) {
                        $pivotParam = \App\Models\CustomizationPivotParam::with('param')->find($pivotId);
                        if ($pivotParam && $pivotParam->param) {
                            $paramKey = $pivotParam->param->key;
                            // Sadece boyut kısmını al (örn: "20x30 Albüm" -> "20x30")
                            $size = explode(' ', $paramKey)[0];
                            break; // Ebat bulundu, döngüden çık
                        }
                    }
                }
            }
        }

        // Product title - temizle
        $productTitle = '';
        if ($this->product) {
            $productTitle = \Illuminate\Support\Str::slug($this->product->title, '-');
        }

        // Page count - sadece sayı olarak
        $pageCount = $this->page_count ?? '';

        // İsimler - "Albüm Üzerine Yazılacak Yazı" (category_id=8)
        $isimler = '';
        if (isset($customizations[8])) {
            $categoryData = $customizations[8];
            // Value varsa ve boş değilse
            if (is_array($categoryData) && isset($categoryData['value']) && !empty($categoryData['value'])) {
                // Value direkt string ise (input field'dan geliyor)
                $isimler = \Illuminate\Support\Str::slug($categoryData['value'], '-');
            }
        }

        // PVC Kalınlığı - (category_id=13)
        $pvcKalinlik = '';
        if (isset($customizations[13])) {
            $categoryData = $customizations[13];
            // Value varsa (pivot_id olarak)
            if (is_array($categoryData) && isset($categoryData['value'])) {
                $pivotId = $categoryData['value'];
                // Pivot param bulup title al
                $pivotParam = \App\Models\CustomizationPivotParam::find($pivotId);
                if ($pivotParam) {
                    // "İnce Pvc" veya "Kalın Pvc" -> slug hali
                    $pvcKalinlik = \Illuminate\Support\Str::slug($pivotParam->title, '-');
                }
            }
        }

        // Acil üretim durumu
        $acil = '';
        if ($this->urgent_status == 1) {
            $acil = 'acil';
        }

        // Tüm parçaları temizle ve birleştir
        $identifier = implode('-', array_filter([
            $cartId,
            $date,
            $customerUnvan,
            \Illuminate\Support\Str::slug($size, '-'),
            $productTitle,
            $pageCount,
            $isimler,
            $pvcKalinlik,
            $acil
        ], function($value) {
            return $value !== '' && $value !== null;
        }));

        return $identifier;
    }

    /**
     * Generate unique barcode number (standard format)
     * Format: 13-digit unique barcode for CODE-128
     */
    public function generateUniqueBarcode()
    {
        // Generate a unique 13-digit barcode
        // Using CODE-128 format which supports any length
        $uniqueNumber = sprintf('%013d', $this->id * 10000 + rand(0, 9999));
        
        // Return 13-digit barcode
        return $uniqueNumber;
    }

    /**
     * Cart_id slug değişince R2'deki object key'lerini yeniden adlandır
     * (CopyObject + DeleteObject — sunucu transferi yok, ZIP içeriği dokunulmaz).
     * Eski Spaces URL'leri (geçmiş siparişler) dokunulmadan bırakılır.
     */
    public function renameS3Zip($oldCartId, $newCartId)
    {
        if (empty($this->s3_zip) || $oldCartId === $newCartId) {
            return;
        }

        $r2 = app(\App\Services\R2UploadService::class);
        $publicBase = rtrim((string) env('R2_PUBLIC_LINK', ''), '/');
        $existingUrls = array_filter(array_map('trim', explode(',', (string) $this->s3_zip)));
        $newUrls = [];
        $renamed = 0;

        foreach ($existingUrls as $url) {
            if ($publicBase === '' || !str_starts_with($url, $publicBase . '/')) {
                $newUrls[] = $url;
                continue;
            }

            $oldKey = $r2->extractKeyFromUrl($url);
            if (!$oldKey) {
                $newUrls[] = $url;
                continue;
            }

            $newKey = $r2->renameSlugInKey($oldKey, $oldCartId, $newCartId, (int) $this->id);
            if ($newKey === null) {
                $newUrls[] = $url;
                continue;
            }

            $newUrls[] = $r2->publicUrl($newKey);
            $renamed++;
        }

        if ($renamed > 0) {
            $this->update(['s3_zip' => implode(',', $newUrls)]);
            Log::info('R2 object keys renamed for new cart_id', [
                'cart_id' => $this->id,
                'old_slug' => $oldCartId,
                'new_slug' => $newCartId,
                'renamed_count' => $renamed,
            ]);
        }
    }

    /**
     * Delete all associated files from S3 and local storage
     */
    public function deleteAssociatedFiles()
    {
        // S3 zip dosyasını sil (eğer varsa)
        if ($this->s3_zip) {
            $disk = \Storage::disk('s3');
            try {
                $disk->delete($this->s3_zip);
                \Log::info('Cart item silinirken S3 zip dosyası da silindi:', [
                    'cart_id' => $this->id,
                    's3_zip' => $this->s3_zip
                ]);
            } catch (\Exception $e) {
                \Log::warning('S3 zip dosyası silinirken hata:', [
                    'cart_id' => $this->id,
                    's3_zip' => $this->s3_zip,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Images alanındaki dosyaları sil (eğer varsa)
        if ($this->images) {
            $imageUrls = $this->getThumbnailUrlsAttribute();
            foreach ($imageUrls as $imageUrl) {
                // Local dosyayı sil
                $filePath = storage_path('app/public/' . str_replace('storage/', '', $imageUrl));
                if (file_exists($filePath)) {
                    unlink($filePath);
                    \Log::info('Cart item silinirken images local dosya da silindi:', [
                        'cart_id' => $this->id,
                        'file_path' => $filePath
                    ]);
                }
            }
        } 

        // S3_zip değerini temizle (cart kaydını güncelle)
        $this->update(['s3_zip' => null]);
        
        \Log::info('Cart item s3_zip değeri temizlendi:', [
            'cart_id' => $this->id
        ]);
    }
}
