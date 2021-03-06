<?php

namespace SimonBowen\Barcode;

use Illuminate\Contracts\Redis\Connection;
use SimonBowen\Barcode\Events\BarcodeCounterUpdated;

class BarcodeRepository
{
    protected Connection $redis;

    protected string $counterKey;
    protected string $prefixKey;

    public function __construct(Connection $redis, string $counterKey, string $prefixKey)
    {
        $this->redis = $redis;
        $this->counterKey = $counterKey;
        $this->prefixKey = $prefixKey;
    }

    /**
     * Atomically return a new Barcode Product Number
     *
     * @return Barcode
     */
    public function next()
    {
        $next = $this->redis->command('INCR', [$this->counterKey]);
        $prefix = $this->getPrefix();

        BarcodeCounterUpdated::dispatch($prefix, $next);
        return new Barcode($prefix, $next);
    }

    /**
     * Set the prefix to use for the barcode
     *
     * @param $prefix
     */
    public function setPrefix($prefix)
    {
        $this->redis->set($this->prefixKey, $prefix);
    }

    /**
     * Get the current prefix
     *
     * @return mixed
     */
    public function getPrefix()
    {
        return $this->redis->get($this->prefixKey);
    }

    /**
     * Set the counter to use for the product number
     *
     * @param $number
     */
    public function setCounter($number)
    {
        $this->redis->set($this->counterKey, $number);
    }

    /**
     * Get the current counter
     *
     * @return mixed
     */
    public function getCounter()
    {
        return $this->redis->get($this->counterKey);
    }

    /**
     * Atomically set a prefix and optionally set a counter
     *
     * @param $prefix
     * @param null $counter
     */
    public function updatePrefix($prefix, $counter = null)
    {
        $this->redis->multi();
        $this->setPrefix($prefix);
        if (isset($counter)) {
            $this->setCounter($counter);
        }
        $this->redis->exec();
    }

    public function current()
    {
        return $this->getCounter();
    }
}