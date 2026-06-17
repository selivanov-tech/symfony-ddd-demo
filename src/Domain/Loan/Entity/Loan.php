<?php

declare(strict_types=1);

namespace App\Domain\Loan\Entity;

use App\Domain\Customer\Entity\Customer;
use App\Domain\Loan\Event\LoanApproved;
use App\Domain\Loan\Event\LoanRejected;
use App\Domain\Product\Entity\Product;
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

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    private Customer $customer;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    private Product $product;

    #[ORM\Column(type: Money::class)]
    private Money $amount;

    #[ORM\Column(type: 'boolean')]
    private bool $approved;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $rejectReason;

    private function __construct(UuidInterface $id, Customer $customer, Product $product, Money $amount, bool $approved, ?string $rejectReason)
    {
        $this->id = $id;
        $this->customer = $customer;
        $this->product = $product;
        $this->amount = $amount;
        $this->approved = $approved;
        $this->rejectReason = $rejectReason;
    }

    public static function approved(UuidFactoryInterface $uuidFactory, Customer $customer, Product $product, Money $amount): self
    {
        $id = $uuidFactory->uuid7();
        $loan = new self($id, $customer, $product, $amount, true, null);
        $loan->recordEvent(new LoanApproved($id->toString(), $customer->getId()->toString(), $amount));

        return $loan;
    }

    public static function rejected(UuidFactoryInterface $uuidFactory, Customer $customer, Product $product, Money $amount, string $reason): self
    {
        $id = $uuidFactory->uuid7();
        $loan = new self($id, $customer, $product, $amount, false, $reason);
        $loan->recordEvent(new LoanRejected($id->toString(), $customer->getId()->toString(), $amount, $reason));

        return $loan;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function getProduct(): Product
    {
        return $this->product;
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
