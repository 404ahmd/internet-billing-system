<?php

namespace App\Services;

use Exception;
use PhpParser\Node\Stmt\TryCatch;
use RouterOS\Client;
use RouterOS\Query;

class RouterOsService
{

    protected $client;

    public function __construct()
    {
        try {
            $this->client = new Client([
                'host' => env('ROUTEROS_HOST'),
                'user' => env('ROUTEROS_USER'),
                'pass' => env('ROUTEROS_PASS'),
                'port' => 8728,
                'timeout' => 5,
            ]);
        } catch (\Exception $e) {
            throw new Exception("Koneksi ke routerOs gagal: " . $e->getMessage());
        }
    }

    public function testCOnnection()
    {
        try {
            $queryIdentity = new Query('/system/identity/print');
            $queryVersion = new Query('/system/resource/print');

            $identity = $this->client->query($queryIdentity)->read();
            $version = $this->client->query($queryVersion)->read();

            return [
                'status' => 'success',
                'identity' => $identity[0]['name'] ?? 'Tidak diketahui',
                'version' => $version[0]['version'] ?? 'Tidak diketahui',
                'uptime' => $version[0]['uptime'] ?? 'Tidak diketahui'
            ];
        } catch (\Exception $e) {
            throw new Exception("gagal mengambil informansi dari router: " . $e->getMessage());
        }
    }
}
