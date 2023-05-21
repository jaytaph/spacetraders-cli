<?php

namespace App\Command\Contract;

use Jaytaph\Spacetraders\Api\Command\Contract\FulfillCommand as ApiFulfillCommand;
use Jaytaph\Spacetraders\Api\Response\Contract\FulfillResponse;
use App\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FulfillCommand extends BaseCommand
{
    protected static $defaultName = 'contract:fulfill';

    protected function configure(): void
    {
        $this->setDescription('Fulfill a contract')
            ->setHelp('Fulfill a contract')
            ->setDefinition([
                new InputArgument('contract', InputArgument::REQUIRED, 'The contract ID'),
            ])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $api = $this->getApi();
        $command = new ApiFulfillCommand(strval($input->getArgument('contract')));
        $response = $api->execute($command);
        $result = FulfillResponse::fromJson($response->data);

        $output->writeln("Fulfilled contract <info>{$result->contract->id}</info>");

        return Command::SUCCESS;
    }
}
