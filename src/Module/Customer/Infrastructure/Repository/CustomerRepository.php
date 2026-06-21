<?php

namespace App\Module\Customer\Infrastructure\Repository;

use App\Module\Customer\Domain\Entity\Customer;
use App\Module\Customer\Domain\Repository\CustomerRepositoryInterface;
use App\Shared\Infrastructure\Persistence\Doctrine\AbstractDoctrineRepository;

class CustomerRepository extends AbstractDoctrineRepository implements CustomerRepositoryInterface
{
    protected function getEntityClass(): string
    {
        return Customer::class;
    }

    public function findById(string $id): ?Customer
    {
        return $this->find($id);
    }

    public function findByEmail(string $email): ?Customer
    {
        return $this->findOneBy(['email' => $email]);
    }

    public function save(Customer $customer): void
    {
        $this->getEntityManager()->persist($customer);
        $this->getEntityManager()->flush();
    }

    public function remove(Customer $customer): void
    {
        $this->getEntityManager()->remove($customer);
        $this->getEntityManager()->flush();
    }
}
