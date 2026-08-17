<?php

namespace Tests\Feature;

use App\Services\Sms\SmsService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class SmsServiceTest extends TestCase
{
    public function test_sms_falls_back_to_log_when_credentials_are_not_configured(): void
    {
        config(['services.eskiz.email' => null, 'services.eskiz.password' => null]);
        Http::fake();

        Log::shouldReceive('info')->once()->withArgs(fn (string $line) => str_contains($line, '998901234567'));

        $result = app(SmsService::class)->send('+998901234567', 'Test xabar');

        $this->assertTrue($result);
        Http::assertNothingSent();
    }

    public function test_sms_calls_eskiz_api_when_credentials_are_configured(): void
    {
        config([
            'services.eskiz.email' => 'test@binocor.uz',
            'services.eskiz.password' => 'secret',
            'services.eskiz.base_url' => 'https://notify.eskiz.uz',
        ]);

        Http::fake([
            '*/api/auth/login' => Http::response(['data' => ['token' => 'fake-token']], 200),
            '*/api/message/sms/send' => Http::response(['status' => 'waiting'], 200),
        ]);

        $result = app(SmsService::class)->send('+998901234567', 'Test xabar');

        $this->assertTrue($result);
        Http::assertSent(fn ($request) => str_contains($request->url(), '/api/message/sms/send')
            && $request['mobile_phone'] === '998901234567');
    }
}
