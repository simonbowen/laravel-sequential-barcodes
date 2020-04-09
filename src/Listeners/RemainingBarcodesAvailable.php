<?php

namespace SimonBowen\Barcode\Listeners;

use Illuminate\Support\Facades\Log;

use SimonBowen\Barcode\Barcode;
use SimonBowen\Barcode\Events\BarcodeCounterUpdated;
use SimonBowen\Barcode\BarcodeRepository;

class RemainingBarcodesAvailable
{
    protected BarcodeRepository $repository;

    public function __construct(BarcodeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Calculate how many product codes are left and notify if under the threshold
     *
     * @param BarcodeCounterUpdated $event
     */
    public function handle(BarcodeCounterUpdated $event)
    {
        $prefix    = $this->repository->getPrefix();
        $barcode   = new Barcode($prefix, $event->counter);
        $remaining = $barcode->remainingCodes();

        if ($remaining < config('barcodes.threshold', 100)) {
            Log::critical("Barcodes with Prefix below threshold", [
                'prefix' => $prefix,
                'remaining' => $remaining,
                'counter' => $event->counter
            ]);
        }
    }
}