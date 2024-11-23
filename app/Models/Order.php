<?php

namespace App\Models;

use App\Enums\OrderStatusEnum;
use App\ValueObjects\Money;
use Carbon\Carbon;

class Order
{
    private string $id;
    private Carbon $createdAt;
    private ?Carbon $updatedAt;
    private OrderStatusEnum $status;
    private string $productName;
    private int $productQuantity;
    private Money $productUnitPrice;
    private Money $totalPrice;

    public function __construct(
        string $id,
        Carbon $createdAt,
        ?Carbon $updatedAt,
        OrderStatusEnum $status,
        string $productName,
        int $productQuantity,
        Money $productUnitPrice,
        Money $totalPrice,
    ) {
        $this->id = $id;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->status = $status;
        $this->productName = $productName;
        $this->productQuantity = $productQuantity;
        $this->productUnitPrice = $productUnitPrice;
        $this->totalPrice = $totalPrice;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCreatedAt(): Carbon
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?Carbon
    {
        return $this->updatedAt;
    }

    public function getStatus(): OrderStatusEnum
    {
        return $this->status;
    }
    public function setStatus(OrderStatusEnum $status): OrderStatusEnum
    {
        return $this->status = $status;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function getProductQuantity(): int
    {
        return $this->productQuantity;
    }

    public function getProductUnitPrice(): Money
    {
        return $this->productUnitPrice;
    }

    public function getTotalPrice(): Money
    {
        return $this->totalPrice;
    }
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'product_name' => $this->getProductName(),
            'status' => $this->getStatus()->value,
            'product_unit_price' => $this->getProductUnitPrice()->getValue(),
            'product_quantity' => $this->getProductQuantity(),
            'total_price' => $this->getTotalPrice()->getValue(),
            'created_at' => $this->getCreatedAt()->format('d.m.Y H:i:s'),
            'updated_at' => $this->getUpdatedAt()?->format('d.m.Y H:i:s'),
        ];
    }
    public function updateStatus(OrderStatusEnum $newStatus): void
    {
        if ($this->status->equals(OrderStatusEnum::COMPLETE) && $newStatus->equals(OrderStatusEnum::PENDING)) {
            throw new \Exception('Cannot change status from complete to pending.');
        }

        $this->status = $newStatus;
        $this->updatedAt = new Carbon();
    }
}
