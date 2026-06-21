<?php

namespace App\Module\Product\Domain\Repository;

use App\Module\Product\Domain\Entity\Product;

interface ProductRepositoryInterface
{
    public function findById(string $id): ?Product;

    public function save(Product $customer): void;

    public function remove(Product $customer): void;
}
