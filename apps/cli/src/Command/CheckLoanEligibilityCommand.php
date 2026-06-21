<?php

declare(strict_types=1);

namespace Cli\Command;

use App\Module\Loan\Application\Query\CheckEligibility\CheckLoanEligibilityQuery;
use App\Module\Loan\Application\ReadModel\EligibilityView;
use App\Shared\Application\Bus\Query\QueryBusInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:loan:check-eligibility',
    description: 'Check whether a customer is eligible for a product loan.',
)]
final class CheckLoanEligibilityCommand extends Command
{
    public function __construct(private readonly QueryBusInterface $queryBus)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('productId', InputArgument::REQUIRED, 'Product id')
            ->addArgument('customerId', InputArgument::REQUIRED, 'Customer id');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var EligibilityView $view */
        $view = $this->queryBus->ask(new CheckLoanEligibilityQuery(
            (string) $input->getArgument('productId'),
            (string) $input->getArgument('customerId'),
        ));

        if ($view->eligible) {
            $io->success('Customer is eligible for this product.');

            return Command::SUCCESS;
        }

        $io->warning('Not eligible: ' . ($view->reason ?? 'no reason given'));

        return Command::FAILURE;
    }
}
