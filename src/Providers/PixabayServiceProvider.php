<?php

namespace hexa_package_pixabay\Providers;

use Illuminate\Support\ServiceProvider;
use hexa_package_pixabay\Services\PixabayService;

class PixabayServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PixabayService::class);
    }

    public function boot(): void {}
}
