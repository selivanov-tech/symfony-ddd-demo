<?php

namespace App\Module\Loan\Application\Request;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class LoanUserRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        #[OA\Property(example: '0193b3e9-5a7c-7e2a-9f10-2c8b1d4e6f01')]
        public readonly string $productId,
        #[Assert\NotBlank]
        #[Assert\Uuid]
        #[OA\Property(example: '0193b3e9-7b1d-7c44-8a90-3f2e1a9c7d22')]
        public readonly string $customerId,
    ) {
    }
}
