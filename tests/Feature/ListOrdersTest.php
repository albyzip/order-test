<?php

namespace Tests\Feature;

use App\DTO\OrderCreateDto;
use App\Exceptions\InvalidProductQuantityException;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\TestCase;

class ListOrdersTest extends TestCase
{
    protected OrderService $orderService;

    /**
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->orderService = $this->app->make(OrderService::class);
    }

    /**
     * @throws InvalidProductQuantityException
     */
    public function test_orders_can_be_listed()
    {
        $order = $this->createOrder();

        $orderDetails = $this->orderService->showOrder($order->getId());

        $this->assertEquals($order->getId(), $orderDetails->getId());
        $this->assertEquals($order->getCreatedAt()->format('d.m.Y H:i:s'), $orderDetails->getCreatedAt()->format('d.m.Y H:i:s'));
        $this->assertEquals($order->getProductName(), $orderDetails->getProductName());
        $this->assertEquals($order->getProductQuantity(), $orderDetails->getProductQuantity());
        $this->assertEquals($order->getProductUnitPrice()->__toString(), $orderDetails->getProductUnitPrice()->__toString());
        $this->assertEquals($order->getTotalPrice(), $orderDetails->getTotalPrice());
        $this->assertEquals($order->getStatus()->value, $orderDetails->getStatus()->value);
    }

    /**
     * @throws InvalidProductQuantityException
     */
    private function createOrder(): Order
    {
        $data = OrderCreateDto::from([
            'created_at' => '10.10.2024 12:00:00',
            'product_name' => 'Test Product',
            'product_unit_price' => 10.00,
            'product_quantity' => 5,
        ]);

        return $this->orderService->createOrder($data);
    }
}
