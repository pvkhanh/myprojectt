<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\WelcomeMail;
use App\Mail\VerifyEmailMail;

class MailService
{
    /**
     * Gửi email chào mừng user mới
     */
    public function sendWelcomeEmail(User $user): bool
    {
        try {
            Mail::to($user->email)->send(new WelcomeMail($user));
            return true;
        } catch (\Exception $e) {
            Log::error('Lỗi gửi Welcome Email', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Gửi email xác thực
     */
    public function sendEmailVerification(User $user, string $verificationUrl): bool
    {
        try {
            Mail::to($user->email)->send(new VerifyEmailMail($user, $verificationUrl));
            return true;
        } catch (\Exception $e) {
            Log::error('Lỗi gửi Email Verification', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
