<?php

declare(strict_types = 1);

namespace Service\Order;

use Model\Entity\User;
use Service\Billing\BillingContext;
use Service\Billing\Exception\BillingException;
use Service\Discount\DiscountContext;
use Service\Discount\Exception\DiscountException;
use Service\Log\ILogger;

class Checkout implements \SplSubject
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
     * @var User
     */
    protected $user;
    /**
     * @var ILogger
     */
    private $logger;

    /**
     * @var \Model\Entity\Product[]
     */
    private $products;

    /**
     * @var \SplObjectStorage
     */
    private $observers;

    /**
     * Checkout constructor.
     * @param CheckoutBuilder $checkoutBuilder
     */
    public function __construct(CheckoutBuilder $checkoutBuilder) {
        $this->billing  = $checkoutBuilder->getBilling();
        $this->discount = $checkoutBuilder->getDiscount();
        $this->user   = $checkoutBuilder->getUser();
        $this->logger   = $checkoutBuilder->getLogger();
        $this->products = $checkoutBuilder->getProducts();
        $this->observers = new \SplObjectStorage();
    }
    /**
     * @return User
     */
    public function getUser(): User {
        return $this->user;
    }

    /**
     * @return ILogger
     */
    public function getLogger(): ILogger {
        return $this->logger;
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
        //Notification of user (observers)
        $this->notify();
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

    /**
     * @param \SplObserver $observer
     */
    public function attach(\SplObserver $observer) {
        $this->observers->attach($observer);
    }

    /**
     * @param \SplObserver $observer
     */
    public function detach(\SplObserver $observer) {
        $this->observers->detach($observer);
    }

    public function notify() {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }
}