<?php

declare(strict_types = 1);

namespace Service\Billing\BillingTypes;

class Card implements IBilling
{
    /**
     * @inheritdoc
     */
    public function pay(float $totalPrice): void
    {
        // Оплата кредитной или дебетовой картой
    }
}
