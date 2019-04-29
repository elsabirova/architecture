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
     * @var \Model\Entity\Product[]
     */
    private $products;

    /**
     * Checkout constructor.
     * @param CheckoutBuilder $checkoutBuilder
     */
    public function __construct(CheckoutBuilder $checkoutBuilder) {
        $this->billing  = $checkoutBuilder->getBilling();
        $this->discount = $checkoutBuilder->getDiscount();
        $this->logger   = $checkoutBuilder->getLogger();
        $this->products = $checkoutBuilder->getProducts();
    }

    /**
     * Checkout process
     *
     * @return bool
     */
    public function process(): bool {
        $totalPrice = $this->calculatePrice();
        $discount   = $this->getDiscount();
        $totalPrice = $this->applyDiscount($totalPrice, $discount);
        $this->pay($totalPrice);
        return true;
    }

    /**
     * Count total price
     *
     * @return float
     */
    public function calculatePrice() {
        $totalPrice = 0;
        foreach ($this->products as $product) {
            $totalPrice += $product->getPrice();
        }

        return $totalPrice;
    }

    /**
     * Get a discount
     *
     * @return float
     */
    public function getDiscount() {
        try {
            return $this->discount->getDiscount();
        } catch (DiscountException $e) {
            // error of get discount
            $this->logger->log($e->getMessage());
        }
    }

    /**
     * Apply discount
     *
     * @param float $totalPrice
     * @param float $discount
     * @return float
     */
    public function applyDiscount(float $totalPrice, float $discount) {
        return $totalPrice - $totalPrice / 100 * $discount;
    }

    /**
     * Payment
     *
     * @param float $totalPrice
     */
    public function pay(float $totalPrice) {
        try {
            $this->billing->pay($totalPrice);
        } catch (BillingException $e) {
            //error of payment
            $this->logger->log($e->getMessage());
        }
    }
}