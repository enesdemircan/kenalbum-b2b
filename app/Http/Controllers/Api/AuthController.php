<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * API Login - Token Al
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ], [
                'email.required' => 'Email adresi gereklidir',
                'email.email' => 'Geçerli bir email adresi giriniz',
                'password.required' => 'Şifre gereklidir',
            ]);

            $user = User::where('email', $request->email)->first();

            // Kullanıcı kontrolü
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email veya şifre hatalı'
                ], 401);
            }

            // Kullanıcı aktif mi kontrolü
            if ($user->status != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hesabınız henüz onaylanmamış. Lütfen yönetici ile iletişime geçin.'
                ], 403);
            }

            // Role kontrolü - ID 11 API kullanıcısı grubu
            $hasApiRole = $user->roles()->where('roles.id', 11)->exists();
            
            if (!$hasApiRole) {
                return response()->json([
                    'success' => false,
                    'message' => 'API erişim yetkiniz yok. Bu hizmeti kullanabilmek için API kullanıcısı grubuna dahil olmanız gerekmektedir.'
                ], 403);
            }

            // Token oluştur - createToken metodunu kullanmaya çalış
            try {
                $token = $user->createToken('api-token', ['api-access'])->plainTextToken;
            } catch (\Error $e) {
                // createToken çalışmazsa manuel oluştur
                $tokenString = Str::random(64);
                $hashedToken = hash('sha256', $tokenString);
                
                DB::table('personal_access_tokens')->insert([
                    'tokenable_type' => 'App\Models\User',
                    'tokenable_id' => $user->id,
                    'name' => 'api-token',
                    'token' => $hashedToken,
                    'abilities' => json_encode(['api-access']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $tokenId = DB::getPdo()->lastInsertId();
                $token = $tokenId . '|' . $tokenString;
            }

            return response()->json([
                'success' => true,
                'message' => 'Giriş başarılı',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => null
                ]
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Doğrulama hatası',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Giriş yapılırken bir hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API Logout - Token İptal Et
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            // Mevcut token'ı sil
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Başarıyla çıkış yapıldı'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Çıkış yapılırken bir hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tüm Token'ları İptal Et
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logoutAll(Request $request)
    {
        try {
            // Kullanıcının tüm token'larını sil
            $request->user()->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tüm oturumlar sonlandırıldı'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'İşlem sırasında bir hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kullanıcı Bilgilerini Getir
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        try {
            $user = $request->user();
            $user->load('roles', 'customer');

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'status' => $user->status,
                    'roles' => $user->roles->pluck('name'),
                    'customer' => $user->customer ? [
                        'id' => $user->customer->id,
                        'unvan' => $user->customer->unvan,
                        'balance' => $user->customer->balance,
                    ] : null,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kullanıcı bilgileri alınırken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
