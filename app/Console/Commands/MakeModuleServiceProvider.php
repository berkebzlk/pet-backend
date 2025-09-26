<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeModuleServiceProvider extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-service-provider {name}';

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
        $name = $this->argument('name');
        $this->createServiceProvider($name);
        $this->info("$name service provider created successfully. " . base_path("app/Modules/{$name}/Providers/{$name}ServiceProvider.php"));
    }

    protected function createServiceProvider($name)
    {
        $stub = File::get(base_path('stubs/module-service-provider.stub'));
        $content = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            ["App\\Modules\\{$name}\\Providers", "{$name}ServiceProvider"],
            $stub
        );

        File::put(
            app_path("Modules/{$name}/Providers/{$name}ServiceProvider.php"),
            $content
        );
    }
}
