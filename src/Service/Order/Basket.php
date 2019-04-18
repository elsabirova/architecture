<?php

declare(strict_types = 1);

namespace Service\Order;

use Model;
use SplObserver;
use SplObjectStorage;
use Model\Entity\User;
use Service\Billing\Card;
use Service\Billing\IBilling;
use Service\Discount\DiscountIdentifier;
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
     * Basket constructor.
     * @param SessionInterface $session
     * @param User $user
     */
    public function __construct(SessionInterface $session, User $user)
    {
        $this->session = $session;
        $this->user = $user;
        $this->observers = new SplObjectStorage();
    }

    public function getUser() {
        return $this->user;
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
     * Оформление заказа
     *
     * @return void
     * @throws \Service\Billing\Exception\BillingException
     */
    public function checkout(): void
    {
        // Здесь должна быть некоторая логика выбора способа платежа
        $billing = new Card();

        // Здесь должна быть некоторая логика получения информации о скидки пользователя
        $discount = new DiscountIdentifier();
        //Выбираем тип скидки
        $discount->setDiscount(new VipDiscount($this->user));
        //$discount->setDiscount(new NullObject());
        //$discount->setDiscount(new PromoCode('month_discount'));

        $this->checkoutProcess($discount, $billing);
    }

    /**
     * Проведение всех этапов заказа
     *
     * @param DiscountIdentifier $discount
     * @param IBilling $billing
     * @return void
     * @throws \Service\Billing\Exception\BillingException
     */
    public function checkoutProcess(
        DiscountIdentifier $discount,
        IBilling $billing
    ): void {
        $totalPrice = 0;
        foreach ($this->getProductsInfo() as $product) {
            $totalPrice += $product->getPrice();
        }

        //Get a discount
        try {
            $discount = $discount->getDiscount();
        }
        catch (\Exception $e) {
            // discount not defined
        }
        $totalPrice = $totalPrice - $totalPrice / 100 * $discount;

        $billing->pay($totalPrice);

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
        return new Model\Repository\Product();
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
