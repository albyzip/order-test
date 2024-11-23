<?php

namespace App\Console\Commands;

use App\DTO\OrderCreateDto;
use App\Services\OrderService;
use App\Traits\OrderTableHeaders;
use Illuminate\Console\Command;

class OrderCreateCommand extends Command
{
    use OrderTableHeaders;
    protected $signature = 'order:create
                            {--created_at= : Дата создания в формате d.m.Y H:i:s}
                            {--product_name= : Наименование товара}
                            {--product_unit_price= : Цена за единицу товара}
                            {--product_quantity= : Количество единиц товара}';

    protected $description = 'Создание заказа';
    protected OrderService $orderService;
    public function __construct(OrderService $orderService)
    {
        parent::__construct();
        $this->orderService = $orderService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $order = OrderCreateDto::from($this->options());

        if (!$order->created_at || !$order->product_name || !$order->product_unit_price || !$order->product_quantity) {
            $this->error('Все параметры обязательны для заполнения.');
            return;
        }

        try {
            $order = $this->orderService->createOrder($order);

            $this->info('Заказ успешно создан:');
            $this->table(
                $this->getHeaders(),
                [$order->toArray()]
            );

        } catch (\Exception $e) {
            $this->error('Ошибка при создании заказа: ' . $e->getMessage());
        }
    }
}
