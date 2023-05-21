<?php

namespace Jaytaph\Spacetraders;

use Jaytaph\Spacetraders\Api\Component\Cargo;
use Jaytaph\Spacetraders\Api\Component\Fuel;
use Jaytaph\Spacetraders\Api\Component\Nav;
use Jaytaph\Spacetraders\Api\Component\Survey;
use Jaytaph\Spacetraders\Api\Component\Waypoint;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

class OutputTables
{
    protected OutputInterface $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @param OutputInterface $output
     * @param Waypoint[] $waypoints
     * @return void
     */
    public static function displayWaypoints(OutputInterface $output, array $waypoints): void
    {
        $table = new Table($output);
        $table->setHeaders([
            'Symbol',
            'Type',
            'Coord',
            'Num. orbitals',
            'Faction',
            'Traits',
            'Chart'
        ]);

        foreach ($waypoints as $waypoint) {
            $traits = [];
            foreach ($waypoint->traits as $trait) {
                $traits[] = $trait->name;
            }

            $table->addRow([
                $waypoint->symbol,
                $waypoint->type,
                "{$waypoint->x},{$waypoint->y}",
                count($waypoint->orbitals),
                $waypoint->faction,
                join(", ", $traits),
                $waypoint->chart?->waypointSymbol,
            ]);
        }

        $table->render();
        $output->writeln("");
    }

    /**
     * @param OutputInterface $output
     * @param Survey[] $surveys
     * @return void
     */
    public static function displaySurveys(OutputInterface $output, array $surveys): void
    {
        $table = new Table($output);
        $table->setHeaders([
            'Signature',
            'Symbol',
            'Deposits',
            'Expiration',
            'Size',
        ]);

        foreach ($surveys as $survey) {
            $table->addRow([
                $survey->signature,
                $survey->symbol,
                join(", ", $survey->deposits ?? []),
                $survey->expiration->format('Y-m-d H:i:s'),
                $survey->size,
            ]);
        }

        $table->render();
        $output->writeln("");
    }

    public static function displayNavigation(OutputInterface $output, Nav $nav): void
    {
        $output->writeln("System     : <info>" . $nav->system . "</info>");
        $output->writeln("Waypoint   : <info>" . $nav->waypoint . "</info>");
        $output->writeln("Flightmode : <info>" . $nav->flightmode . "</info>");
        $output->writeln("Status     : <info>" . $nav->status . "</info>");
        $output->writeln("Route      :");
        $output->writeln("  Destination :");
        $output->writeln("    Symbol : <info>" . $nav->route->destination->symbol . "</info>");
        $output->writeln("    Type   : <info>" . $nav->route->destination->type . "</info>");
        $output->writeln("    System : <info>" . $nav->route->destination->system . "</info>");
        $output->writeln("    Coord  : <info>" . $nav->route->destination->x . "," . $nav->route->destination->y . "</info>");
        $output->writeln("  Depature :");
        $output->writeln("    Symbol : <info>" . $nav->route->departure->symbol . "</info>");
        $output->writeln("    Type   : <info>" . $nav->route->departure->type . "</info>");
        $output->writeln("    System : <info>" . $nav->route->departure->system . "</info>");
        $output->writeln("    Coord  : <info>" . $nav->route->departure->x . "," . $nav->route->destination->y . "</info>");
        $output->writeln("  Depature time : <info>" . $nav->route->departureTime?->format('d-M-Y h:i:s') . "</info>");
        $output->writeln("  Arrival time  : <info>" . $nav->route->arrival?->format('d-M-Y h:i:s') . "</info>");
        $output->writeln("");
    }

    public static function displayFuel(OutputInterface $output, Fuel $fuel): void
    {
        $output->writeln("Fuel :");
        $output->writeln("  Current : <info>" . $fuel->current . "</info>");
        $output->writeln("  Capacity : <info>" . $fuel->capacity . "</info>");
        $output->writeln("  Consumed : <info>" . $fuel->consumedAmount . "</info>");
        $output->writeln("  Timestamp : <info>" . $fuel->consumedTimestamp->format('d-M-Y h:i:s') . "</info>");
        $output->writeln("");
    }

    public static function displayCargo(OutputInterface $output, Cargo $cargo): void
    {
        $output->writeln("Cargo :");
        $output->writeln("  Capacity : <info>" . $cargo->capacity . "</info>");
        $output->writeln("  Units    : <info>" . $cargo->units . "</info>");

        $table = new Table($output);
        $table->setHeaders([
            'Symbol',
            'Name',
            'Description',
            'Units',
        ]);

        foreach ($cargo->inventory as $inventory) {
            $table->addRow([
                $inventory->symbol,
                $inventory->name,
                wordwrap($inventory->description),
                $inventory->units,
            ]);
        }

        $table->render();
        $output->writeln("");
    }
}
