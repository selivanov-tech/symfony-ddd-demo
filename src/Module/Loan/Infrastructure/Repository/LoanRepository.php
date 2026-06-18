<?php

namespace App\Module\Loan\Infrastructure\Repository;

use App\Module\Loan\Domain\Entity\Loan;
use App\Module\Loan\Domain\Repository\LoanRepositoryInterface;
use App\Shared\Infrastructure\Persistence\Doctrine\AbstractDoctrineRepository;

class LoanRepository extends AbstractDoctrineRepository implements LoanRepositoryInterface
{
    protected function getEntityClass(): string
    {
        return Loan::class;
    }

    public function findById(string $id): ?Loan
    {
        return $this->find($id);
    }

    public function findByCustomerId(string $customerId): array
    {
        return $this->findBy(['customerId' => $customerId]);
    }

    public function save(Loan $loan): void
    {
        $this->getEntityManager()->persist($loan);
        $this->getEntityManager()->flush();
    }

    public function remove(Loan $loan): void
    {
        $this->getEntityManager()->remove($loan);
        $this->getEntityManager()->flush();
    }
}
