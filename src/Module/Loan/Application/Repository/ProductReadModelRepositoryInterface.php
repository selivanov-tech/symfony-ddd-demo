<?php

declare(strict_types=1);

namespace App\Module\Loan\Application\Repository;

use App\Module\Loan\Application\ReadModel\ProductOffer;

interface ProductReadModelRepositoryInterface
{
    public function findById(string $id): ?ProductOffer;
}
