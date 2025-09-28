<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateModularIdeHelper extends Command
{
    protected $signature = 'ide-helper:modular {--force : Force regenerate all files}';
    protected $description = 'Generate IDE helper for modular structure';

    public function handle()
    {
        $this->info('🚀 Generating modular IDE helper...');

        $this->generateModels();
        $this->generateInterfaces();
        $this->generateServices();
        $this->generateRepositories();
        $this->generateControllers();
        $this->generateResources();

        $this->info('✅ Modular IDE helper generation completed!');
    }

    private function generateModels()
    {
        $this->info('📁 Generating models...');
        
        $modulesPath = app_path('Modules');
        $modules = File::directories($modulesPath);

        foreach ($modules as $module) {
            $moduleName = basename($module);
            $modelsPath = $module . '/Models';
            
            if (File::isDirectory($modelsPath)) {
                // ✅ Array olarak geç
                $this->call('ide-helper:models', [
                    '--dir' => [$modelsPath], // Array olarak
                    '--write' => true,
                ]);
                $this->line("  ✓ {$moduleName} models generated");
            }
        }
    }

    private function generateInterfaces()
    {
        $this->info('🔗 Generating interfaces...');

        $interfaces = [];
        $modulesPath = app_path('Modules');
        $modules = File::directories($modulesPath);

        foreach ($modules as $module) {
            $moduleName = basename($module);

            // Service interfaces
            $serviceInterfaces = $this->findFiles($module . '/Services', '*ServiceInterface.php');
            foreach ($serviceInterfaces as $interface) {
                $interfaceName = $this->getClassName($interface);
                $implementationName = str_replace('Interface', '', $interfaceName);
                $implementationPath = $module . '/Services/Impl/' . $implementationName . '.php';

                if (File::exists($implementationPath)) {
                    $interfaces[$interfaceName] = $this->getFullClassName($implementationPath);
                }
            }

            // Repository interfaces
            $repositoryInterfaces = $this->findFiles($module . '/Repositories', '*RepositoryInterface.php');
            foreach ($repositoryInterfaces as $interface) {
                $interfaceName = $this->getClassName($interface);
                $implementationName = str_replace('Interface', 'Eloquent', $interfaceName);
                $implementationPath = $module . '/Repositories/Impl/' . $implementationName . '.php';

                if (File::exists($implementationPath)) {
                    $interfaces[$interfaceName] = $this->getFullClassName($implementationPath);
                }
            }
        }

        // Core interfaces
        $interfaces['App\Modules\Core\Repositories\BaseRepositoryInterface'] = 'App\Modules\Core\Repositories\Impl\BaseRepositoryEloquent';
        $interfaces['App\Modules\Core\Services\BaseServiceInterface'] = 'App\Modules\Core\Services\Impl\BaseService';

        $this->updateIdeHelperConfig($interfaces);
    }

    private function generateServices()
    {
        $this->info('⚙️ Generating services...');

        $modulesPath = app_path('Modules');
        $modules = File::directories($modulesPath);

        foreach ($modules as $module) {
            $moduleName = basename($module);
            $servicesPath = $module . '/Services';

            if (File::isDirectory($servicesPath)) {
                $services = $this->findFiles($servicesPath, '*Service.php');
                foreach ($services as $service) {
                    $this->line("  ✓ {$moduleName} service: " . basename($service));
                }
            }
        }
    }

    private function generateRepositories()
    {
        $this->info('🗄️ Generating repositories...');

        $modulesPath = app_path('Modules');
        $modules = File::directories($modulesPath);

        foreach ($modules as $module) {
            $moduleName = basename($module);
            $repositoriesPath = $module . '/Repositories';

            if (File::isDirectory($repositoriesPath)) {
                $repositories = $this->findFiles($repositoriesPath, '*Repository.php');
                foreach ($repositories as $repository) {
                    $this->line("  ✓ {$moduleName} repository: " . basename($repository));
                }
            }
        }
    }

    private function generateControllers()
    {
        $this->info('🎮 Generating controllers...');

        $modulesPath = app_path('Modules');
        $modules = File::directories($modulesPath);

        foreach ($modules as $module) {
            $moduleName = basename($module);
            $controllersPath = $module . '/Controllers';

            if (File::isDirectory($controllersPath)) {
                $controllers = $this->findFiles($controllersPath, '*Controller.php');
                foreach ($controllers as $controller) {
                    $this->line("  ✓ {$moduleName} controller: " . basename($controller));
                }
            }
        }
    }

    private function generateResources()
    {
        $this->info('📦 Generating resources...');

        $modulesPath = app_path('Modules');
        $modules = File::directories($modulesPath);

        foreach ($modules as $module) {
            $moduleName = basename($module);
            $resourcesPath = $module . '/Resources';

            if (File::isDirectory($resourcesPath)) {
                $resources = $this->findFiles($resourcesPath, '*Resource.php');
                foreach ($resources as $resource) {
                    $this->line("  ✓ {$moduleName} resource: " . basename($resource));
                }
            }
        }
    }

    private function findFiles($directory, $pattern)
    {
        if (!File::isDirectory($directory)) {
            return [];
        }

        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && fnmatch($pattern, $file->getFilename())) {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    private function getClassName($filePath)
    {
        $content = File::get($filePath);
        if (preg_match('/namespace\s+([^;]+);/', $content, $matches)) {
            $namespace = $matches[1];
        }

        if (preg_match('/class\s+(\w+)/', $content, $matches)) {
            return $namespace . '\\' . $matches[1];
        }

        return null;
    }

    private function getFullClassName($filePath)
    {
        $content = File::get($filePath);
        if (preg_match('/namespace\s+([^;]+);/', $content, $matches)) {
            $namespace = $matches[1];
        }

        if (preg_match('/class\s+(\w+)/', $content, $matches)) {
            return $namespace . '\\' . $matches[1];
        }

        return null;
    }

    private function updateIdeHelperConfig($interfaces)
    {
        $configPath = config_path('ide-helper.php');
        $config = File::get($configPath);

        // Remove old interfaces section
        $config = preg_replace(
            '/\'interfaces\'\s*=>\s*\[.*?\],/s',
            '',
            $config
        );

        // Add new interfaces section
        $interfacesString = "    'interfaces' => [\n";
        foreach ($interfaces as $interface => $implementation) {
            $interfacesString .= "        '{$interface}' => '{$implementation}',\n";
        }
        $interfacesString .= "    ],\n";

        // Insert before the last closing bracket
        $config = preg_replace(
            '/\s*\]\s*;\s*$/',
            "\n{$interfacesString}\n];",
            $config
        );

        File::put($configPath, $config);
        $this->line("  ✓ IDE Helper config updated");
    }
}
