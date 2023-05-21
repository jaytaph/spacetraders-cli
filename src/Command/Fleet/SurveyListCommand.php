<?php

namespace App\Command\Fleet;

use Jaytaph\Spacetraders\Api\Component\Survey;
use App\Command\BaseCommand;
use App\OutputTables;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SurveyListCommand extends BaseCommand
{
    protected static $defaultName = 'fleet:survey:list';

    protected function configure(): void
    {
        $this->setDescription('List locally stored surveys')
            ->setHelp('List locally stored surveys')
            ->setDefinition([]);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $contents = @file_get_contents(".surveys.json");
        if ($contents == false) {
            $contents = '';
        }
        $json = json_decode($contents, true);
        if (! is_array($json)) {
            $json = [];
        }
        $surveys = $this->hydrateSurveys($json);

        OutputTables::displaySurveys($output, $surveys);

        return Command::SUCCESS;
    }

    /**
     * @param mixed[] $json
     * @return Survey[]
     * @throws \Exception
     */
    protected function hydrateSurveys(array $json): array
    {
        $ret = [];
        foreach ($json as $survey) {
            $entry = new Survey();
            $entry->signature = $survey['signature'];
            $entry->symbol = $survey['symbol'];
            $entry->size = $survey['size'];
            $entry->deposits = $survey['deposits'];
            $entry->expiration = new \DateTime($survey['expiration']['date'], new \DateTimeZone($survey['expiration']['timezone']));
            $ret[] = $entry;
        }

        return $ret;
    }
}
