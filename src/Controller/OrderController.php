<?php

declare(strict_types = 1);

namespace Controller;

use Framework\Render;
use Service\Order\CheckoutBuilder;
use Service\Order\BasketDirector;
use Service\User\Security;
use Service\Order\Observer\CheckoutObserver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderController
{
    use Render;

    /**
     * Basket
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

        $basket = (new BasketDirector)->build($request, $security);

        $productList = $basket->getProductsInfo();

        $isLogged = $security->isLogged();

        return $this->render('order/info.html.php', ['productList' => $productList, 'isLogged' => $isLogged]);
    }

    /**
     * Checkout
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

        $observer = new CheckoutObserver();

        $basket = (new BasketDirector)->build($request, $security);
        $basket->attach($observer);
        $basket->checkout(new CheckoutBuilder());

        return $this->render('order/checkout.html.php');
    }
}
