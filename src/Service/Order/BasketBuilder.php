<?php

declare(strict_types = 1);

namespace Service\Order;

use Model\Entity\User;
use Service\Log\ILogger;
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
        return new Basket($this);
    }
}