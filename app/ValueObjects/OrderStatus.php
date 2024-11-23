<?php

namespace App\ValueObjects;

use App\Enums\OrderStatusEnum;

class OrderStatus
{
    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function PENDING(): self
    {
        return new self('pending');
    }

    public static function COMPLETE(): self
    {
        return new self('complete');
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function fromString(string $value): self
    {
        return new self(OrderStatusEnum::from($value)->value);
    }
}
