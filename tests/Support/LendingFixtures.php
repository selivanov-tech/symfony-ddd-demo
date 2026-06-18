<?php

declare(strict_types=1);

namespace App\Tests\Support;

use App\Domain\Customer\Entity\Customer;
use App\Domain\Product\Entity\Product;
use App\Shared\Domain\Identity\UuidFactoryInterface;
use App\Shared\Infrastructure\Identity\SymfonyUuidFactory;
use App\Tests\Builder\CustomerBuilder;
use App\Tests\Builder\ProductBuilder;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Persists the loan-domain entities feature tests need.
 *
 * Builder defaults describe an eligible applicant in CA; pass a configured
 * builder to exercise the denial paths.
 */
trait LendingFixtures
{
    private UuidFactoryInterface $uuidFactory;

    private function customerBuilder(): CustomerBuilder
    {
        return new CustomerBuilder($this->uuidFactory());
    }

    private function productBuilder(): ProductBuilder
    {
        return new ProductBuilder($this->uuidFactory());
    }

    private function createCustomer(EntityManagerInterface $em, ?CustomerBuilder $builder = null): Customer
    {
        $customer = ($builder ?? $this->customerBuilder())->build();
        $em->persist($customer);
        $em->flush();

        return $customer;
    }

    private function createProduct(EntityManagerInterface $em, ?ProductBuilder $builder = null): Product
    {
        $product = ($builder ?? $this->productBuilder())->build();
        $em->persist($product);
        $em->flush();

        return $product;
    }

    private function uuidFactory(): UuidFactoryInterface
    {
        return $this->uuidFactory ??= new SymfonyUuidFactory();
    }
}
