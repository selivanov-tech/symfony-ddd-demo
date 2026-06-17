<?php

namespace App\Infrastructure\Service\Exception;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Throwable;

class ExceptionTransformer
{
    public function transform(Throwable $exception): JsonResponse
    {
        $status = Response::HTTP_INTERNAL_SERVER_ERROR;

        if ($exception instanceof HttpExceptionInterface) {
            $status = $exception->getStatusCode();
        }

        if ($exception instanceof ValidationFailedException) {
            return $this->handleValidationErrors($exception);
        }

        return new JsonResponse(
            data: [
                'error' => $exception->getMessage(),
                'details' => [/* todo */],
            ],
            status: $status
        );
    }

    private function handleValidationErrors(ValidationFailedException $exception): JsonResponse
    {
        $violations = $exception->getViolations();

        $errors = [];
        foreach ($violations as $violation) {
            $errors[] = [
                'field' => $violation->getPropertyPath(),
                'message' => $violation->getMessage(),
            ];
        }

        return new JsonResponse(['errors' => $errors], JsonResponse::HTTP_BAD_REQUEST);
    }
}
