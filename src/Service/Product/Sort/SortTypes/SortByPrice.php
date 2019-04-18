<?php

namespace Service\Product\Sort\SortTypes;

use Model\Repository\Product;

class SortByPrice implements ISort
{
    public function run(Product $product) {
        // TODO: sort by price
        return $product->fetchAll();
    }

}