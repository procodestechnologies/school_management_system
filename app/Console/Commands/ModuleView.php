<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

#[Signature('app:module-view {name} {module}')]
#[Description('Create a Blade view inside a module')]
class ModuleView extends Command
{
    public function handle(): int
    {
        $view = str_replace('.', '/', $this->argument('name'));
        $module = Str::studly($this->argument('module'));

        $path = base_path("modules/{$module}/resources/views/{$view}.blade.php");

        File::ensureDirectoryExists(dirname($path));

        if (File::exists($path)) {
            $this->error("View [{$view}] already exists.");

            return self::FAILURE;
        }

        File::put($path, $this->stub($module));

        $this->info("View [{$view}] created successfully.");
        $this->line($path);

        return self::SUCCESS;
    }

    protected function stub(string $module): string
    {
        return <<<BLADE
<x-layouts::app :title="__('{$module}')">
    <div class="p-4">

    </div>
</x-layouts::app>

BLADE;
    }
}
