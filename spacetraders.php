<?php

require __DIR__ . '/vendor/autoload.php';

use App\Command;
use Symfony\Component\Console\Application;

$app = new Application('Spacetraders', '0.0.1');
$app->addCommands(commands: [
    new Command\RegisterCommand(),
    new Command\StatusCommand(),

    new Command\Agent\DetailsCommand(),

    new Command\Faction\ListCommand(),
    new Command\Faction\DetailsCommand(),

    new Command\Contract\ListCommand(),
    new Command\Contract\DetailsCommand(),
    new Command\Contract\AcceptCommand(),
    new Command\Contract\DeliverCommand(),
    new Command\Contract\FulfillCommand(),

    new Command\System\ListCommand(),
    new Command\System\DetailsCommand(),
    new Command\System\Waypoint\ListCommand(),
    new Command\System\Waypoint\DetailsCommand(),
    new Command\System\Waypoint\MarketCommand(),
    new Command\System\Waypoint\ShipyardCommand(),
    new Command\System\Waypoint\JumpgateCommand(),
    new Command\Fleet\ListCommand(),
    new Command\Fleet\DetailsCommand(),
    new Command\Fleet\NavDetailsCommand(),
    new Command\Fleet\CooldownCommand(),
    new Command\Fleet\PurchaseShipCommand(),
    new Command\Fleet\OrbitCommand(),
    new Command\Fleet\DockCommand(),
    new Command\Fleet\RefineCommand(),
    new Command\Fleet\ChartCommand(),
    new Command\Fleet\SurveyCommand(),
    new Command\Fleet\SurveyListCommand(),
    new Command\Fleet\ExtractCommand(),
    new Command\Fleet\Cargo\DetailsCommand(),
    new Command\Fleet\Cargo\JettisonCommand(),
//    new Command\Fleet\Cargo\PurchaseCommand(),
//    new Command\Fleet\Cargo\TransferCommand(),
    new Command\Fleet\JumpCommand(),
    new Command\Fleet\NavigateCommand(),
//    new Command\Fleet\PatchShipNavCommand(),
//    new Command\Fleet\WarpCommand(),
    new Command\Fleet\Cargo\SellCommand(),
//    new Command\Fleet\Scan\SystemCommand(),
//    new Command\Fleet\Scan\WaypointsCommand(),
//    new Command\Fleet\Scan\ShipsCommand(),
    new Command\Fleet\RefuelCommand(),
]);
$app->run();
