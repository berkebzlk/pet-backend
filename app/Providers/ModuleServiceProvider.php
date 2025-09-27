<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

class ModuleServiceProvider extends ServiceProvider
{
    public function register()
    {
        $modules = File::directories(app_path('Modules'));

        foreach ($modules as $module) {
            // Modül adını al
            $moduleName = basename($module);

            // Provider'ı yükle
            $providerClass = "App\\Modules\\{$moduleName}\\Providers\\{$moduleName}ServiceProvider";
            if (class_exists($providerClass)) {
                $this->app->register($providerClass);
            }

            // Migration'ları yükle
            $migrationPath = $module . '/Database/Migrations';
            if (File::isDirectory($migrationPath)) {
                $this->loadMigrationsFrom($migrationPath);
            }

            // Route'ları yükle
            $this->registerRoutes($moduleName);
            // $routePath = $module . '/Routes';
            // if (File::isDirectory($routePath)) {
            //     $routes = File::glob($routePath . '/*.php');
            //     foreach ($routes as $route) {
            //         $prefix = strtolower($moduleName);
            //         Route::prefix('api')
            //             ->name($prefix . '.')
            //             ->group($route);
            //     }
            // }

            // View'ları yükle
            $viewPath = $module . '/Resources/Views';
            if (File::isDirectory($viewPath)) {
                $this->loadViewsFrom($viewPath, strtolower($moduleName));
            }

            // Lang dosyalarını yükle
            $langPath = $module . '/Resources/lang';
            if (File::isDirectory($langPath)) {
                $this->loadTranslationsFrom($langPath, strtolower($moduleName));
            }

            // Config dosyalarını yükle
            $configPath = $module . '/Config';
            if (File::isDirectory($configPath)) {
                foreach (File::files($configPath) as $file) {
                    $filename = pathinfo($file, PATHINFO_FILENAME);
                    
                    // Load with namespace: 'role::role' example: app/Modules/Role/Config/role.php => 'role::role'
                    $this->mergeConfigFrom($file->getPathname(), strtolower($moduleName) . '::' . $filename);
                }
            }
        }
    }

    private function registerRoutes($moduleName)
    {
        $modules = File::directories(app_path('Modules'));

        foreach ($modules as $module) {
            $moduleName = basename($module);
            $provider = "App\\Modules\\{$moduleName}\\Providers\\RouteServiceProvider";

            if (class_exists($provider)) {
                $this->app->register($provider);
            }
        }
    }
}
