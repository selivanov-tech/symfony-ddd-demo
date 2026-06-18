<?php

namespace App\Module\Loan\Domain\Repository;

use App\Module\Loan\Domain\Entity\Loan;

interface LoanRepositoryInterface
{
    public function findById(string $id): ?Loan;

    public function findByCustomerId(string $customerId): array;

    public function save(Loan $loan): void;

    public function remove(Loan $loan): void;
}
