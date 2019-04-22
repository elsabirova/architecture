<?php

declare(strict_types=1);

namespace Service\Order;

use Service\Log\ILogger;
use Service\Billing\BillingContext;
use Service\Discount\DiscountContext;

class CheckoutBuilder
{
    /**
     * @var BillingContext
     */
    private $billing;
    /**
     * @var DiscountContext
     */
    private $discount;
    /**
     * @var ILogger
     */
    private $logger;

    /**
     * @return BillingContext
     */
    public function getBilling() {
        return $this->billing;
    }

    /**
     * @param BillingContext $billing
     */
    public function setBilling(BillingContext $billing): void {
        $this->billing = $billing;
    }

    /**
     * @return DiscountContext
     */
    public function getDiscount() {
        return $this->discount;
    }

    /**
     * @param DiscountContext $discount
     */
    public function setDiscount(DiscountContext $discount): void {
        $this->discount = $discount;
    }

    /**
     * @return ILogger
     */
    public function getLogger(): ILogger {
        return $this->logger;
    }

    /**
     * @param ILogger $logger
     */
    public function setLogger(ILogger $logger): void {
        $this->logger = $logger;
    }

    public function build() {
        return new Checkout($this);
    }
}