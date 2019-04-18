<?php

declare(strict_types = 1);

namespace Service\Product\Sort\SortTypes;

use Model\Repository\Product;

interface ISort
{
    /**
     * @param Product $product
     * @return \Model\Entity\Product[]
     */
    public function run(Product $product);
}