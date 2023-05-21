<?php

namespace App\Command\Fleet;

use Jaytaph\Spacetraders\Api\Response\Fleet\CooldownResponse;
use Jaytaph\Spacetraders\Api\Command\Fleet\CooldownCommand as ApiCooldownCommand;
use App\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CooldownCommand extends BaseCommand
{
    protected static $defaultName = 'fleet:cooldown';

    protected function configure(): void
    {
        $this->setDescription('Get ship cooldown status')
            ->setHelp('Get ship cooldown status')
            ->setDefinition([
                new InputArgument('ship', InputArgument::REQUIRED, 'The ship symbol'),
            ])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $api = $this->getApi();
        $command = new ApiCooldownCommand(strval($input->getArgument('ship')));
        $response = $api->execute($command);
        if ($response->statusCode == 204) {
            $output->writeln("Ship is currently not in cooldown");
            return Command::SUCCESS;
        }

        $result = CooldownResponse::fromJson($response->data);

        $output->writeln("Ship Cooldown Details");
        $output->writeln("=====================");
        $output->writeln("Symbol          : <info>" . $result->cooldown->shipSymbol . "</info>");
        $output->writeln("Total seconds   : <info>" . $result->cooldown->totalSeconds . "</info>");
        $output->writeln("Total remaining : <info>" . $result->cooldown->remainingSeconds . "</info>");
        $output->writeln("Expiration      : <info>" . $result->cooldown->expiration->format('Y-m-d H:i:s') . "</info>");

        return Command::SUCCESS;
    }
}
