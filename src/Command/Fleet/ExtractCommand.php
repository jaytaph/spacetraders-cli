<?php

namespace App\Command\Fleet;

use Jaytaph\Spacetraders\Api\Component\Survey;
use Jaytaph\Spacetraders\Api\Response\Fleet\ExtractResponse;
use Jaytaph\Spacetraders\Api\Command\Fleet\ExtractCommand as ApiExtractCommand;
use App\Command\BaseCommand;
use App\OutputTables;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExtractCommand extends BaseCommand
{
    protected static $defaultName = 'fleet:extract';

    protected function configure(): void
    {
        $this->setDescription('Extract minirals')
            ->setHelp('Extract minirals')
            ->setDefinition([
                new InputArgument('ship', InputArgument::REQUIRED, 'The ship symbol'),
                new InputArgument('survey', InputArgument::OPTIONAL, 'Survey signature'),
            ])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $survey = $this->getOptionalSurvey($input->getArgument('survey'));

        $api = $this->getApi();
        $command = new ApiExtractCommand(
            strval($input->getArgument('ship')),
            $survey
        );
        $response = $api->execute($command);
        $result = ExtractResponse::fromJson($response->data);

        $output->writeln("Ship is extracting ores");

        $output->writeln("Cooldown Details");
        $output->writeln("================");
        $output->writeln("Symbol          : <info>" . $result->cooldown->shipSymbol . "</info>");
        $output->writeln("Total seconds   : <info>" . $result->cooldown->totalSeconds . "</info>");
        $output->writeln("Total remaining : <info>" . $result->cooldown->remainingSeconds . "</info>");
        $output->writeln("Expiration      : <info>" . $result->cooldown->expiration->format('Y-m-d H:i:s') . "</info>");
        $output->writeln("");

        $output->writeln("Extraction Details");
        $output->writeln("==================");
        $output->writeln("Symbol : <info>" . $result->extraction->shipSymbol . "</info>");
        $output->writeln("Yield  : <info>" . $result->extraction->yieldSymbol . "</info>");
        $output->writeln("Units  : <info>" . $result->extraction->yieldUnits . "</info>");
        $output->writeln("");

        $output->writeln("Cargo Details");
        $output->writeln("=============");
        OutputTables::displayCargo($output, $result->cargo);

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

    protected function getOptionalSurvey(string $signature): ?Survey
    {
        if (!$signature) {
            return null;
        }

        $contents = @file_get_contents(".surveys.json");
        if ($contents == false) {
            $contents = '';
        }
        $json = json_decode($contents, true);
        if (! is_array($json)) {
            $json = [];
        }

        $surveys = $this->hydrateSurveys($json);
        foreach ($surveys as $entry) {
            if ($entry->signature === $signature) {
                return $entry;
            }
        }

        return null;
    }
}
