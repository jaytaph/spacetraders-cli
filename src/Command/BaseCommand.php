<?php

namespace App\Command;

use Jaytaph\Spacetraders\Api\Api;
use Symfony\Component\Console\Command\Command;

class BaseCommand extends Command
{
    // Retrieves an instance of the API, either with or without a token from the .token file.
    public function getApi(bool $useToken = true): Api
    {
        $api = new Api();

        if ($useToken) {
            $token = file_get_contents('.token');
            if ($token) {
                $api->setToken(trim($token));
            }
        }

        return $api;
    }
}
