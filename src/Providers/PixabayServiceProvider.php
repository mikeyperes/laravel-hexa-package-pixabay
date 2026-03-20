<?php

namespace hexa_package_pixabay\Providers;

use Illuminate\Support\ServiceProvider;
use hexa_package_pixabay\Services\PixabayService;

/**
 * PixabayServiceProvider — registers Pixabay package routes, views, config,
 * and sidebar menu for the Hexa Core framework.
 */
class PixabayServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/pixabay.php', 'pixabay');
        $this->app->singleton(PixabayService::class);
    }

    /**
     * Bootstrap package services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../../routes/pixabay.php');
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'pixabay');

        // Sidebar menu injection (skipped when app controls sidebar)
        $this->registerSidebarMenu();
    }

    /**
     * Register sidebar menu items via view composer.
     *
     * @return void
     */
    private function registerSidebarMenu(): void
    {
        view()->composer('layouts.app', function ($view) {
            if (config('hexa.app_controls_sidebar', false)) return;
            $factory = app('view');
            $factory->startPush('sidebar-sandbox');
            echo $factory->make('pixabay::partials.sidebar-menu')->render();
            $factory->stopPush();
        });
    }
}
