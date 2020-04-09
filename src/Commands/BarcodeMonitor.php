<?php

namespace SimonBowen\Barcode\Commands;

use Illuminate\Config\Repository;
use Illuminate\Console\Command;
use Illuminate\Contracts\Redis\Connection;

use SimonBowen\Barcode\Events\BarcodeCounterUpdated;

class BarcodeMonitor extends Command
{
    protected $signature = 'barcode:monitor';

    protected $description = 'Subscribe to the barcode counter to monitor current status';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Subscribe to the barcode.counter channel on redis.
     *
     * Dispatch an internal event to notify that the Barcode Counter
     * has been updated.
     *
     * @param Connection $redis
     */
    public function handle(Connection $redis, Repository $config)
    {
        $redis->subscribe([$config->get('barcode.keys.channel')], function ($message) {
            $message = json_decode($message, true);
            BarcodeCounterUpdated::dispatch($message['prefix'], $message['counter']);
        });
    }
}
