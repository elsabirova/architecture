<?php

declare(strict_types = 1);

namespace Service\Order;

use Service\Billing\BillingContext;
use Service\Billing\Exception\BillingException;
use Service\Discount\DiscountContext;
use Service\Discount\Exception\DiscountException;
use Service\Log\ILogger;

class Checkout
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
     * Checkout constructor.
     * @param CheckoutBuilder $checkoutBuilder
     */
    public function __construct(CheckoutBuilder $checkoutBuilder) {
        $this->billing = $checkoutBuilder->getBilling();
        $this->discount = $checkoutBuilder->getDiscount();
        $this->logger = $checkoutBuilder->getLogger();
    }

    /**
     * Checkout process
     *
     * @param \Model\Entity\Product[] $products
     * @return void
     */
    public function process(array $products): void {
        $totalPrice = 0;

        foreach ($products as $product) {
            $totalPrice += $product->getPrice();
        }

        //Get a discount
        try {
            $discount = $this->discount->getDiscount();
        } catch (DiscountException $e) {
            // error of get discount
            $this->logger->log($e->getMessage());
        }

        //Count total price
        $totalPrice = $totalPrice - $totalPrice / 100 * $discount;

        //Payment
        try {
            $this->billing->pay($totalPrice);
        } catch (BillingException $e) {
            //error of payment
            $this->logger->log($e->getMessage());
        }
    }
}