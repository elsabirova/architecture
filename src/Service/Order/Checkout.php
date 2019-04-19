<?php

namespace Service\Order;

use Service\Billing\Exception\BillingException;
use Service\Discount\Exception\DiscountException;

class Checkout
{
    /**
     * Checkout process
     *
     * @param BasketBuilder $basketBuilder
     * @param \Model\Entity\Product[] $products
     * @return void
     */
    public function process(BasketBuilder $basketBuilder, array $products): void {
        $totalPrice = 0;

        foreach ($products as $product) {
            $totalPrice += $product->getPrice();
        }

        //Get a discount
        try {
            $discount = $basketBuilder->getDiscount()->getDiscount();
        } catch (DiscountException $e) {
            // error of get discount
            $basketBuilder->getLogger()->log($e->getMessage());
        }

        //Count total price
        $totalPrice = $totalPrice - $totalPrice / 100 * $discount;

        //Payment
        try {
            $basketBuilder->getBilling()->pay($totalPrice);
        } catch (BillingException $e) {
            //error of payment
            $basketBuilder->getLogger()->log($e->getMessage());
        }
    }
}