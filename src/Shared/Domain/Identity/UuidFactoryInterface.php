<?php

declare(strict_types=1);

namespace App\Shared\Domain\Identity;

interface UuidFactoryInterface
{
    public function uuid7(): UuidInterface;

    public function fromString(string $uuid): UuidInterface;

    public function fromBytes(string $bytes): UuidInterface;
}
