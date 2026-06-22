<?php

declare(strict_types=1);

namespace App\Module\Loan\Application\Repository;

use App\Module\Loan\Application\ReadModel\ApplicantProfile;

interface ApplicantReadModelRepositoryInterface
{
    public function findById(string $id): ?ApplicantProfile;
}
