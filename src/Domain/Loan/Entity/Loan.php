<?php

declare(strict_types=1);

namespace App\Domain\Loan\Entity;

use App\Domain\Customer\Entity\Customer;
use App\Domain\Loan\Event\LoanApproved;
use App\Domain\Loan\Event\LoanRejected;
use App\Domain\Product\Entity\Product;
use App\Infrastructure\Persistence\Doctrine\LoanRepository;
use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\ValueObject\Money;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV7;

#[ORM\Entity(repositoryClass: LoanRepository::class)]
#[ORM\Table(name: 'loans')]
class Loan extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    private Customer $customer;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    private Product $product;

    #[ORM\Column(type: 'money')]
    private Money $amount;

    #[ORM\Column(type: 'boolean')]
    private bool $approved;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $rejectReason;

    private function __construct(Customer $customer, Product $product, Money $amount, bool $approved, ?string $rejectReason)
    {
        $this->id = (string) new UuidV7();
        $this->customer = $customer;
        $this->product = $product;
        $this->amount = $amount;
        $this->approved = $approved;
        $this->rejectReason = $rejectReason;
    }

    public static function approved(Customer $customer, Product $product, Money $amount): self
    {
        $loan = new self($customer, $product, $amount, true, null);
        $loan->recordEvent(new LoanApproved($loan->id, $customer->getId(), $amount));

        return $loan;
    }

    public static function rejected(Customer $customer, Product $product, Money $amount, string $reason): self
    {
        $loan = new self($customer, $product, $amount, false, $reason);
        $loan->recordEvent(new LoanRejected($loan->id, $customer->getId(), $amount, $reason));

        return $loan;
    }

    public function getId(): string
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

    public function getCreatedAt(): DateTimeImmutable
    {
        return (new UuidV7($this->id))->getDateTime();
    }
}
