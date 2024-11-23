<?php

namespace Tests\Feature;

use App\DTO\OrderCreateDto;
use App\Enums\OrderStatusEnum;
use App\Exceptions\InvalidProductQuantityException;
use App\Models\Order;
use App\Services\OrderService;
use Tests\TestCase;

class CreateOrderTest extends TestCase
{
    protected OrderService $orderService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderService = $this->app->make(OrderService::class);
    }

    public function test_order_can_be_created()
    {
        $data = OrderCreateDto::from([
            'created_at' => '10.10.2024 12:00:00',
            'product_name' => 'Test Product',
            'product_unit_price' => 10.00,
            'product_quantity' => 5,
        ]);

        $order = $this->orderService->createOrder($data);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals('Test Product', $order->getProductName());
        $this->assertEquals(10.00, $order->getProductUnitPrice()->getValue());
        $this->assertEquals(5, $order->getProductQuantity());
        $this->assertEquals(50.00, $order->getTotalPrice()->getValue());
        $this->assertEquals(OrderStatusEnum::PENDING, $order->getStatus());
        $this->assertEquals('10.10.2024 12:00:00', $order->getCreatedAt()->format('d.m.Y H:i:s'));
    }

    /**
     * @throws InvalidProductQuantityException
     */
    public function test_order_cannot_be_created_with_quantity_exceeding_limit()
    {
        $data = OrderCreateDto::from([
            'created_at' => '10.09.2024 12:00:00',
            'product_name' => 'Test Product',
            'product_unit_price' => 10.00,
            'product_quantity' => 11,
        ]);
        $this->expectException(InvalidProductQuantityException::class);
        $this->orderService->createOrder($data);
    }
}
