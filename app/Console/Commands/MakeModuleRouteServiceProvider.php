<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeModuleRouteServiceProvider extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-route-service-provider {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $moduleName = $this->argument('name');        
        $this->createRouteServiceProvider($moduleName);
        $this->info("$moduleName route service provider created successfully. " . base_path("app/Modules/{$moduleName}/Providers/RouteServiceProvider.php"));
    }

    protected function createRouteServiceProvider($name)
    {
        $stub = File::get(base_path('stubs/module-route-service-provider.stub'));

        $content = str_replace(
            ['{{ namespace }}', '{{ name }}'],
            ["App\\Modules\\{$name}\\Providers", $name],
            $stub
        );

        File::put(
            app_path("Modules/{$name}/Providers/RouteServiceProvider.php"),
            $content
        );
    }
}
