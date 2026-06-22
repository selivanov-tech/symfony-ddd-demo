<?php

namespace Api\Module\Customer\Controller;

use App\Module\Customer\Application\Query\GetCustomer\GetCustomerQuery;
use App\Module\Customer\Application\ReadModel\CustomerView;
use App\Shared\Application\Bus\Query\QueryBusInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\UuidV7;

#[OA\Tag(name: 'Customer')]
class GetCustomerController
{
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid'))]
    #[OA\Response(response: 200, description: 'The customer', content: new Model(type: CustomerView::class))]
    #[OA\Response(response: 404, description: 'Customer not found')]
    #[Route('customer/{id}', name: 'get customer', methods: 'GET')]
    public function __invoke(
        UuidV7 $id,
        QueryBusInterface $queryBus,
        SerializerInterface $serializer,
    ): JsonResponse {
        $view = $queryBus->ask(new GetCustomerQuery((string) $id));

        return JsonResponse::fromJsonString($serializer->serialize($view, 'json'));
    }
}
