<?php

namespace App\Console\Commands;

use App\DTO\OrderUpdateDto;
use App\Exceptions\InvalidOrderStatusException;
use App\Services\OrderService;
use App\Traits\OrderTableHeaders;
use Illuminate\Console\Command;

class OrderUpdateCommand extends Command
{
    use OrderTableHeaders;
    protected $signature = 'order:update {id} {--status=}';
    protected $description = 'Обновить статус заказа';

    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        parent::__construct();
        $this->orderService = $orderService;
    }

    /**
     * @throws InvalidOrderStatusException
     */
    public function handle()
    {
        $id = $this->argument('id');
        $status = $this->option('status');

        $order = OrderUpdateDto::from([
            'id' => $id,
            'status' => $status
        ]);

        if (!$order->status) {
            $this->error('Статус обязателен для заполнения.');
            return;
        }

        try {
            $order = $this->orderService->updateOrderStatus($order);

            $this->table(
                $this->getHeaders(),
                [$order->toArray()]
            );

        } catch (InvalidOrderStatusException $e) {
            throw new InvalidOrderStatusException($e->getMessage());
        }
        catch (\Exception $e) {
            $this->error('Ошибка при обновлении статуса заказа: ' . $e->getMessage());
        }
    }
}
