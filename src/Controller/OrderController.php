<?php

declare(strict_types = 1);

namespace Controller;

use Framework\Render;
use Service\Log\Logger;
use Service\Order\BasketBuilder;
use Service\User\Security;
use Service\Order\Observer\CheckoutObserver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderController
{
    use Render;

    /**
     * Корзина
     *
     * @param Request $request
     * @return Response
     */
    public function infoAction(Request $request): Response
    {
        if ($request->isMethod(Request::METHOD_POST)) {
            return $this->redirect('order_checkout');
        }

        $security = new Security($request->getSession());
        $user = $security->getUser();

        $basketBuilder = (new BasketBuilder());
        $basketBuilder->setSession($request->getSession());
        $basketBuilder->setUser($user);
        $basketBuilder->setLogger(new Logger());
        $basket = $basketBuilder->build();

        $productList = $basket->getProductsInfo();

        $isLogged = $security->isLogged();

        return $this->render('order/info.html.php', ['productList' => $productList, 'isLogged' => $isLogged]);
    }

    /**
     * Оформление заказа
     *
     * @param Request $request
     * @return Response
     */
    public function checkoutAction(Request $request): Response
    {
        $security = new Security($request->getSession());
        $isLogged = $security->isLogged();
        if (!$isLogged) {
            return $this->redirect('user_authentication');
        }

        $user = $security->getUser();
        $observer = new CheckoutObserver();

        $basketBuilder = (new BasketBuilder());
        $basketBuilder->setSession($request->getSession());
        $basketBuilder->setUser($user);
        $basketBuilder->setLogger(new Logger());

        $basket = $basketBuilder->build();
        $basket->attach($observer);
        $basket->checkout();

        return $this->render('order/checkout.html.php');
    }
}
