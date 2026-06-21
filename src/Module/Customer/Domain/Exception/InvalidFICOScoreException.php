<?php

namespace App\Module\Customer\Domain\Exception;

use DomainException;

class InvalidFICOScoreException extends DomainException
{
    public function __construct(int $score)
    {
        parent::__construct("Invalid FICO score: $score. Must be between 300 and 850.");
    }
}
