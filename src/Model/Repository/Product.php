<?php

declare(strict_types = 1);

namespace Model\Repository;

use Model\Entity\IProductEntity;

class Product
{
    /**
     * @var $productEntity IProductEntity
     */
    protected $productPrototype;

    /**
     * Product constructor.
     * @param $productEntity IProductEntity
     */
    public function __construct(IProductEntity $productEntity) {
        $this->productPrototype = $productEntity;
    }

    protected function cloneProductEntity(array $product) {
        $productEntity = clone $this->productPrototype;
        $productEntity->setId($product['id']);
        $productEntity->setName($product['name']);
        $productEntity->setPrice($product['price']);

        return $productEntity;
    }

    /**
     * Поиск продуктов по массиву id
     *
     * @param int[] $ids
     * @return IProductEntity[]
     */
    public function search(array $ids = []): array
    {
        if (!count($ids)) {
            return [];
        }

        $productList = [];
        foreach ($this->getDataFromSource(['id' => $ids]) as $item) {
            $productList[] = $this->cloneProductEntity($item);
        }

        return $productList;
    }

    /**
     * Получаем все продукты
     *
     * @return IProductEntity[]
     */
    public function fetchAll(): array
    {
        $productList = [];
        foreach ($this->getDataFromSource() as $item) {
            $productList[] = $this->cloneProductEntity($item);
        }

        return $productList;
    }

    /**
     * Получаем продукты из источника данных
     *
     * @param array $search
     *
     * @return array
     */
    private function getDataFromSource(array $search = [])
    {
        $dataSource = [
            [
                'id' => 1,
                'name' => 'PHP',
                'price' => 15300,
            ],
            [
                'id' => 2,
                'name' => 'Python',
                'price' => 20400,
            ],
            [
                'id' => 3,
                'name' => 'C#',
                'price' => 30100,
            ],
            [
                'id' => 4,
                'name' => 'Java',
                'price' => 30600,
            ],
            [
                'id' => 5,
                'name' => 'Ruby',
                'price' => 18600,
            ],
            [
                'id' => 8,
                'name' => 'Delphi',
                'price' => 8400,
            ],
            [
                'id' => 9,
                'name' => 'C++',
                'price' => 19300,
            ],
            [
                'id' => 10,
                'name' => 'C',
                'price' => 12800,
            ],
            [
                'id' => 11,
                'name' => 'Lua',
                'price' => 5000,
            ],
        ];

        if (!count($search)) {
            return $dataSource;
        }

        $productFilter = function (array $dataSource) use ($search): bool {
            return in_array($dataSource[key($search)], current($search), true);
        };

        return array_filter($dataSource, $productFilter);
    }
}
