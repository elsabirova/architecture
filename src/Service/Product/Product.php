<?php

declare(strict_types = 1);

namespace Service\Product;

use Model;
use Model\Entity;
use Service\Product\Sort\SortContext;
use Service\Product\Sort\SortTypes\SortByName;
use Service\Product\Sort\SortTypes\SortByPrice;

class Product
{
    /**
     * Получаем информацию по конкретному продукту
     *
     * @param int $id
     * @return Model\Entity\Product|null
     */
    public function getInfo(int $id): ?Model\Entity\Product
    {
        $product = $this->getProductRepository()->search([$id]);
        return count($product) ? $product[0] : null;
    }

    /**
     * Получаем все продукты
     *
     * @param string $sortType
     *
     * @return Model\Entity\Product[]
     */
    public function getAll(string $sortType): array
    {
        $productRepository = $this->getProductRepository();
        // Применить паттерн Стратегия
        // $sortType === 'price'; // Сортировка по цене
        // $sortType === 'name'; // Сортировка по имени

        //Sort by price
        if($sortType === 'price') {
            $sort = new SortContext(new SortByPrice());
        } //Sort by name
        else {
            $sort = new SortContext(new SortByName());
        }

        $productList = $sort->run($productRepository);

        return $productList;
    }

    /**
     * Фабричный метод для репозитория Product
     *
     * @return Model\Repository\Product
     */
    protected function getProductRepository(): Model\Repository\Product
    {
        return new Model\Repository\Product(new Entity\Product());
    }
}
