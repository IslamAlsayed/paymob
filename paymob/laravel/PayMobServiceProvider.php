<?php

namespace IslamAlsayed\PayMob;

use Illuminate\Support\ServiceProvider;
use IslamAlsayed\PayMob\Facades\PayMob;

class PayMobServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            // Config file.
            __DIR__ . '/config/paymob.php' => config_path('paymob.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // PayMob Facede.
        $this->app->singleton('paymob', function () {
            return new PayMob;
        });
    }
}
