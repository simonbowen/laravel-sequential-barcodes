<?php

namespace SimonBowen\Barcode;

class Barcode
{
    public string $prefix;

    public int $number;

    public string $ean;

    const LENGTH = 13;

    public function __construct(string $prefix, int $number)
    {
        $this->prefix = $prefix;
        $this->number = $number;
        $this->ean = $this->formatEan();

        $this->validateLength();
    }

    private function checkDigit(string $ean)
    {
        $digits = collect(str_split($ean));

        $even = $digits->filter(function ($digit, $key) {
            return $key % 2 == 0;
        })->sum();
        $odd = $digits->filter(function ($digit, $key) {
            return $key % 2 == 1;
        })->sum();

        $oddSum = $odd * 3;
        $totalSum = $even + $oddSum;
        $ten = ceil($totalSum / 10) * 10;

        return $ten - $totalSum;
    }

    private function formatEan()
    {
        $number = $this->prefix . str_pad($this->number, 12 - strlen($this->prefix), "0", STR_PAD_LEFT);
        return $number . $this->checkDigit($number);
    }

    private function validateLength()
    {
        if (strlen($this->getEan()) > self::LENGTH) {
            throw new \Exception("EAN exceeds maximum length: {$this->getEan()}");
        }
    }

    public function remainingCodes()
    {
        $space = 12 - strlen($this->prefix);
        $maximum = (int)str_repeat('9', $space);

        return $maximum - (int)$this->number;
    }

    public function getEan()
    {
        return $this->ean;
    }
}