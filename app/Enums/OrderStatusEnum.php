<?php

namespace App\Enums;

enum OrderStatusEnum: string
{
    case PENDING = 'pending';
    case COMPLETE = 'complete';

    public function equals(self $status): bool
    {
        return $this->value === $status->value;
    }
}
