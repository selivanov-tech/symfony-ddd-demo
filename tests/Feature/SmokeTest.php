<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use App\Tests\Support\DatabaseTestCase;

final class SmokeTest extends DatabaseTestCase
{
    public function testTestEndpointReturnsJson(): void
    {
        $this->client->request('GET', '/test');

        self::assertResponseIsSuccessful();
        self::assertSame(['message' => 'test'], $this->responseJson());
    }
}
