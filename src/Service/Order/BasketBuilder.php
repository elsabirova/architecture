<?php

namespace Service\Order;

use Model\Entity\User;
use Service\Log\ILogger;
use Service\Billing\BillingContext;
use Service\Discount\DiscountContext;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class BasketBuilder
{
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var User
     */
    private $user;
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
     * @return SessionInterface
     */
    public function getSession(): SessionInterface {
        return $this->session;
    }

    /**
     * @param SessionInterface $session
     */
    public function setSession(SessionInterface $session): void {
        $this->session = $session;
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
     * @param DiscountContext $discount
     */
    public function setDiscount(DiscountContext $discount): void {
        $this->discount = $discount;
    }

    public function build() {
        return new Basket($this);
    }
}