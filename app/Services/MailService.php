<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;
use App\Mail\OrderConfirmationMail;
use App\Mail\OrderStatusUpdateMail;
use App\Mail\PasswordResetMail;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderStatus;

class MailService
{
    /**
     * Yeni kullanıcı kaydı için hoş geldin e-postası gönder
     */
    public function sendWelcomeEmail(User $user): bool
    {
        try {
            Mail::to($user->email)->send(new WelcomeMail($user));
            return true;
        } catch (\Exception $e) {
            \Log::error('Welcome email sending failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Sipariş onay e-postası gönder
     */
    public function sendOrderConfirmationEmail(Order $order): bool
    {
        try {
            Mail::to($order->user->email)->send(new OrderConfirmationMail($order));
            return true;
        } catch (\Exception $e) {
            \Log::error('Order confirmation email sending failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Sipariş durumu güncelleme e-postası gönder
     */
    public function sendOrderStatusUpdateEmail(Order $order, OrderStatus $status, OrderStatus $previousStatus = null): bool
    {
        try {
            Mail::to($order->user->email)->send(new OrderStatusUpdateMail($order, $status, $previousStatus));
            return true;
        } catch (\Exception $e) {
            \Log::error('Order status update email sending failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Şifre sıfırlama e-postası gönder
     */
    public function sendPasswordResetEmail(User $user, string $resetUrl): bool
    {
        try {
            Mail::to($user->email)->send(new PasswordResetMail($user, $resetUrl));
            return true;
        } catch (\Exception $e) {
            \Log::error('Password reset email sending failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Toplu e-posta gönderimi (gelecekte kullanım için)
     */
    public function sendBulkEmail(array $emails, string $subject, string $view, array $data = []): bool
    {
        try {
            Mail::send($view, $data, function ($message) use ($emails, $subject) {
                $message->to($emails)->subject($subject);
            });
            return true;
        } catch (\Exception $e) {
            \Log::error('Bulk email sending failed: ' . $e->getMessage());
            return false;
        }
    }
} 