<?php

declare(strict_types=1);

namespace Controller;

use Framework\Render;
use Service\Billing\BillingContext;
use Service\Billing\BillingTypes\BankTransfer;
use Service\Billing\BillingTypes\Card;
use Service\Discount\DiscountContext;
use Service\Discount\DiscountTypes\NullObject;
use Service\Discount\DiscountTypes\PromoCode;
use Service\Discount\DiscountTypes\VipDiscount;
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
    public function infoAction(Request $request): Response {
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
    public function checkoutAction(Request $request): Response {
        $security = new Security($request->getSession());
        $isLogged = $security->isLogged();
        if (!$isLogged) {
            return $this->redirect('user_authentication');
        }

        $basket = (new BasketDirector)->build($request, $security);

        $observer = new CheckoutObserver();
        $basket->attach($observer);

        if ($request->isMethod(Request::METHOD_POST)) {
            $checkoutBuilder = new CheckoutBuilder();
            //Choose a way to payment
            switch ($request->request->get('billing')) {
                case 'card':
                    $checkoutBuilder->setBilling(new BillingContext(new Card()));
                    break;

                case 'bankTransfer':
                    $checkoutBuilder->setBilling(new BillingContext(new BankTransfer()));
                    break;

                default:
                    $checkoutBuilder->setBilling(new BillingContext(new Card()));
            }
            //Choose a way to get discount
            switch ($request->request->get('discount')) {
                case 'card':
                    $checkoutBuilder->setDiscount(new DiscountContext(new VipDiscount($security->getUser())));
                    break;

                case 'bankTransfer':
                    $checkoutBuilder->setDiscount(new DiscountContext(new PromoCode($request->request->get('promoCode'))));
                    break;

                default:
                    $checkoutBuilder->setDiscount(new DiscountContext(new NullObject()));
            }
            $checkout = $basket->checkout($checkoutBuilder);
            if ($checkout) {
                $response = 'Purchase completed successfully';
            } else {
                $response = 'Purchase error. Try again.';
            }
            $orderForm = '';
        } else {
            $orderForm = $basket->renderOrderForm();
            $response = '';
        }

        return $this->render('order/checkout.html.php', ['orderForm' => $orderForm, 'response' => $response]);
    }
}
