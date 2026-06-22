<?php

namespace App\Module\Loan\Application\Exception;

use App\Shared\Application\Exception\AbstractNotFoundException;

class CustomerNotFoundException extends AbstractNotFoundException
{
    protected function getEntityName(): string
    {
        return 'Customer';
    }
}
