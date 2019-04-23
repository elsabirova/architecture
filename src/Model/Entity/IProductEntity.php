<?php

namespace Model\Entity;

interface IProductEntity
{
    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return float
     */
    public function getPrice(): float;

    /**
     * @param int $id
     */
    public function setId(int $id): void;

    /**
     * @param string $name
     */
    public function setName(string $name): void;

    /**
     * @param float $price
     */
    public function setPrice(float $price): void;

    /**
     * @return array
     */
    public function toArray(): array;
}