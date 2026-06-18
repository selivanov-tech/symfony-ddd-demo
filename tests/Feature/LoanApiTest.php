<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use App\Tests\Support\DatabaseTestCase;
use App\Tests\Support\LendingFixtures;

final class LoanApiTest extends DatabaseTestCase
{
    use LendingFixtures;

    public function testEligibleApplicantPassesTheCheck(): void
    {
        $customer = $this->createCustomer($this->em);
        $product = $this->createProduct($this->em);

        $this->client->request('GET', $this->eligibilityUri((string) $product->getId(), (string) $customer->getId()));

        self::assertResponseIsSuccessful();
        self::assertSame(['result' => true], $this->responseJson());
    }

    public function testIneligibleApplicantIsRejectedWithAReason(): void
    {
        $customer = $this->createCustomer($this->em, $this->customerBuilder()->withFicoScore(620));
        $product = $this->createProduct($this->em, $this->productBuilder()->withMinFICOScore(800));

        $this->client->request('GET', $this->eligibilityUri((string) $product->getId(), (string) $customer->getId()));

        self::assertResponseIsSuccessful();

        $body = $this->responseJson();
        self::assertFalse($body['result']);
        self::assertArrayHasKey('reason', $body);
    }

    private function eligibilityUri(string $productId, string $customerId): string
    {
        return sprintf('/loan/apply?productId=%s&customerId=%s&onlyCheck=1', $productId, $customerId);
    }
}
