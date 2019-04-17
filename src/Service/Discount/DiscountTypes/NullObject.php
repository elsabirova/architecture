<?php

declare(strict_types = 1);

namespace Service\Discount\DiscountTypes;

class NullObject implements IDiscount
{
    /**
     * @inheritdoc
     */
    public function getDiscount(): float
    {
        // Скидка отсутствует
        return 0;
    }
}
