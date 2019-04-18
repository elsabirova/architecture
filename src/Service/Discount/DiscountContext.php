<?php

namespace Service\Discount;

use Service\Discount\DiscountTypes\IDiscount;

/**
 * Class DiscountContext
 * @package Service\Discount
 */
class DiscountContext
{
    /**
     * @var IDiscount $discount
     */
    protected $discount;

    /**
     * @param IDiscount $discount
     */
    public function __construct(IDiscount $discount)
    {
        $this->discount = $discount;
    }

    /**
     * @return float
     * @throws Exception\DiscountException
     */
    public function getDiscount() : float
    {
        return $this->discount->getDiscount();
    }
}