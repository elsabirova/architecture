<?php

declare(strict_types = 1);

namespace Service\Discount\DiscountTypes;

use Service\Discount\Exception\DiscountException;

interface IDiscount
{
    /**
     * Получаем скидку в процентах
     *
     * @return float
     *
     * @throws DiscountException
     */
    public function getDiscount(): float;
}
