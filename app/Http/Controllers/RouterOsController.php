<?php

namespace App\Http\Controllers;

use App\Services\RouterOsService;
use Illuminate\Http\Request;
use RouterOS\Client;
use RouterOS\Query;

class RouterOsController extends Controller
{
    // $host = '192.168.1.10';
        // $username = 'admin';
        // $password = 'pass';
        // $port = 8728;

        // try {
        //     $client = new Client([
        //         'host' => $host,
        //         'user' => $username,
        //         'pass' => $password,
        //         'port' => $port,
        //         'timeout' => 3,
        //     ]);

        //     // Coba kirim perintah ke router
        //     $response = $client->query(new Query('/system/resource/print'))->read();

        //     echo '✅ Berhasil terhubung ke router. Informasi sistem:<br><pre>';
        //     print_r($response);
        //     echo '</pre>';
        // } catch (\Exception $e) {
        //     echo '❌ Gagal terhubung: ' . $e->getMessage();
        // }

    // public function test()
    // {
        

    //     $name = 'ip_pool_test';
    //     $range = '192.168.1.10-192.168.1.254';

    //     $host = '192.168.1.10';
    //     $username = 'admin';
    //     $password = 'pass';
    //     $port = 8728;

    //     try {
    //         $client = new Client([
    //             'host' => $host,
    //             'user' => $username,
    //             'pass' => $password,
    //             'port' => $port,
    //             'timeout' => 3,
    //         ]);

    //         $response = $client->query((new Query('/ip/pool/add'))
    //                 ->equal('name', $name)
    //                 ->equal('ranges', $range)
    //         )->read();

    //         echo '✅ Berhasil terhubung ke router. Informasi sistem:<br><pre>';
    //         print_r($response);

    //     } catch (\Exception $e) {
    //         echo '❌ Gagal terhubung: ' . $e->getMessage();
    //     }
    // }

    public function test(){
        $host = '116.197.129.247';
        $username = 'admin';
        $password = 'kingking2000';
        $port = 8728;

        try {
            $client = new Client([
                'host' => $host,
                'user' => $username,
                'pass' => $password,
                'port' => $port,
                'timeout' => 3,
            ]);

             $query = new Query('/interface/print');
        $interfaces = $client->query($query)->read();
        
             dd($interfaces);
            
        } catch (\Exception $e) {
            echo 'gagal' . $e->getMessage();
        }
    }
}
