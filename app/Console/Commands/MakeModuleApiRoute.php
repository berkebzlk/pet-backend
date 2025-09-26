<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeModuleApiRoute extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:api-route {name}';

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
        $lcName = lcfirst($name);
        $stub = File::get(base_path('stubs/module-api-route.stub'));
        $content = str_replace('{{ name }}', $lcName, $stub);
        File::put(base_path("app/Modules/{$name}/Routes/api.php"), $content);

        $this->info("$name api route created successfully, " . base_path("app/Modules/{$name}/Routes/api.php"));
    }
}
