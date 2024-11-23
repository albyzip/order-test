<?php

namespace App\Repositories;

use App\Enums\OrderStatusEnum;
use App\Models\Order;

interface OrderRepositoryInterface
{
    public function create(Order $order): Order;
    public function findAll(): array;
    public function findById(string $id): ?Order;
    public function updateStatus(string $id, OrderStatusEnum $status): Order;
}
