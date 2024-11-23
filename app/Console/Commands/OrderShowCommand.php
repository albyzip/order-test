<?php

namespace App\Console\Commands;

use App\Services\OrderService;
use App\Traits\OrderTableHeaders;
use Illuminate\Console\Command;

class OrderShowCommand extends Command
{
    use OrderTableHeaders;
    protected $signature = 'order:show {id}';
    protected $description = 'Получить детали заказа по ID';
    protected OrderService $orderService;


    public function __construct(OrderService $orderService)
    {
        parent::__construct();
        $this->orderService = $orderService;
    }

    public function handle()
    {
        $id = $this->argument('id');

        try {
            $order = $this->orderService->showOrder($id);

            if (!$order) {
                $this->error('Заказ с ID ' . $id . ' не найден.');
                return;
            }

            $this->table(
                $this->getHeaders(),
                [$order->toArray()]
            );

        } catch (\Exception $e) {
            $this->error('Ошибка при получении деталей заказа: ' . $e->getMessage());
        }
    }
}
