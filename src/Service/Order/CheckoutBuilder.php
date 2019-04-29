<?php

declare(strict_types=1);

namespace Service\Order;

use Model\Entity\User;
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
     * @return User
     */
    public function getUser(): User {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void {
        $this->user = $user;
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

    /**
     * @return \Model\Entity\Product[]
     */
    public function getProducts(): array {
        return $this->products;
    }

    /**
     * @param \Model\Entity\Product[] $products
     */
    public function setProducts(array $products): void {
        $this->products = $products;
    }

    public function build() {
        return new Checkout($this);
    }
}