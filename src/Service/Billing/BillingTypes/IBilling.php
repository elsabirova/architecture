<?php

declare(strict_types = 1);

namespace Service\Billing\BillingTypes;

use Service\Billing\Exception\BillingException;

interface IBilling
{
    /**
     * Рассчёт стоимости доставки заказа
     *
     * @param float $totalPrice
     *
     * @return void
     *
     * @throws BillingException
     */
    public function pay(float $totalPrice): void;
}
