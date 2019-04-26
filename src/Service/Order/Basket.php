<?php

declare(strict_types=1);

namespace Service\Order;

use Model;
use Model\Entity;
use Model\Entity\User;
use Service\BuilderForm\Fieldset;
use Service\BuilderForm\Form;
use Service\BuilderForm\Input;
use Service\BuilderForm\Select;
use Service\BuilderForm\Textarea;
use SplObserver;
use SplObjectStorage;
use Service\Log\ILogger;
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
    public function __construct(BasketBuilder $basketBuilder) {
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
    public function addProduct(int $product): void {
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
    public function isProductInBasket(int $productId): bool {
        return in_array($productId, $this->getProductIds(), true);
    }

    /**
     * Получаем информацию по всем продуктам в корзине
     *
     * @return Model\Entity\Product[]
     */
    public function getProductsInfo(): array {
        $productIds = $this->getProductIds();
        return $this->getProductRepository()->search($productIds);
    }

    /**
     * Checkout
     *
     * @param CheckoutBuilder $checkoutBuilder
     * @return bool
     */
    public function checkout(CheckoutBuilder $checkoutBuilder): bool {

        $checkoutBuilder->setLogger($this->logger);

        //Build Checkout
        $checkout = $checkoutBuilder->build();
        $result = $checkout->process($this->getProductsInfo());

        //Notification of observers
        if ($result) {
            $this->notify();
        }

        return $result;
    }

    public function renderOrderForm() {
        $orderForm = new Form(['key' => 'orderForm', 'title' => 'Checkout', 'class' => 'order-form', 'action' => '/order/checkout', 'method' => 'post']);
        $fullName = new Input(['key' => 'fullName', 'name' => 'fullName', 'class' => 'order-client-full-name', 'type' => 'text', 'placeholder' => 'Enter full name']);
        $email = new Input(['key' => 'email', 'name' => 'email', 'class' => 'order-client-email', 'type' => 'email', 'placeholder' => 'Enter email']);
        $billingFieldset = new Fieldset(['key' => 'billingFieldset', 'title' => 'Choose payment method']);
        $radioButtonCard = new Input(['key' => 'billingCard', 'name' => 'billing', 'type' => 'radio', 'value' => 'card', 'title' => 'Card', 'checked' => true]);
        $radioButtonBankTransfer = new Input(['key' => 'billingTransfer', 'name' => 'billing', 'type' => 'radio', 'value' => 'bankTransfer', 'title' => 'Bank transfer']);
        $billingFieldset
            ->add($radioButtonCard)
            ->add($radioButtonBankTransfer);
        $discountFieldset = new Fieldset(['key' => 'discountFieldset', 'title' => 'Choose discount']);
        $radioButtonCode = new Input(['key' => 'discountCode', 'name' => 'discount', 'type' => 'radio', 'value' => 'code', 'title' => 'Promo code']);
        $radioButtonVip = new Input(['key' => 'discountVip', 'name' => 'discount', 'type' => 'radio', 'value' => 'vip', 'title' => 'Vip ']);
        $promoCode = new Input(['key' => 'promoCode', 'name' => 'promoCode', 'type' => 'text', 'placeholder' => 'Enter promo code']);
        $discountFieldset
            ->add($radioButtonVip)
            ->add($radioButtonCode)
            ->add($promoCode);

        $notification = new Select(['key' => 'notification', 'name' => 'notification', 'class' => 'notification', 'title' => 'Notification ', 'values' => ['sms' => 'Sms', 'email' => 'Email']]);
        $button = new Input(['key' => 'orderButton', 'name' => 'orderButton', 'class' => 'order-button', 'type' => 'submit', 'value' => 'Checkout']);
        $comment = new Textarea(['key' => 'comment', 'name' => 'comment', 'title' => 'Comment', 'class' => 'order-comment', 'rows' => '4', 'cols' => '50']);
        $orderForm
            ->add($fullName)
            ->add($email)
            ->add($billingFieldset)
            ->add($discountFieldset)
            ->add($comment)
            ->add($notification)
            ->add($button);

        return $orderForm->render();
    }

    /**
     * Фабричный метод для репозитория Product
     *
     * @return Model\Repository\Product
     */
    protected function getProductRepository(): Model\Repository\Product {
        return new Model\Repository\Product(new Entity\Product());
    }

    /**
     * Получаем список id товаров корзины
     *
     * @return array
     */
    private function getProductIds(): array {
        return $this->session->get(static::BASKET_DATA_KEY, []);
    }
}
