<?php

namespace Artisan\Settings\Commands;

use Illuminate\Console\DetectsApplicationNamespace;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeSettingsCommand extends Command
{
    use DetectsApplicationNamespace;

    protected $signature = 'settings:make {name}';

    protected $description = 'Create a settings file.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $directory = $this->directory();

        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, $recursive = true);
        }

        File::put(
            $directory . DIRECTORY_SEPARATOR . $this->argument('name') . '.php',
            $this->stub()
        );
    }

    protected function stub()
    {
        return str_replace(
            ['DummyNamespace', 'DummySettingsClass'],
            [$this->namespace(), $this->argument('name')],
            File::get(__DIR__ . '/settings.stub')
        );
    }

    protected function directory()
    {
        $path = str_replace("\\", DIRECTORY_SEPARATOR, config('laravel-settings.namespace'));

        return app_path($path);
    }

    protected function namespace()
    {
        return $this->getAppNamespace() . config('laravel-settings.namespace');
    }
}
