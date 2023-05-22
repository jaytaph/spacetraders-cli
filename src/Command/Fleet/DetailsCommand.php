<?php

namespace App\Command\Fleet;

use Jaytaph\Spacetraders\Api\Command\Fleet\DetailsCommand;
use Jaytaph\Spacetraders\Api\Component\Crew;
use Jaytaph\Spacetraders\Api\Component\Frame;
use Jaytaph\Spacetraders\Api\Component\Reactor;
use Jaytaph\Spacetraders\Api\Component\Ship;
use Jaytaph\Spacetraders\Api\Response\Fleet\DetailsResponse;
use App\Command\BaseCommand;
use App\OutputTables;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DetailsCommand extends BaseCommand
{
    protected static $defaultName = 'fleet:details';

    protected function configure(): void
    {
        $this->setDescription('Display ship details')
            ->setHelp('Display ship details')
            ->setDefinition([
                new InputArgument('ship', InputArgument::REQUIRED, 'The ship symbol'),
                new InputOption('registration', '', InputOption::VALUE_NONE, 'Display registration details'),
                new InputOption('navigation', '', InputOption::VALUE_NONE, 'Display navigation details'),
                new InputOption('crew', '', InputOption::VALUE_NONE, 'Display crew details'),
                new InputOption('frame', '', InputOption::VALUE_NONE, 'Display frame details'),
                new InputOption('reactor', '', InputOption::VALUE_NONE, 'Display reactor details'),
                new InputOption('modules', '', InputOption::VALUE_NONE, 'Display module details'),
                new InputOption('mounts', '', InputOption::VALUE_NONE, 'Display mount details'),
                new InputOption('cargo', '', InputOption::VALUE_NONE, 'Display cargo details'),
                new InputOption('fuel', '', InputOption::VALUE_NONE, 'Display fuel details'),
            ])
        ;
    }

    protected function check(InputInterface $input, string $option): bool
    {
        if ($input->getOption($option) === true) {
            return true;
        }

        $options = ['registration', 'navigation', 'crew', 'frame', 'reactor', 'modules', 'mounts', 'cargo', 'fuel'];
        $enabled = false;
        foreach ($options as $option) {
            if ($input->getOption($option)) {
                $enabled = true;
                break;
            }
        }

        return !$enabled;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $api = $this->getApi();
        $command = new DetailsCommand(strval($input->getArgument('ship')));
        $response = $api->execute($command);
        $result = DetailsResponse::fromJson($response->data);

        $output->writeln("Ship Details <info>" . $result->ship->symbol . "</info>");

        if ($this->check($input, 'registration')) {
            $this->displayRegistration($output, $result->ship);
        }
        if ($this->check($input, 'navigation')) {
            OutputTables::displayNavigation($output, $result->ship->nav);
        }
        if ($this->check($input, 'crew')) {
            $this->displayCrew($output, $result->ship->crew);
        }
        if ($this->check($input, 'frame')) {
            $this->displayFrame($output, $result->ship->frame);
        }
        if ($this->check($input, 'reactor')) {
            $this->displayReactor($output, $result->ship->reactor);
        }
        if ($this->check($input, 'modules')) {
            $this->displayModules($output, $result->ship->modules);
        }
        if ($this->check($input, 'mounts')) {
            $this->displayMounts($output, $result->ship->mounts);
        }
        if ($this->check($input, 'cargo')) {
            OutputTables::displayCargo($output, $result->ship->cargo);
        }
        if ($this->check($input, 'fuel')) {
            OutputTables::displayFuel($output, $result->ship->fuel);
        }

        return Command::SUCCESS;
    }

    protected function displayCrew(OutputInterface $output, Crew $crew): void
    {
        $output->writeln("Crew :");
        $output->writeln("  Current  : <info>" . $crew->current . "</info>");
        $output->writeln("  Required : <info>" . $crew->required . "</info>");
        $output->writeln("  Capacity : <info>" . $crew->capacity . "</info>");
        $output->writeln("  Rotation : <info>" . $crew->rotation . "</info>");
        $output->writeln("  Morale   : <info>" . $crew->morale . "</info>");
        $output->writeln("  Wages    : <info>" . $crew->wages . "</info>");
        $output->writeln("");
    }

    protected function displayFrame(OutputInterface $output, Frame $frame): void
    {
        $output->writeln("Frame :");
        $output->writeln("  Symbol          : <info>" . $frame->symbol . "</info>");
        $output->writeln("  Name            : <info>" . $frame->name . "</info>");
        $output->writeln("  Description     : <info>" . $frame->description . "</info>");
        $output->writeln("  Condition       : <info>" . $frame->condition . "</info>");
        $output->writeln("  Module Slots    : <info>" . $frame->moduleSlots . "</info>");
        $output->writeln("  Mounting Points : <info>" . $frame->mountingPoints . "</info>");
        $output->writeln("  Fuel Capacity   : <info>" . $frame->fuelCapacity . "</info>");
        $output->writeln("  Req. Power      : <info>" . $frame->requirementsPower . "</info>");
        $output->writeln("  Req. Crew       : <info>" . $frame->requirementsCrew . "</info>");
        $output->writeln("");
    }

    protected function displayReactor(OutputInterface $output, Reactor $reactor): void
    {
        $output->writeln("Reactor :");
        $output->writeln("  Symbol       : <info>" . $reactor->symbol . "</info>");
        $output->writeln("  Name         : <info>" . $reactor->name . "</info>");
        $output->writeln("  Description  : <info>" . $reactor->description . "</info>");
        $output->writeln("  Condition    : <info>" . $reactor->condition . "</info>");
        $output->writeln("  PowerOutput  : <info>" . $reactor->powerOutput . "</info>");
        $output->writeln("  Req. Power   : <info>" . $reactor->requirementsPower . "</info>");
        $output->writeln("  Req. Crew    : <info>" . $reactor->requirementsCrew . "</info>");
        $output->writeln("  Req. Slots   : <info>" . $reactor->requirementsSlots . "</info>");
        $output->writeln("");
    }

    protected function displayModules(OutputInterface $output, array $modules): void
    {
        $output->writeln("Modules :");

        $table = new Table($output);
        $table->setHeaders([
            'Symbol',
            'Capacity',
            'Range',
            'Name',
            'Description',
            'Req. Power',
            'Req. Crew',
            'Req. Slots',
        ]);

        foreach ($modules as $module) {
            $table->addRow([
                $module->symbol,
                $module->capacity,
                $module->range,
                $module->name,
                $module->description,
                $module->requirementsPower,
                $module->requirementsCrew,
                $module->requirementsSlots,
            ]);
        }
        $table->setVertical();
        $table->render();

        $output->writeln("");
    }

    protected function displayMounts(OutputInterface $output, array $mounts): void
    {
        $output->writeln("Mounts :");

        $table = new Table($output);
        $table->setHeaders([
            'Symbol',
            'Name',
            'Description',
            'Strength',
            'Deposits',
            'Req. Power',
            'Req. Crew',
            'Req. Slots',
        ]);

        foreach ($mounts as $mount) {
            $table->addRow([
                $mount->symbol,
                $mount->name,
                wordwrap($mount->description),
                $mount->strength,
                join(", ", $mount->deposits),
                $mount->requirementsPower,
                $mount->requirementsCrew,
                $mount->requirementsSlots,
            ]);
        }

        $table->setVertical();
        $table->render();

        $output->writeln("");
    }

    protected function displayRegistration(OutputInterface $output, Ship $ship): void
    {
        $output->writeln("Registration :");
        $output->writeln("  Name       : <info>" . $ship->registrationName . "</info>");
        $output->writeln("  Faction    : <info>" . $ship->registrationFaction . "</info>");
        $output->writeln("  Role       : <info>" . $ship->registrationRole . "</info>");
        $output->writeln("");
    }
}
