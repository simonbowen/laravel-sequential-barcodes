<?php

namespace SimonBowen\Barcode\Commands;

use SimonBowen\Barcode\BarcodeRepository;
use Illuminate\Console\Command;

class UpdateBarcodePrefix extends Command
{
    protected $signature = 'barcode:prefix {prefix} {counter?}';

    protected $description = 'Updates the prefix for barcode generation with an option to change the counter';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(BarcodeRepository $barcodeRepository)
    {
        $prefix = $this->argument('prefix');
        $counter = $this->argument('counter');

        $barcodeRepository->updatePrefix($prefix, $counter);

        $this->info('Set Barcode Prefix: ' . $prefix);
    }
}
