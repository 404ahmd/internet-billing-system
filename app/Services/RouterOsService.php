<?php

namespace App\Services;

use Exception;
use PhpParser\Node\Stmt\TryCatch;
use RouterOS\Client;
use RouterOS\Query;

class RouterOsService
{

    public static function connect($host, $username, $password, $port = 8728) {
        try {
            $client = new Client([
                'host' => $host,
                'username' => $username,
                'password' => $password,
                'port' => $port,
                'timeout' => 3
            ]);
            $client->connect();
            return $client;
        } catch (\Exception $e) {
            return null;
        }
    }
}
