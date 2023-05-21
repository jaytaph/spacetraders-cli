<?php

namespace App\Command\Contract;

use Jaytaph\Spacetraders\Api\Response\Contract\ListResponse;
use Jaytaph\Spacetraders\Api\Command\Contract\ListCommand as ApiListCommand;
use App\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends BaseCommand
{
    protected static $defaultName = 'contract:list';

    protected function configure(): void
    {
        $this->setDescription('Display contracts')
            ->setHelp('Display contracts')
            ->setDefinition([
                new InputOption('page', 'p', InputArgument::OPTIONAL, 'Page number', 1),
                new InputOption('limit', 'l', InputArgument::OPTIONAL, 'Limit', 10),
            ])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $api = $this->getApi();
        $command = new ApiListCommand(
            intval($input->getOption('page')),
            intval($input->getOption('limit'))
        );
        $response = $api->execute($command);
        $result = ListResponse::fromJson($response->data, $response->meta);

        $table = new Table($output);
        $table->setHeaders(['ID', 'Type', 'Accepted', 'Fulfilled', 'Expires']);

        foreach ($result->contracts as $contract) {
            $table->addRow([
                $contract->id,
                $contract->type,
                $contract->accepted ? "yes" : "no",
                $contract->fulfilled ? "yes" : "no",
                $contract->expiration->format('Y-m-d H:i:s T'),
            ]);
        }

        $table->render();

        return Command::SUCCESS;
    }
}
