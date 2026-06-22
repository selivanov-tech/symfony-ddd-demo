<?php

namespace App\Module\Loan\Application\Exception;

use App\Shared\Application\Exception\AbstractNotFoundException;

class ProductNotFoundException extends AbstractNotFoundException
{
    protected function getEntityName(): string
    {
        return 'Product';
    }
}
