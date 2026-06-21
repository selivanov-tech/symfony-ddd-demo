<?php

namespace App\Module\Product\Application\Exception;

use App\Shared\Application\Exception\AbstractNotFoundException;

class ProductNotFoundException extends AbstractNotFoundException
{
    protected function getEntityName(): string
    {
        return 'Product';
    }
}
