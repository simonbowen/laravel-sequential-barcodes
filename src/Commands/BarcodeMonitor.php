<?php

namespace SimonBowen\Barcode\Commands;

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
    public function handle(Connection $redis)
    {
        $redis->subscribe(['ean13.barcode.counter.channel'], function ($message) {
            BarcodeCounterUpdated::dispatch($message);
        });
    }
}
