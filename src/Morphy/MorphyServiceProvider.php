<?php

namespace Morphy;

use Illuminate\Support\ServiceProvider;

class MorphyServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/morphy.php', 'morphy');

        if (function_exists('config_path')) {
            $publishPath = config_path('morphy.php');
        } else {
            $publishPath = base_path('config/morphy.php');
        }
        $this->publishes([__DIR__ . '/../config/morphy.php' => $publishPath], 'config');

        Morphy::setConfig(Config::get('morphy'));
    }
}