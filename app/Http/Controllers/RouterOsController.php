<?php

namespace App\Http\Controllers;

use App\Services\RouterOsService;
use Illuminate\Http\Request;
use RouterOS\Client;
use RouterOS\Query;

class RouterOsController extends Controller
{
    public function test()
    {
        $host = '192.168.1.10';
        $username = 'admin';
        $password = 'pass';
        $port = 8728;

        try {
            $client = new Client([
                'host' => $host,
                'user' => $username,
                'pass' => $password,
                'port' => $port,
                'timeout' => 3,
            ]);

            // Coba kirim perintah ke router
            $response = $client->query(new Query('/system/resource/print'))->read();

            echo '✅ Berhasil terhubung ke router. Informasi sistem:<br><pre>';
            print_r($response);
            echo '</pre>';
        } catch (\Exception $e) {
            echo '❌ Gagal terhubung: ' . $e->getMessage();
        }
    }
}
