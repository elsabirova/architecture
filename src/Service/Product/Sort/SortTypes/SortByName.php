<?php

namespace Service\Product\Sort\SortTypes;

use Model\Repository\Product;

class SortByName implements ISort
{
    public function run(Product $product) {
        // TODO: sort by name
        return $product->fetchAll();
    }
}