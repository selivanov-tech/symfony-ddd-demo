<?php

declare(strict_types=1);

namespace App\Tests\Support;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Base class for functional tests that hit the HTTP layer and need a real database.
 *
 * Each test gets a fresh SQLite schema (drop + create) so tests stay isolated and
 * order-independent, without depending on migrations or fixtures.
 */
abstract class DatabaseTestCase extends WebTestCase
{
    protected KernelBrowser $client;
    protected EntityManagerInterface $em;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->em = static::getContainer()->get(EntityManagerInterface::class);

        $this->resetSchema();
    }

    private function resetSchema(): void
    {
        $schemaTool = new SchemaTool($this->em);
        $metadata = $this->em->getMetadataFactory()->getAllMetadata();

        try {
            $schemaTool->dropSchema($metadata);
        } catch (\Throwable) {
            // First run against a fresh database file: nothing to drop.
        }

        $schemaTool->createSchema($metadata);
    }

    /**
     * @param array<string, mixed> $payload
     */
    protected function jsonRequest(string $method, string $uri, array $payload = []): void
    {
        $this->client->request(
            method: $method,
            uri: $uri,
            server: ['CONTENT_TYPE' => 'application/json'],
            content: $payload === [] ? '' : (string) json_encode($payload),
        );
    }

    /**
     * @return array<string, mixed>
     */
    protected function responseJson(): array
    {
        return (array) json_decode((string) $this->client->getResponse()->getContent(), true);
    }
}
