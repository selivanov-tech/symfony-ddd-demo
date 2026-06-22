<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ApiDocTest extends WebTestCase
{
    public function testItServesTheOpenApiSpec(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/doc.json');

        self::assertResponseIsSuccessful();

        $spec = (array) json_decode((string) $client->getResponse()->getContent(), true);
        self::assertArrayHasKey('openapi', $spec);

        $paths = array_keys((array) ($spec['paths'] ?? []));
        self::assertContains('/customer/create', $paths);
        self::assertContains('/customer/{id}', $paths);
        self::assertContains('/loan/eligibility', $paths);
        self::assertContains('/loan/applications', $paths);
    }

    public function testItRendersTheSwaggerUi(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/doc');

        self::assertResponseIsSuccessful();
        self::assertStringContainsString(
            'text/html',
            (string) $client->getResponse()->headers->get('Content-Type'),
        );
    }
}
