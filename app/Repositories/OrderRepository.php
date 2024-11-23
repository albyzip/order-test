<?php

namespace App\Repositories;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\ValueObjects\Money;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class OrderRepository implements OrderRepositoryInterface
{
    private const ORDER_KEY_PREFIX = 'asd:';

    public function create(Order $order): Order
    {
        $orderData = [
            'id' => $order->getId(),
            'created_at' => $order->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $order->getUpdatedAt()?->format('Y-m-d H:i:s'),
            'status' => $order->getStatus(),
            'product_name' => $order->getProductName(),
            'product_quantity' => $order->getProductQuantity(),
            'product_unit_price' => $order->getProductUnitPrice()->getValue(),
            'total_price' => $order->getTotalPrice()->getValue(),
        ];

        Redis::set(self::ORDER_KEY_PREFIX . $order->getId(), json_encode($orderData));

        return $order;
    }


    public function findAll(): array
    {
        $keys = Redis::keys(self::ORDER_KEY_PREFIX . '*');
        $orders = [];

        foreach ($keys as $key) {
            $key = Str::ltrim($key, 'laravel_database:');
            $orderData = json_decode(Redis::get($key), true);

            $orders[] = new Order(
                $orderData['id'],
                new Carbon($orderData['created_at']),
                $orderData['updated_at'] ? new Carbon($orderData['updated_at']) : null,
                OrderStatusEnum::from($orderData['status']),
                $orderData['product_name'],
                $orderData['product_quantity'],
                Money::fromFloat($orderData['product_unit_price']),
                Money::fromFloat($orderData['total_price'])
            );
        }

        return $orders;
    }

    public function findById(string $id): ?Order
    {
        $orderData = Redis::get(self::ORDER_KEY_PREFIX . $id);
        if (!$orderData) {
            return null;
        }

        $orderData = json_decode($orderData, true);
        return new Order(
            $orderData['id'],
            new Carbon($orderData['created_at']),
            $orderData['updated_at'] ? new Carbon($orderData['updated_at']) : null,
            OrderStatusEnum::from($orderData['status']),
            $orderData['product_name'],
            $orderData['product_quantity'],
            Money::fromFloat($orderData['product_unit_price']),
            Money::fromFloat($orderData['total_price']),
        );
    }

    public function updateStatus(string $id, OrderStatusEnum $status): Order
    {
        $order = $this->findById($id);
        if (!$order) {
            throw new \Exception('Order not found.');
        }

        $order->updateStatus($status);
        $this->create($order);
        return $order;
    }
}
