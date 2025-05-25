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
            'user' => $username,      // Harus 'user' bukan 'username'
            'pass' => $password,      // Harus 'pass' bukan 'password'
            'port' => $port,
            'timeout' => 3
        ]);
        
        // Test koneksi dengan query ringan
        $client->query(new \RouterOS\Query('/system/resource/print'))->read();

        return $client;
    } catch (\Exception $e) {
        return null;
    }

    }


}
