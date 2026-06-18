<?php

namespace Api\Module\Customer\Controller;

use App\Module\Customer\Application\Command\CreateCustomer\CreateCustomerCommand;
use App\Module\Customer\Application\Command\UpdateCustomer\UpdateCustomerCommand;
use App\Module\Customer\Application\Query\GetCustomer\GetCustomerQuery;
use App\Module\Customer\Application\Request\CustomerCreateRequest;
use App\Module\Customer\Application\Request\CustomerUpdateRequest;
use App\Shared\Application\Bus\Command\CommandBusInterface;
use App\Shared\Application\Bus\Query\QueryBusInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\UuidV7;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CustomerController
{
    #[Route('customer/create', name: 'create customer', methods: 'POST')]
    public function createCustomer(
        Request $request,
        CommandBusInterface $commandBus,
        ValidatorInterface $validator
    ): JsonResponse {
        $createDTO = new CustomerCreateRequest($request->toArray());

        $errors = $validator->validate($createDTO, groups: ['CustomerCreateRequest']);
        if (count($errors) > 0) {
            throw new ValidationFailedException($createDTO, $errors);
        }

        $id = $commandBus->dispatch(new CreateCustomerCommand(
            email: (string) $createDTO->email,
            phone: (string) $createDTO->phone,
            birthday: (string) $createDTO->birthday,
            firstName: (string) $createDTO->firstName,
            lastName: (string) $createDTO->lastName,
            ssn: (string) $createDTO->ssn,
            ficoScore: (int) $createDTO->ficoScore,
            address: $createDTO->address?->toArray() ?? [],
            monthlyIncome: (int) $createDTO->monthlyIncome,
        ));

        return new JsonResponse(['id' => $id]);
    }

    #[Route('customer/{id}', name: 'edit customer', methods: 'PATCH')]
    public function editCustomer(
        UuidV7 $id,
        Request $request,
        CommandBusInterface $commandBus,
        QueryBusInterface $queryBus,
        ValidatorInterface $validator,
        SerializerInterface $serializer
    ): JsonResponse {
        $updateDTO = new CustomerUpdateRequest($request->toArray());

        $errors = $validator->validate($updateDTO, groups: ['CustomerUpdateRequest']);
        if (count($errors) > 0) {
            throw new ValidationFailedException($updateDTO, $errors);
        }

        $commandBus->dispatch(new UpdateCustomerCommand(
            id: (string) $id,
            email: $updateDTO->email,
            phone: $updateDTO->phone,
            birthday: $updateDTO->birthday,
            firstName: $updateDTO->firstName,
            lastName: $updateDTO->lastName,
            ssn: $updateDTO->ssn,
            ficoScore: $updateDTO->ficoScore,
            address: $updateDTO->address?->toArray(),
            monthlyIncome: $updateDTO->monthlyIncome,
        ));

        $view = $queryBus->ask(new GetCustomerQuery((string) $id));

        return JsonResponse::fromJsonString($serializer->serialize($view, 'json'));
    }

    #[Route('customer/{id}', name: 'get customer', methods: 'GET')]
    public function getCustomer(
        UuidV7 $id,
        QueryBusInterface $queryBus,
        SerializerInterface $serializer
    ): JsonResponse {
        $view = $queryBus->ask(new GetCustomerQuery((string) $id));

        return JsonResponse::fromJsonString($serializer->serialize($view, 'json'));
    }
}
