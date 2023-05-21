<?php

namespace App\Command;

use Jaytaph\Spacetraders\Api\Response\RegisterResponse;
use Jaytaph\Spacetraders\Api\Command\RegisterCommand as ApiRegisterCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RegisterCommand extends BaseCommand
{
    protected static $defaultName = 'register';

    protected function configure(): void
    {
        $this->setDescription('Register a new user')
            ->setHelp('Register a new user')
            ->setDefinition([
                new InputArgument('callsign', InputArgument::REQUIRED, 'callsign'),
                new InputArgument('faction', InputArgument::REQUIRED, 'faction'),
                new InputOption('save', 's', InputOption::VALUE_NONE, 'Save the token to the config file')
            ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Registering a new user');

        $api = $this->getApi(useToken: false);
        $command = new ApiRegisterCommand($input->getArgument('callsign'), $input->getArgument('faction'));
        $response = $api->execute($command);
        $result = RegisterResponse::fromJson($response->data);

        $output->writeln("Token: <info>{$result->token}</info>");

        if ($input->getOption('save')) {
            if (file_exists('.token')) {
                $output->writeln('<error>Token file already exists</error>');
            } else {
                file_put_contents('.token', $result->token);
                $output->writeln('Token saved to <info>.token</info>');
            }
        }

        return Command::SUCCESS;
    }
}
