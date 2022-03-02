<?php

namespace Jaap\HypixelApi;

use Illuminate\Support\ServiceProvider;

class HypixelProvider extends ServiceProvider
{
    public function register()
    {

    }

    public function boot() {
        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('hypixel.php'),
        ]);
    }
}
