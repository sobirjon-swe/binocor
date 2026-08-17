<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected const TOKEN_CACHE_KEY = 'eskiz_sms_token';

    public function send(string $phone, string $message): bool
    {
        $phone = $this->normalizePhone($phone);

        if (! config('services.eskiz.email') || ! config('services.eskiz.password')) {
            Log::info("[SMS:log] {$phone} — {$message}");

            return true;
        }

        $response = $this->call($phone, $message);

        if ($response->status() === 401) {
            Cache::forget(self::TOKEN_CACHE_KEY);
            $response = $this->call($phone, $message);
        }

        if ($response->failed()) {
            Log::warning('Eskiz SMS yuborilmadi', [
                'phone' => $phone,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }

        return $response->successful();
    }

    protected function call(string $phone, string $message)
    {
        return Http::baseUrl(config('services.eskiz.base_url'))
            ->withToken($this->token())
            ->asForm()
            ->post('/api/message/sms/send', [
                'mobile_phone' => $phone,
                'message' => $message,
                'from' => config('services.eskiz.from'),
            ]);
    }

    protected function token(): string
    {
        return Cache::remember(self::TOKEN_CACHE_KEY, now()->addDays(25), function () {
            $response = Http::baseUrl(config('services.eskiz.base_url'))
                ->asForm()
                ->post('/api/auth/login', [
                    'email' => config('services.eskiz.email'),
                    'password' => config('services.eskiz.password'),
                ]);

            return $response->json('data.token', '');
        });
    }

    protected function normalizePhone(string $phone): string
    {
        return ltrim(preg_replace('/[^0-9]/', '', $phone), '+');
    }
}
