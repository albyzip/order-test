<?php

namespace App\Console\Commands;

use App\DTO\OrderShowDto;
use App\Services\OrderService;
use App\Traits\OrderTableHeaders;
use Illuminate\Console\Command;

class OrderListCommand extends Command
{
    use OrderTableHeaders;

    protected $signature = 'order:list';
    protected $description = 'Получить список всех заказов';

    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        parent::__construct();
        $this->orderService = $orderService;
    }

    public function handle()
    {
        try {
            $orders = $this->orderService->listOrders();

            if (empty($orders)) {
                $this->info('Нет заказов.');
                return;
            }

            $ordersArray = array_map(function (OrderShowDto $order) {
                return $order->toArray();
            }, $orders);

            $this->info('Список всех заказов:');

            $this->table(
                $this->getHeaders(),
                $ordersArray
            );
        } catch (\Exception $e) {
            $this->error('Ошибка при получении списка заказов: ' . $e->getMessage());
        }
    }
}
