<?php

declare(strict_types = 1);

namespace Service\Discount\DiscountTypes;

interface IDiscount
{
    /**
     * Получаем скидку в процентах
     *
     * @return float
     */
    public function getDiscount(): float;
}
