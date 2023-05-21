<?php

namespace App\Command\Contract;

use Jaytaph\Spacetraders\Api\Command\Contract\DetailCommand;
use Jaytaph\Spacetraders\Api\Response\Contract\DetailResponse;
use App\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DetailsCommand extends BaseCommand
{
    protected static $defaultName = 'contract:details';

    protected function configure(): void
    {
        $this->setDescription('Display contract details')
            ->setHelp('Display contract details')
            ->setDefinition([
                'contract' => new InputArgument('contract', InputArgument::REQUIRED, 'The contract id'),
            ])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $api = $this->getApi();
        $command = new DetailCommand(strval($input->getArgument('contract')));
        $response = $api->execute($command);
        $result = DetailResponse::fromJson($response->data);

        $output->writeln("Contract Details");
        $output->writeln("==============");
        $output->writeln("Id        : <info>" . $result->contract->id . "</info>");
        $output->writeln("Symbol    : <info>" . $result->contract->factionSymbol . "</info>");
        $output->writeln("Type      : <info>" . $result->contract->type . "</info>");
        $output->writeln("Accepted  : <info>" . ($result->contract->accepted ? "Yes" : "No") . "</info>");
        $output->writeln("Fulfilled : <info>" . ($result->contract->fulfilled  ? "Yes" : "No") . "</info>");
        $output->writeln("Expration : <info>" . $result->contract->expiration->format('Y-m-d H:i:s T') . "</info>");

        $output->writeln("Deadline          : <info>" . $result->contract->terms->deadline->format('Y-m-d H:i:s T') . "</info>");
        $output->writeln("Payment accepted  : <info>" . $result->contract->terms->paymentOnAccepted . "</info>");
        $output->writeln("Payment fulfilled : <info>" . $result->contract->terms->paymentOnFulfilled . "</info>");

        foreach ($result->contract->terms->deliveries as $delivery) {
            $output->writeln("* Delivery");
            $output->writeln("    Trade           : <info>" . $delivery->tradeSymbol . "</info>");
            $output->writeln("    Destination     : <info>" . $delivery->destination . "</info>");
            $output->writeln("    Units required  : <info>" . $delivery->unitsRequired . "</info>");
            $output->writeln("    Units fulfilled : <info>" . $delivery->unitsFulfilled . "</info>");
        }

        return Command::SUCCESS;
    }
}
