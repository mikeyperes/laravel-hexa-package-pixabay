<?php

namespace hexa_package_pixabay\Providers;

use Illuminate\Support\ServiceProvider;
use hexa_package_pixabay\Services\PixabayService;
use hexa_core\Services\PackageRegistryService;

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

        // Sidebar links — registered via PackageRegistryService with auto permission checks
        if (!config('hexa.app_controls_sidebar', false)) {
            $registry = app(PackageRegistryService::class);
            // HWS-SIDEBAR-MENU-3L-BEGIN
            $registry->registerDomainGroup('Discovery', 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z', 20);
            $registry->registerSectionGroup('Sandbox', 'Discovery', '', 20);
            // HWS-SIDEBAR-MENU-3L-END

            $registry->registerSidebarLink('pixabay.index', 'Pixabay', 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z', 'Sandbox', 'pixabay', 84);
            $registry->registerPackage('pixabay', 'hexawebsystems/laravel-hexa-package-pixabay', [
                'title' => 'Pixabay',
                'description' => 'Stock image search and import tooling powered by the Pixabay API.',
                'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
                'color' => 'amber',
                'settingsRoute' => 'pixabay.index',
            ]);
        }
    }
}
