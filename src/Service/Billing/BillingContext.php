<?php

namespace Service\Billing;

use Service\Billing\BillingTypes\IBilling;

class BillingContext
{
    /**
     * @var IBilling $billing
     */
    protected $billing;

    /**
     * @param IBilling $billing
     */
    public function __construct(IBilling $billing)
    {
        $this->billing = $billing;
    }

    /**
     * @param float $totalPrice
     * @throws Exception\BillingException
     */
    public function pay(float $totalPrice)
    {
        $this->billing->pay($totalPrice);
    }
}