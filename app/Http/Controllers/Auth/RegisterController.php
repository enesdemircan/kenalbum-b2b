<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\Registered;
use App\Services\MailService;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'company_title' => 'required|string|max:255',
            'company_phone' => [
                'required',
                'string',
                'max:20',
                'unique:customers,phone',
                'regex:/^[0-9\s\-\+\(\)]+$/',
                function ($attribute, $value, $fail) {
                    // Sadece rakamları al
                    $digits = preg_replace('/[^0-9]/', '', $value);
                    
                    // Türkiye telefon numarası kontrolü (10-11 haneli)
                    if (strlen($digits) < 10 || strlen($digits) > 11) {
                        $fail('Telefon numarası 10-11 haneli olmalıdır.');
                    }
                    
                    // Türkiye alan kodu kontrolü (5 ile başlamalı)
                    if (strlen($digits) == 11 && substr($digits, 0, 1) != '0') {
                        $fail('Geçerli bir telefon numarası giriniz.');
                    }
                    
                    if (strlen($digits) == 10 && substr($digits, 0, 1) != '5') {
                        $fail('Geçerli bir telefon numarası giriniz.');
                    }
                }
            ],
        ], [
            'company_phone.regex' => 'Telefon numarası sadece rakam, boşluk, tire, artı ve parantez içerebilir.',
        ]);

        // Firma ID'yi race condition olmadan benzersiz üret (kilitle)
        $customer = Cache::lock('firma_id_generation', 10)->block(5, function () use ($request) {
            // Sayısal olarak en büyük firma numarasını al (string sıralama hatası olmasın)
            $maxNum = (int) DB::table('customers')
                ->selectRaw('COALESCE(MAX(CAST(SUBSTRING(firma_id, 6) AS UNSIGNED)), 0) as max_num')
                ->value('max_num');
            $nextNumber = $maxNum + 1;
            $firmaId = 'FIRMA' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            return \App\Models\Customer::create([
                'firma_id' => $firmaId,
                'unvan' => $request->company_title,
                'email' => $request->email,
                'phone' => $request->company_phone,
                'adres' => '',
                'vergi_dairesi' => '',
                'vergi_numarasi' => '',
            ]);
        });

        // Sonra user oluştur
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'customer_id' => $customer->id,
            'status' => 0, // Onay bekliyor
        ]);

        event(new Registered($user));
        Auth::login($user);

        // Hoş geldin e-postası gönder
        $mailService = new MailService();
        $mailService->sendWelcomeEmail($user);

        return redirect('/');
    }
}
