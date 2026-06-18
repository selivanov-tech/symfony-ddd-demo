<?php

namespace Api\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestController
{
    #[Route('test', name: 'test')]
    public function test(): Response
    {
        return new JsonResponse(['message' => 'test']);
    }
}
