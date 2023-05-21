<?php

namespace App\Command\Contract;

use Jaytaph\Spacetraders\Api\Command\Contract\DeliverCommand as ApiDeliverCommand;
use Jaytaph\Spacetraders\Api\Response\Contract\DeliverResponse;
use App\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeliverCommand extends BaseCommand
{
    protected static $defaultName = 'contract:deliver';

    protected function configure(): void
    {
        $this->setDescription('Deliver a contract')
            ->setHelp('Deliver a contract')
            ->setDefinition([
                new InputArgument('contract', InputArgument::REQUIRED, 'The contract ID'),
                new InputArgument('ship', InputArgument::REQUIRED, 'Ship symbol'),
                new InputArgument('trade', InputArgument::REQUIRED, 'Trade symbol'),
                new InputArgument('units', InputArgument::REQUIRED, 'Units'),
            ])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $api = $this->getApi();
        $command = new ApiDeliverCommand(
            strval($input->getArgument('contract')),
            strval($input->getArgument('ship')),
            strval($input->getArgument('trade')),
            intval($input->getArgument('units'))
        );
        $response = $api->execute($command);
        $result = DeliverResponse::fromJson($response->data);

        $output->writeln("Delivered cargo on contract <info>{$result->contract->id}</info>");

        return Command::SUCCESS;
    }
}
