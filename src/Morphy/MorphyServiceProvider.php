<?php

namespace Morphy;

use Illuminate\Support\ServiceProvider;

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

        Morphy::setConfig(Config::get('morphy'));
    }
}