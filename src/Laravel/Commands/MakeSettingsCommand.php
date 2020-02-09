<?php

namespace Humans\Settings\Laravel\Commands;

use Illuminate\Console\DetectsApplicationNamespace;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
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
        $filename = $this->argument('name') . '.php';

        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, $recursive = true);
        }

        if (! File::exists($path = $directory . DIRECTORY_SEPARATOR . $filename)) {
            File::put($path, $this->stub());

            $this->info("{$filename} created.");
        } else {
            $this->error("{$filename} already exists.");
        }
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
        $path = str_replace("\\", DIRECTORY_SEPARATOR, Config::get('humans.settings.namespace'));

        return App::path($path);
    }

    protected function namespace()
    {
        return $this->getAppNamespace() . Config::get('humans.settings.namespace');
    }
}
