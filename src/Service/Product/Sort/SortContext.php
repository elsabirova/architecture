<?php

namespace Service\Product\Sort;

use Model\Repository\Product;
use Service\Product\Sort\SortTypes\ISort;

class SortContext
{
    protected $sort;

    /**
     * SortContext constructor.
     * @param $sort
     */
    public function __construct(ISort $sort) {
        $this->sort = $sort;
    }

    /**
     * @param Product $product
     * @return \Model\Entity\Product[]
     */
    public function run(Product $product) {
        return $this->sort->run($product);
    }
}