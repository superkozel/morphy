<?php

namespace Morphy;

use Illuminate\Support\ServiceProvider;
use Morphy\Adapter\MorphyLaravelAdapter;

class MorphyServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $configPath = __DIR__.'/../../config/morphy.php';

        $this->mergeConfigFrom($configPath, 'morphy');

        if (function_exists('config_path')) {
            $publishPath = config_path('morphy.php');
        } else {
            $publishPath = base_path('config/morphy.php');
        }
        $this->publishes([$configPath => $publishPath], 'config');

        $name = \Config::get('morphy.laravel.connection', \DB::getDefaultConnection());
        $conn = \DB::connection($name);

        Morphy::setAdapter(new MorphyLaravelAdapter($conn));
        Morphy::setConfig(\Config::get('morphy'));
    }
}