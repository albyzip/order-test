<?php

namespace App\DTO;

use Carbon\Carbon;

class OrderCreateDto
{
    public function __construct(
        public string $product_name,
        public float $product_unit_price,
        public int $product_quantity,
        public Carbon $created_at,

    )
    {}

    public function toArray(): array
    {
        return [
            'product_name' => $this->product_name,
            'product_unit_price' => $this->product_unit_price,
            'product_quantity' => $this->product_quantity,
            'createdAt' => $this->created_at,
        ];
    }

    public static function from(array $data): self
    {
        return new self(
            $data['product_name'],
            $data['product_unit_price'],
            $data['product_quantity'],
            new Carbon($data['created_at']),
        );
    }
}
