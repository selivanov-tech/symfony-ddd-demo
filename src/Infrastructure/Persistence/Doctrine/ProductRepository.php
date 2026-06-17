<?php

namespace App\Infrastructure\Persistence\Doctrine;

use App\Domain\Product\Entity\Product;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Abstract\AbstractDoctrineRepository;

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
