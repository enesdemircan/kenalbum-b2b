<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    /**
     * Firma adına giriş yap (impersonate)
     */
    public function start($customerId)
    {
        $currentUser = Auth::user();

        // Sadece Administrator ve Satış Müdürü kullanabilir
        if (!$currentUser->hasRole('Administrator') && !$currentUser->hasRole('Satış Müdürü')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        // Zaten impersonate ediyorsa izin verme
        if (session()->has('impersonator_id')) {
            return redirect()->back()->with('error', 'Zaten bir firma adına giriş yapmış durumdasınız.');
        }

        $customer = Customer::findOrFail($customerId);

        // Firmanın ilk aktif kullanıcısını bul
        $targetUser = User::where('customer_id', $customer->id)
            ->where('status', 1)
            ->first();

        if (!$targetUser) {
            return redirect()->back()->with('error', 'Bu firmaya ait aktif kullanıcı bulunamadı.');
        }

        // Orijinal kullanıcıyı session'a kaydet
        session()->put('impersonator_id', $currentUser->id);
        session()->put('impersonator_name', $currentUser->name);

        // Hedef kullanıcıya geçiş yap
        Auth::loginUsingId($targetUser->id);

        return redirect('/')->with('success', $customer->unvan . ' adına giriş yapıldı.');
    }

    /**
     * Kendi hesabına geri dön
     */
    public function stop()
    {
        $impersonatorId = session()->get('impersonator_id');

        if (!$impersonatorId) {
            return redirect('/');
        }

        // Session'daki impersonation verilerini temizle
        session()->forget('impersonator_id');
        session()->forget('impersonator_name');

        // Orijinal kullanıcıya geri dön
        Auth::loginUsingId($impersonatorId);

        return redirect()->route('admin.dashboard')->with('success', 'Kendi hesabınıza geri döndünüz.');
    }
}
