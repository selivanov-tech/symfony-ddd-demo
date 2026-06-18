<?php

declare(strict_types=1);

namespace App\Shared\Domain\Identity;

use Stringable;

interface UuidInterface extends Stringable
{
    public function toString(): string;

    /** Binary (16-byte) representation, used for compact storage. */
    public function getBytes(): string;

    public function equals(?object $other): bool;
}
