<?php

namespace App\Module\Product\Infrastructure\Repository;

use App\Module\Product\Domain\Entity\Product;
use App\Module\Product\Domain\Repository\ProductRepositoryInterface;
use App\Shared\Infrastructure\Persistence\Doctrine\AbstractDoctrineRepository;

class ProductRepository extends AbstractDoctrineRepository implements ProductRepositoryInterface
{
    protected function getEntityClass(): string
    {
        return Product::class;
    }

    public function findById(string $id): ?Product
    {
        return $this->find($id);
    }

    public function save(Product $product): void
    {
        $this->getEntityManager()->persist($product);
        $this->getEntityManager()->flush();
    }

    public function remove(Product $product): void
    {
        $this->getEntityManager()->remove($product);
        $this->getEntityManager()->flush();
    }
}
