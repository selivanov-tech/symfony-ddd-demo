<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use App\Tests\Support\DatabaseTestCase;

final class CustomerApiTest extends DatabaseTestCase
{
    /**
     * @return array<string, mixed>
     */
    private function validPayload(): array
    {
        return [
            'email' => 'jane.doe@example.com',
            'phone' => '5550000001',
            'birthday' => '1990-01-01',
            'firstName' => 'Jane',
            'lastName' => 'Doe',
            'ssn' => '123-45-6789',
            'ficoScore' => 720,
            'address' => [
                'street' => '1 Market St',
                'city' => 'San Francisco',
                'state' => 'CA',
                'zip' => '94105',
            ],
            'monthlyIncome' => 6000,
        ];
    }

    public function testItCreatesACustomer(): void
    {
        $this->jsonRequest('POST', '/customer/create', $this->validPayload());

        self::assertResponseIsSuccessful();
        self::assertArrayHasKey('id', $this->responseJson());
    }

    public function testItRejectsAnInvalidEmail(): void
    {
        $this->jsonRequest('POST', '/customer/create', [...$this->validPayload(), 'email' => 'not-an-email']);

        self::assertResponseStatusCodeSame(400);

        $body = $this->responseJson();
        self::assertArrayHasKey('errors', $body);
    }

    public function testItReturnsACreatedCustomer(): void
    {
        $this->jsonRequest('POST', '/customer/create', $this->validPayload());
        $id = $this->responseJson()['id'];

        $this->client->request('GET', '/customer/' . $id);

        self::assertResponseIsSuccessful();
        self::assertStringContainsString('jane.doe@example.com', (string) $this->client->getResponse()->getContent());
    }
}
