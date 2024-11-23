<?php

namespace App\Traits;

trait OrderTableHeaders
{
    public function getHeaders(): array
    {
        return [
            'ID',
            'Наименование товара',
            'Статус',
            'Цена за единицу',
            'Количество',
            'Сумма',
            'Дата создания',
            'Дата обновления',
        ];
    }
}
