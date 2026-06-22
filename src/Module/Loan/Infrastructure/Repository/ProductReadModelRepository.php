<?php

declare(strict_types=1);

namespace App\Module\Loan\Infrastructure\Repository;

use App\Module\Loan\Application\ReadModel\ProductOffer;
use App\Module\Loan\Application\Repository\ProductReadModelRepositoryInterface;
use App\Module\Loan\Domain\ValueObject\ProductTerms;
use App\Module\Product\Domain\Repository\ProductRepositoryInterface;

final class ProductReadModelRepository implements ProductReadModelRepositoryInterface
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
    ) {
    }

    public function findById(string $id): ?ProductOffer
    {
        $product = $this->products->findById($id);
        if ($product === null) {
            return null;
        }

        /** @var string[] $availableStates */
        $availableStates = $product->getAvailableStates();

        return new ProductOffer(
            $product->getId(),
            $product->getAmount(),
            new ProductTerms(
                $product->getMinFICOScore(),
                $product->getMinMonthlyIncome(),
                $product->getMinAge(),
                $product->getMaxAge(),
                $availableStates,
            ),
        );
    }
}
