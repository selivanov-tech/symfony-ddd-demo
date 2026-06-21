<?php

namespace App\Module\Loan\Application\Request;

use Symfony\Component\Validator\Constraints as Assert;

class LoanUserRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public readonly string $productId,
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public readonly string $customerId,
        #[Assert\IsTrue]
        public readonly bool $onlyCheck = false,
    ) {
    }
}
