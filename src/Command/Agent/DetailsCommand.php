<?php

namespace App\Command\Agent;

use Jaytaph\Spacetraders\Api\Command\Agent\DetailsCommand as ApiDetailsCommand;
use Jaytaph\Spacetraders\Api\Response\Agent\DetailsResponse;
use App\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DetailsCommand extends BaseCommand
{
    protected static $defaultName = 'agent:details';

    protected function configure(): void
    {
        $this->setDescription('Display info about the current agent')
            ->setHelp('Display info about the current agent')
            ->setDefinition([])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $api = $this->getApi();
        $command = new ApiDetailsCommand();
        $response = $api->execute($command);
        $result = DetailsResponse::fromJson($response->data);

        $output->writeln("Your acount <info>{$result->accountId}</info> has symbol <info>{$result->symbol}</info>.");
        $output->writeln("You have <info>{$result->credits}</info> credits remaining, and your HQ is <info>{$result->headquarters}</info>.");

        return Command::SUCCESS;
    }
}
