<?php

namespace SimonBowen\Barcode;

use Illuminate\Contracts\Redis\Connection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use SimonBowen\Barcode\Commands\BarcodeMonitor;
use SimonBowen\Barcode\Commands\UpdateBarcodePrefix;
use SimonBowen\Barcode\Events\BarcodeCounterUpdated;
use SimonBowen\Barcode\Listeners\RemainingBarcodesAvailable;

class BarcodeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                BarcodeMonitor::class,
                UpdateBarcodePrefix::class,
            ]);
        }

        $this->publishes([
            dirname(__DIR__) . '/config/barcode.php' => config_path('barcode.php')
        ], 'config');

        $repo = app(BarcodeRepository::class);
        if (!$repo->getPrefix()) {
            $repo->setPrefix(config('barcode.ean'));
        }

        Event::listen(BarcodeCounterUpdated::class, RemainingBarcodesAvailable::class);
    }

    public function register()
    {
        dd(config('barcode'));
//        $this->app->bind(BarcodeRepository::class, function () {
//            $redis      = app(Connection::class);
//            $counterKey = config('barcode.keys.counter', 'barcode.counter');
//            $prefixKey  = config('barcode.keys.prefix', 'barcode.prefix');
//
//            return new BarcodeRepository($redis, $counterKey, $prefixKey);
//        });
    }
}