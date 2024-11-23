<?php

namespace App\DTO;

use App\Enums\OrderStatusEnum;
use App\ValueObjects\Money;
use Carbon\Carbon;

class OrderShowDto
{
    public function __construct(
        public string          $id,
        public string          $product_name,
        public OrderStatusEnum $status,
        public Money           $product_unit_price,
        public int             $product_quantity,
        public Money             $total_price,
        public Carbon          $created_at,
        public ?Carbon         $updated_at,
    )
    {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'product_name' => $this->product_name,
            'status' => $this->status->value,
            'product_unit_price' => $this->product_unit_price,
            'product_quantity' => $this->product_quantity,
            'total_price' => $this->total_price,
            'created_at' => $this->created_at->format('d.m.Y H:i:s'),
            'updated_at' => $this->updated_at?->format('d.m.Y H:i:s'),
        ];
    }

    public static function from(array $data): self
    {
        return new self(
            $data['id'],
            $data['product_name'],
            OrderStatusEnum::from($data['status']),
            $data['product_unit_price'],
            $data['product_quantity'],
            $data['total_price'],
            new Carbon($data['updated_at']),
            new Carbon($data['created_at']),
        );
    }
}
