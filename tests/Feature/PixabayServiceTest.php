<?php

namespace Tests\Feature;

use hexa_package_pixabay\Services\PixabayService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PixabayServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->requireInstalledPackage("hexawebsystems/laravel-hexa-package-pixabay", PixabayService::class);
    }

    public function test_api_key_probe_uses_provider_endpoint(): void
    {
        Http::fake(["*pixabay.com/api/*" => Http::response(["totalHits" => 123], 200)]);

        $result = app(PixabayService::class)->testApiKey("test-key");

        $this->assertTrue($result["success"]);
        Http::assertSent(fn (Request $request): bool => str_contains($request->url(), "pixabay.com/api/"));
    }
}
