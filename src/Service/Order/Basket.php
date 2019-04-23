<?php

declare(strict_types = 1);

namespace Service\Order;

use Model;
use Model\Entity;
use Model\Entity\User;
use SplObserver;
use SplObjectStorage;
use Service\Log\ILogger;
use Service\Billing\BillingContext;
use Service\Billing\BillingTypes\Card;
use Service\Billing\BillingTypes\BankTransfer;
use Service\Discount\DiscountContext;
use Service\Discount\DiscountTypes\NullObject;
use Service\Discount\DiscountTypes\PromoCode;
use Service\Discount\DiscountTypes\VipDiscount;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Basket implements \SplSubject
{
    /**
     * Сессионный ключ списка всех продуктов корзины
     */
    private const BASKET_DATA_KEY = 'basket';

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var SplObjectStorage
     */
    private $observers;
    /**
     * @var ILogger
     */
    private $logger;

    /**
     * Basket constructor.
     * @param BasketBuilder $basketBuilder
     */
    public function __construct(BasketBuilder $basketBuilder)
    {
        $this->session = $basketBuilder->getSession();
        $this->user = $basketBuilder->getUser();
        $this->logger = $basketBuilder->getLogger();

        $this->observers = new SplObjectStorage();
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
     * @param SplObserver $observer
     */
    public function attach(SplObserver $observer) {
        $this->observers->attach($observer);
    }

    /**
     * @param SplObserver $observer
     */
    public function detach(SplObserver $observer) {
        $this->observers->detach($observer);
    }

    public function notify() {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }

    /**
     * Добавляем товар в заказ
     *
     * @param int $product
     *
     * @return void
     */
    public function addProduct(int $product): void
    {
        $basket = $this->session->get(static::BASKET_DATA_KEY, []);
        if (!in_array($product, $basket, true)) {
            $basket[] = $product;
            $this->session->set(static::BASKET_DATA_KEY, $basket);
        }
    }

    /**
     * Проверяем, лежит ли продукт в корзине или нет
     *
     * @param int $productId
     *
     * @return bool
     */
    public function isProductInBasket(int $productId): bool
    {
        return in_array($productId, $this->getProductIds(), true);
    }

    /**
     * Получаем информацию по всем продуктам в корзине
     *
     * @return Model\Entity\Product[]
     */
    public function getProductsInfo(): array
    {
        $productIds = $this->getProductIds();
        return $this->getProductRepository()->search($productIds);
    }

    /**
     * Checkout
     *
     * @param CheckoutBuilder $checkoutBuilder
     * @return void
     */
    public function checkout(CheckoutBuilder $checkoutBuilder): void
    {
        //Choose a way to payment Card or BankTransfer
        $checkoutBuilder->setBilling(new BillingContext(new Card()));
        //Choose a way to get discount
        //new VipDiscount($this->user)
        //new PromoCode('month_discount')
        //new NullObject()
        $checkoutBuilder->setDiscount(new DiscountContext(new VipDiscount($this->user)));
        $checkoutBuilder->setLogger($this->logger);

        //Build Checkout
        $checkout = $checkoutBuilder->build();
        $checkout->process($this->getProductsInfo());

        //Notification of observers
        $this->notify();
    }

    /**
     * Фабричный метод для репозитория Product
     *
     * @return Model\Repository\Product
     */
    protected function getProductRepository(): Model\Repository\Product
    {
        return new Model\Repository\Product(new Entity\Product());
    }

    /**
     * Получаем список id товаров корзины
     *
     * @return array
     */
    private function getProductIds(): array
    {
        return $this->session->get(static::BASKET_DATA_KEY, []);
    }
}
