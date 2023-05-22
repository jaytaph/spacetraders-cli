<?php

namespace App\Command;

use Jaytaph\Spacetraders\Api\Command\StatusCommand as ApiStatusCommand;
use Jaytaph\Spacetraders\Api\Response\StatusResponse;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StatusCommand extends BaseCommand
{
    protected static $defaultName = 'status';

    protected function configure(): void
    {
        $this->setDescription('Display status')
            ->setHelp('Display status')
            ->setDefinition([]);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Game Status');

        $api = $this->getApi(useToken: false);
        $command = new ApiStatusCommand();
        $response = $api->execute($command);
        $result = StatusResponse::fromJson($response->response);

        $output->writeln("Status: <info>{$result->status}</info>");
        $output->writeln("Version: <info>{$result->version}</info>");
        $output->writeln("Last reset: <info>{$result->resetDate}</info>");
        $output->writeln("Description: <info>{$result->description}</info>");
        $output->writeln("Server resets: <info>{$result->serverResets->frequency}</info>");
        $output->writeln("               <info>{$result->serverResets->next}</info>");
        $output->writeln("");

        $table = new Table($output);
        $table->setHeaders(['Agents', 'Ships', 'Systems', 'Waypoints']);
        $table->addRow([
            $result->stats->agents,
            $result->stats->ships,
            $result->stats->systems,
            $result->stats->waypoints,
        ]);
        $table->setVertical(true);
        $table->render();
        $output->writeln("");

        $table = new Table($output);
        $table->setHeaders(['Agent', 'Credits']);
        foreach ($result->leaderboards->mostCredits as $entry) {
            $table->addRow([
                $entry->agent,
                $entry->credits,
            ]);
        }
        $table->render();
        $output->writeln("");

        $table = new Table($output);
        $table->setHeaders(['Agent', 'ChartCount']);
        foreach ($result->leaderboards->mostSubmitted as $entry) {
            $table->addRow([
                $entry->agent,
                $entry->chartCount,
            ]);
        }
        $table->render();
        $output->writeln("");

        $table = new Table($output);
        $table->setHeaders(['Title', 'Body']);
        foreach ($result->announcements as $entry) {
            $table->addRow([
                "<info>$entry->title</info>",
                wordwrap($entry->body . "\n", 80),
            ]);
        }
        $table->render();
        $output->writeln("");

        $table = new Table($output);
        $table->setHeaders(['Name', 'Url']);
        foreach ($result->links as $entry) {
            $table->addRow([
                $entry->name,
                $entry->url,
            ]);
        }
        $table->render();
        $output->writeln("");

        return Command::SUCCESS;
    }
}
