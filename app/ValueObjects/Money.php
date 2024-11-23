<?php

namespace App\ValueObjects;
class Money
{
    private float $value;

    private function __construct(float $value)
    {
        $this->value = $value;
    }

    public static function fromFloat(float $value): self
    {
        return new self($value);
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return number_format($this->value, 2);
    }
}
