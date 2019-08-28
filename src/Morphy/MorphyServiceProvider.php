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

        if ($name = \Config::get('morphy.laravel.connection')) {
            $conn = \DB::connection($name);
        }
        else {
            $conn = \DB::getDefaultConnection();
        }

        Morphy::setAdapter(new MorphyLaravelAdapter($conn));
        Morphy::setConfig(\Config::get('morphy'));
    }
}