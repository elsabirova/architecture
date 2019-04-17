<?php

namespace Service\Discount;

use Service\Discount\DiscountTypes\IDiscount;

/**
 * Class DiscountIdentifier
 * @package Service\Discount
 */
class DiscountIdentifier
{
    /**
     * @var IDiscount $discount
     */
    protected $discount;

    /**
     * @param IDiscount $discount
     */
    public function setDiscount(IDiscount $discount)
    {
        $this->discount = $discount;
    }

    /**
     * @return float
     */
    public function getDiscount() : float
    {
        return $this->discount->getDiscount();
    }
}