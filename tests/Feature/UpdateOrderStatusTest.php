<?php

namespace Tests\Feature;

use App\DTO\OrderCreateDto;
use App\DTO\OrderUpdateDto;
use App\Exceptions\InvalidOrderStatusException;
use App\Exceptions\InvalidProductQuantityException;
use App\Exceptions\OrderNotFoundException;
use App\Models\Order;
use App\Services\OrderService;
use App\Enums\OrderStatusEnum;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\TestCase;

class UpdateOrderStatusTest extends TestCase
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
     * @throws InvalidOrderStatusException
     * @throws OrderNotFoundException
     */
    public function test_order_status_can_be_updated()
    {
        $order = $this->createOrder();

        $order->setStatus(OrderStatusEnum::COMPLETE);

        $orderUpdate = OrderUpdateDto::from($order->toArray());

        $updatedOrder = $this->orderService->updateOrderStatus($orderUpdate);

        $this->assertEquals(OrderStatusEnum::COMPLETE, $updatedOrder->getStatus());
        $this->assertNotEquals(OrderStatusEnum::PENDING, $updatedOrder->getStatus());
    }

    /**
     * @throws InvalidProductQuantityException
     * @throws InvalidOrderStatusException
     * @throws OrderNotFoundException
     */
    public function test_order_status_cannot_be_updated_to_pending_if_complete()
    {
        $order = $this->createOrder();

        $order->setStatus(OrderStatusEnum::COMPLETE);
        $orderUpdate = OrderUpdateDto::from($order->toArray());
        $this->orderService->updateOrderStatus($orderUpdate);

        $this->expectException(InvalidOrderStatusException::class);

        $order->setStatus(OrderStatusEnum::PENDING);
        $orderUpdate = OrderUpdateDto::from($order->toArray());
        $this->orderService->updateOrderStatus($orderUpdate);
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
