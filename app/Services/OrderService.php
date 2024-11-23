<?php

namespace App\Services;

use App\DTO\OrderCreateDto;
use App\DTO\OrderShowDto;
use App\DTO\OrderUpdateDto;
use App\Enums\OrderStatusEnum;
use App\Exceptions\InvalidOrderStatusException;
use App\Exceptions\InvalidProductQuantityException;
use App\Exceptions\OrderNotFoundException;
use App\Models\Order;
use App\Repositories\OrderRepositoryInterface;
use App\ValueObjects\Money;
use Carbon\Carbon;
use Illuminate\Support\Str;

class OrderService
{
    protected OrderRepositoryInterface $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @throws InvalidProductQuantityException
     */
    public function createOrder(OrderCreateDto $order): Order
    {
        if ($this->isQuantityLimitExceeded($order->created_at, $order->product_quantity)) {
            throw new InvalidProductQuantityException('Quantity limit exceeded.');
        }

        $order = new Order(
            $this->generateOrderId(),
            $order->created_at,
            null,
            OrderStatusEnum::PENDING,
            $order->product_name,
            $order->product_quantity,
            Money::fromFloat($order->product_unit_price),
            Money::fromFloat($order->product_unit_price * $order->product_quantity),
        );

        return $this->orderRepository->create($order);
    }

    /**
     * @throws InvalidOrderStatusException
     * @throws OrderNotFoundException
     * @throws \Exception
     */
    public function updateOrderStatus(OrderUpdateDto $dto): Order
    {
        $order = $this->orderRepository->findById($dto->id);

        if (!$order) {
            throw new OrderNotFoundException('Order not found.');
        }

        if ($order->getStatus()->equals(OrderStatusEnum::COMPLETE) && $dto->status->equals(OrderStatusEnum::PENDING)) {
            throw new InvalidOrderStatusException('Cannot change status from complete to pending.');
        }

        $order->updateStatus($dto->status);

        return $this->orderRepository->updateStatus($dto->id, $dto->status);
    }
    private function generateOrderId(): string
    {
        return Str::ulid();
    }

    private function isQuantityLimitExceeded(Carbon $createdAt, int $productQuantity): bool
    {
        $startDate = Carbon::createFromFormat('d.m.Y', '10.09.2024')->startOfDay();
        $endDate = Carbon::createFromFormat('d.m.Y', '10.10.2024')->endOfDay();

        if ($createdAt >= $startDate && $createdAt <= $endDate && $productQuantity > 10) {
            return true;
        }

        return false;
    }


    public function listOrders(): array
    {
        $orders = $this->orderRepository->findAll();
        return array_map(function (Order $order) {
            return new OrderShowDto(
                $order->getId(),
                $order->getProductName(),
                $order->getStatus(),
                $order->getProductUnitPrice(),
                $order->getProductQuantity(),
                $order->getTotalPrice(),
                $order->getCreatedAt(),
                $order->getUpdatedAt(),
            );
        }, $orders);
    }

    public function showOrder(string $id): ?Order
    {
        return $this->orderRepository->findById($id);
    }
}
