<?php

namespace SimonBowen\Barcode;

use Illuminate\Contracts\Redis\Connection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use SimonBowen\Barcode\Commands\UpdateBarcodePrefix;
use SimonBowen\Barcode\Events\BarcodeCounterUpdated;
use SimonBowen\Barcode\Listeners\RemainingBarcodesAvailable;

class BarcodeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                UpdateBarcodePrefix::class,
            ]);
        }

        $this->publishes([
            dirname(__DIR__) . '/config/barcode.php' => config_path('barcode.php')
        ], 'config');

        $this->mergeConfigFrom(dirname(__DIR__) . '/config/barcode.php', 'barcode');

        Event::listen(BarcodeCounterUpdated::class, RemainingBarcodesAvailable::class);
    }

    public function register()
    {
        $this->app->singleton(BarcodeRepository::class, function () {
            $redis = app(Connection::class);
            $counterKey = config('barcode.keys.counter', 'barcode.counter');
            $prefixKey = config('barcode.keys.prefix', 'barcode.prefix');

            return new BarcodeRepository($redis, $counterKey, $prefixKey);
        });
    }
}