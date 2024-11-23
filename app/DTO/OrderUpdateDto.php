<?php

namespace App\DTO;

use App\Enums\OrderStatusEnum;

class OrderUpdateDto
{
    public function __construct(
        public string $id,
        public OrderStatusEnum $status,

    )
    {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
        ];
    }

    public static function from(array $data): self
    {
        return new self(
            $data['id'],
            OrderStatusEnum::from($data['status']),
        );
    }
}
