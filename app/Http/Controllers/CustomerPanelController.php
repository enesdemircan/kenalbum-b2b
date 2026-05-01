<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Added this import for Hash facade
use App\Models\UserAddress;
use App\Models\User;

class CustomerPanelController extends Controller
{
  


    public function index()
    {
          return view('frontend.profile.index');
    }

    public function orders(Request $request)
    {
        $user = Auth::user();
        
        // Siparişleri çek
        $query = $user->orders()->with(['cartItems.product']);
        
        // Telefon numarasına göre filtreleme
        if ($request->filled('phone')) {
            $query->where('customer_phone', 'like', '%' . $request->phone . '%');
        }
        
        // Tarih aralığına göre filtreleme
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        // Siparişleri tarihe göre sırala (en yeni önce)
        $orders = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Filtre parametrelerini view'a gönder
        $filters = [
            'phone' => $request->phone,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        ];
        
        return view('frontend.profile.orders', compact('orders', 'filters'));
    }

    public function personels()
    {
        $user = Auth::user();
        
        // Kullanıcının role ID'si 3 mi kontrol et
        if (!$user->hasRole(3)) {
            abort(403, 'Bu sayfaya erişim yetkiniz yok.');
        }
        
        // Kullanıcının customer_id'si var mı kontrol et
        if (!$user->customer_id) {
            abort(403, 'Firma atamanız bulunmamaktadır.');
        }
        
        // Aynı customer_id'ye sahip ve role_id 2 olan kullanıcıları getir
        $personels = User::where('customer_id', $user->customer_id)
                        ->where('id', '!=', $user->id) // Kendisini hariç tut
                        ->whereHas('roles', function($query) {
                            $query->where('roles.id', 2); // Role ID 2 (Firma Personeli)
                        })
                        ->with('roles')
                        ->orderBy('created_at', 'desc')
                        ->get();
        
        return view('frontend.profile.personels', compact('personels'));
    }

    public function storePersonel(Request $request)
    {
        $user = Auth::user();
        
        // Kullanıcının role ID'si 3 mi kontrol et
        if (!$user->hasRole(3)) {
            abort(403, 'Bu sayfaya erişim yetkiniz yok.');
        }
        
        // Kullanıcının customer_id'si var mı kontrol et
        if (!$user->customer_id) {
            abort(403, 'Firma atamanız bulunmamaktadır.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        try {
            // Yeni kullanıcı oluştur
            $newUser = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'customer_id' => $user->customer_id, // Mevcut kullanıcının customer_id'si
                'status' => 1, // Aktif
            ]);
            
            // Role ID 2'yi ata (Firma Personeli)
            $newUser->roles()->attach(2);
            
            return redirect()->route('profile.personels')->with('success', 'Personel başarıyla eklendi!');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Personel eklenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function deletePersonel($id)
    {
        $user = Auth::user();
        
        // Kullanıcının role ID'si 3 mi kontrol et
        if (!$user->hasRole(3)) {
            abort(403, 'Bu sayfaya erişim yetkiniz yok.');
        }
        
        // Kullanıcının customer_id'si var mı kontrol et
        if (!$user->customer_id) {
            abort(403, 'Firma atamanız bulunmamaktadır.');
        }
        
        // Silinecek personeli bul
        $personel = User::where('id', $id)
                        ->where('customer_id', $user->customer_id)
                        ->where('id', '!=', $user->id) // Kendisini silemesin
                        ->whereHas('roles', function($query) {
                            $query->where('roles.id', 2); // Role ID 2 (Firma Personeli)
                        })
                        ->first();
        
        if (!$personel) {
            return redirect()->route('profile.personels')->with('error', 'Personel bulunamadı veya silme yetkiniz yok.');
        }
        
        try {
            // Personeli sil
            $personel->delete();
            return redirect()->route('profile.personels')->with('success', 'Personel başarıyla silindi!');
        } catch (\Exception $e) {
            return redirect()->route('profile.personels')->with('error', 'Personel silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function personelOrders($personelId)
    {
        $user = Auth::user();
        
        // Kullanıcının role ID'si 3 mi kontrol et
        if (!$user->hasRole(3)) {
            abort(403, 'Bu sayfaya erişim yetkiniz yok.');
        }
        
        // Kullanıcının customer_id'si var mı kontrol et
        if (!$user->customer_id) {
            abort(403, 'Firma atamanız bulunmamaktadır.');
        }
        
        // Personeli bul
        $personel = User::where('id', $personelId)
                        ->where('customer_id', $user->customer_id)
                        ->whereHas('roles', function($query) {
                            $query->where('roles.id', 2); // Role ID 2 (Firma Personeli)
                        })
                        ->first();
        
        if (!$personel) {
            abort(404, 'Personel bulunamadı.');
        }
        
        // Personelin siparişlerini getir
        $orders = \App\Models\Order::where('user_id', $personelId)
                                   ->with(['cartItems.product', 'statusHistories.orderStatus'])
                                   ->orderBy('created_at', 'desc')
                                   ->paginate(15);
        
        return view('frontend.profile.personel-orders', compact('personel', 'orders'));
    }

    public function detail()
    {
        $user = Auth::user();
        $customers = \App\Models\Customer::all();
        return view('frontend.profile.detail', compact('user', 'customers'));
    }

    public function updateDetail(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'current_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:8|confirmed',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        // Mevcut şifre kontrolü
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()->withErrors(['current_password' => 'Mevcut şifre yanlış.']);
            }
        }

        // Kullanıcı bilgilerini güncelle
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'customer_id' => $request->customer_id,
        ]);

        // Yeni şifre varsa güncelle
        if ($request->filled('new_password')) {
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);
        }

        return redirect()->route('profile.detail')->with('success', 'Hesap bilgileriniz başarıyla güncellendi!');
    }

    public function addresses()
    {
        $user = Auth::user();
        $companyAddresses = $user->addresses()->where('type', 'company')->orderBy('created_at', 'desc')->get();
        $customerAddresses = $user->addresses()->where('type', 'customer')->orderBy('created_at', 'desc')->get();
        return view('frontend.profile.addresses', compact('companyAddresses', 'customerAddresses'));
    }

    public function storeAddress(Request $request)
    {
        try {
            $validated = $request->validate([
                'type' => 'required|in:company,customer',
                'title' => 'required|string|min:2|max:255',
                'ad' => 'required|string|min:2|max:255|regex:/^[A-Za-zğüşıöçĞÜŞİÖÇ\s]+$/',
                'soyad' => 'required|string|min:2|max:255|regex:/^[A-Za-zğüşıöçĞÜŞİÖÇ\s]+$/',
                'city' => 'required|string|max:255',
                'district' => 'required|string|max:255',
                'adres' => 'required|string|min:10|max:1000',
                'telefon' => 'required|string|min:10|max:20|regex:/^[0-9\s\-\+\(\)]+$/'
            ], [
                'title.required' => 'Adres başlığı zorunludur.',
                'title.min' => 'Adres başlığı en az 2 karakter olmalıdır.',
                'title.max' => 'Adres başlığı 255 karakterden uzun olamaz.',
                'ad.required' => 'Ad alanı zorunludur.',
                'ad.min' => 'Ad en az 2 karakter olmalıdır.',
                'ad.max' => 'Ad 255 karakterden uzun olamaz.',
                'ad.regex' => 'Ad sadece harf ve boşluk karakterleri içerebilir.',
                'soyad.required' => 'Soyad alanı zorunludur.',
                'soyad.min' => 'Soyad en az 2 karakter olmalıdır.',
                'soyad.max' => 'Soyad 255 karakterden uzun olamaz.',
                'soyad.regex' => 'Soyad sadece harf ve boşluk karakterleri içerebilir.',
                'adres.required' => 'Adres alanı zorunludur.',
                'city.required' => 'İl seçimi zorunludur.',
                'city.max' => 'İl adı 255 karakterden uzun olamaz.',
                'district.required' => 'İlçe seçimi zorunludur.',
                'district.max' => 'İlçe adı 255 karakterden uzun olamaz.',
                'city.required' => 'İl seçimi zorunludur.',
                'city.max' => 'İl adı 255 karakterden uzun olamaz.',
                'district.required' => 'İlçe seçimi zorunludur.',
                'district.max' => 'İlçe adı 255 karakterden uzun olamaz.',
                'adres.min' => 'Adres en az 10 karakter olmalıdır.',
                'adres.max' => 'Adres 1000 karakterden uzun olamaz.',
                'telefon.required' => 'Telefon alanı zorunludur.',
                'telefon.min' => 'Telefon numarası en az 10 karakter olmalıdır.',
                'telefon.max' => 'Telefon numarası 20 karakterden uzun olamaz.',
                'telefon.regex' => 'Geçerli bir telefon numarası giriniz.'
            ]);

            $user = Auth::user();
            $address = $user->addresses()->create($validated);

            // AJAX request kontrolü
            if ($request->expectsJson()) {
                \Log::info('AJAX address save request', [
                    'user_id' => $user->id,
                    'address_id' => $address->id,
                    'data' => $validated
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Adres başarıyla eklendi!',
                    'address_id' => $address->id
                ]);
            }

            return redirect()->route('profile.addresses')->with('success', 'Adres başarıyla eklendi!');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation hatası',
                    'errors' => $e->errors()
                ], 422);
            }
            
            throw $e;
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bir hata oluştu: ' . $e->getMessage()
                ], 500);
            }
            
            throw $e;
        }
    }

    public function updateOrderDelivery(Request $request, $id)
    {
        $user = Auth::user();
        $order = $user->orders()->findOrFail($id);

        // Sadece "Onay Bekliyor" durumundaki siparislerin teslimat bilgileri duzenlenebilir
        if ((int) $order->status !== 0) {
            return redirect()->route('profile.orders')
                ->with('error', 'Bu sipariş için teslimat bilgileri artık düzenlenemez (sipariş onaylandı veya durumu değişti).');
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|min:2|max:255|regex:/^[A-Za-zğüşıöçĞÜŞİÖÇ\s]+$/',
            'customer_surname' => 'required|string|min:2|max:255|regex:/^[A-Za-zğüşıöçĞÜŞİÖÇ\s]+$/',
            'customer_phone' => 'required|string|min:10|max:20|regex:/^[0-9\s\-\+\(\)]+$/',
            'city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'shipping_address' => 'required|string|min:10|max:1000',
        ], [
            'customer_name.required' => 'Ad alanı zorunludur.',
            'customer_name.min' => 'Ad en az 2 karakter olmalıdır.',
            'customer_name.regex' => 'Ad sadece harf ve boşluk karakterleri içerebilir.',
            'customer_surname.required' => 'Soyad alanı zorunludur.',
            'customer_surname.min' => 'Soyad en az 2 karakter olmalıdır.',
            'customer_surname.regex' => 'Soyad sadece harf ve boşluk karakterleri içerebilir.',
            'customer_phone.required' => 'Telefon alanı zorunludur.',
            'customer_phone.min' => 'Telefon numarası en az 10 karakter olmalıdır.',
            'customer_phone.regex' => 'Geçerli bir telefon numarası giriniz.',
            'city.required' => 'İl seçimi zorunludur.',
            'district.required' => 'İlçe seçimi zorunludur.',
            'shipping_address.required' => 'Teslimat adresi zorunludur.',
            'shipping_address.min' => 'Teslimat adresi en az 10 karakter olmalıdır.',
            'shipping_address.max' => 'Teslimat adresi 1000 karakterden uzun olamaz.',
        ]);

        // Race condition koruma: update sirasinda statusu bir daha kontrol et
        $order->refresh();
        if ((int) $order->status !== 0) {
            return redirect()->route('profile.orders')
                ->with('error', 'Bu sipariş için teslimat bilgileri artık düzenlenemez.');
        }

        $order->update($validated);

        return redirect()->route('profile.orders')
            ->with('success', 'Teslimat bilgileri başarıyla güncellendi.');
    }

    public function updateAddress(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:company,customer',
            'title' => 'required|string|min:2|max:255',
            'ad' => 'required|string|min:2|max:255|regex:/^[A-Za-zğüşıöçĞÜŞİÖÇ\s]+$/',
            'soyad' => 'required|string|min:2|max:255|regex:/^[A-Za-zğüşıöçĞÜŞİÖÇ\s]+$/',
            'city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'adres' => 'required|string|min:10|max:1000',
            'telefon' => 'required|string|min:10|max:20|regex:/^[0-9\s\-\+\(\)]+$/'
        ], [
            'title.required' => 'Adres başlığı zorunludur.',
            'title.min' => 'Adres başlığı en az 2 karakter olmalıdır.',
            'title.max' => 'Adres başlığı 255 karakterden uzun olamaz.',
            'ad.required' => 'Ad alanı zorunludur.',
            'ad.min' => 'Ad en az 2 karakter olmalıdır.',
            'ad.max' => 'Ad 255 karakterden uzun olamaz.',
            'ad.regex' => 'Ad sadece harf ve boşluk karakterleri içerebilir.',
            'soyad.required' => 'Soyad alanı zorunludur.',
            'soyad.min' => 'Soyad en az 2 karakter olmalıdır.',
            'soyad.max' => 'Soyad 255 karakterden uzun olamaz.',
            'soyad.regex' => 'Soyad sadece harf ve boşluk karakterleri içerebilir.',
            'adres.required' => 'Adres alanı zorunludur.',
            'adres.min' => 'Adres en az 10 karakter olmalıdır.',
            'adres.max' => 'Adres 1000 karakterden uzun olamaz.',
            'telefon.required' => 'Telefon alanı zorunludur.',
            'telefon.min' => 'Telefon numarası en az 10 karakter olmalıdır.',
            'telefon.max' => 'Telefon numarası 20 karakterden uzun olamaz.',
            'telefon.regex' => 'Geçerli bir telefon numarası giriniz.'
        ]);

        $user = Auth::user();
        $address = $user->addresses()->findOrFail($id);
        $address->update($request->all());

        return redirect()->route('profile.addresses')->with('success', 'Adres başarıyla güncellendi!');
    }

    public function deleteAddress($id)
    {
        $user = Auth::user();
        $address = $user->addresses()->findOrFail($id);
        $address->delete();

        return redirect()->route('profile.addresses')->with('success', 'Adres başarıyla silindi!');
    }
}
