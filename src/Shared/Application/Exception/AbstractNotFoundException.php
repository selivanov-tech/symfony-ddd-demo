<?php

namespace App\Shared\Application\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

abstract class AbstractNotFoundException extends NotFoundHttpException
{
    public function __construct(string $message = '', ?Throwable $previous = null, int $code = 0, array $headers = [])
    {
        if (empty($message)) {
            $message = sprintf('%s not found.', $this->getEntityName());
        }

        parent::__construct(...func_get_args());
    }

    abstract protected function getEntityName(): string;
}
