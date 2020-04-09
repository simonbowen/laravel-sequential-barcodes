<?php

namespace SimonBowen\Barcode\Listeners;

use Illuminate\Support\Facades\Log;
use Illuminate\Config\Repository as Config;

use SimonBowen\Barcode\Barcode;
use SimonBowen\Barcode\Events\BarcodeCounterUpdated;

class RemainingBarcodesAvailable
{
    protected Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Calculate how many product codes are left and notify if under the threshold
     *
     * @param BarcodeCounterUpdated $event
     */
    public function handle(BarcodeCounterUpdated $event)
    {
        $barcode = new Barcode($event->prefix, $event->counter);
        $remaining = $barcode->remainingCodes();

        if ($remaining < $this->config->get('barcode.threshold', 100)) {
            Log::critical("Barcodes with Prefix below threshold", [
                'prefix' => $event->prefix,
                'remaining' => $remaining,
                'counter' => $event->counter
            ]);
        }
    }
}