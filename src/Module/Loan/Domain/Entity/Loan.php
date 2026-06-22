<?php

declare(strict_types=1);

namespace App\Module\Loan\Domain\Entity;

use App\Module\Loan\Domain\Event\LoanApproved;
use App\Module\Loan\Domain\Event\LoanRejected;
use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Identity\UuidFactoryInterface;
use App\Shared\Domain\Identity\UuidInterface;
use App\Shared\Domain\ValueObject\Money;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'loans')]
class Loan extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: UuidInterface::class, unique: true)]
    private UuidInterface $id;

    #[ORM\Column(type: UuidInterface::class)]
    private UuidInterface $customerId;

    #[ORM\Column(type: UuidInterface::class)]
    private UuidInterface $productId;

    #[ORM\Column(type: Money::class)]
    private Money $amount;

    #[ORM\Column(type: 'boolean')]
    private bool $approved;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $rejectReason;

    private function __construct(UuidInterface $id, UuidInterface $customerId, UuidInterface $productId, Money $amount, bool $approved, ?string $rejectReason)
    {
        $this->id = $id;
        $this->customerId = $customerId;
        $this->productId = $productId;
        $this->amount = $amount;
        $this->approved = $approved;
        $this->rejectReason = $rejectReason;
    }

    public static function approved(UuidFactoryInterface $uuidFactory, UuidInterface $customerId, UuidInterface $productId, Money $amount): self
    {
        $id = $uuidFactory->uuid7();
        $loan = new self($id, $customerId, $productId, $amount, true, null);
        $loan->recordEvent(new LoanApproved($id->toString(), $customerId->toString(), $amount));

        return $loan;
    }

    public static function rejected(UuidFactoryInterface $uuidFactory, UuidInterface $customerId, UuidInterface $productId, Money $amount, string $reason): self
    {
        $id = $uuidFactory->uuid7();
        $loan = new self($id, $customerId, $productId, $amount, false, $reason);
        $loan->recordEvent(new LoanRejected($id->toString(), $customerId->toString(), $amount, $reason));

        return $loan;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getCustomerId(): UuidInterface
    {
        return $this->customerId;
    }

    public function getProductId(): UuidInterface
    {
        return $this->productId;
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }

    public function isApproved(): bool
    {
        return $this->approved;
    }

    public function getRejectReason(): ?string
    {
        return $this->rejectReason;
    }
}
