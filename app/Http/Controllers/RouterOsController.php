<?php

namespace App\Http\Controllers;

use App\Models\IpPool;
use App\Services\RouterOsService;
use Illuminate\Http\Request;
use RouterOS\Client;
use RouterOS\Query;

class RouterOsController extends Controller
{
    // public function test(){
    //     $host = '116.197.129.247';
    //     $username = 'admin';
    //     $password = 'kingking2000';
    //     $port = 8728;

    //     try {
    //         $client = new Client([
    //             'host' => $host,
    //             'user' => $username,
    //             'pass' => $password,
    //             'port' => $port,
    //             'timeout' => 3,
    //         ]);

    //          $query = new Query('/interface/print');
    //     $interfaces = $client->query($query)->read();

    //          dd($interfaces);

    //     } catch (\Exception $e) {
    //         echo 'gagal' . $e->getMessage();
    //     }
    // }

    public function storeIpPool(){

        try {
            IpPool::create([
                'router_id' => 6,
                'name' => 'dhcp_pool0',
                'range' => '172.30.1.2-172.30.1.254',
            ]);

            return with('sukses', 'berhasil ditambahkan');

        } catch (\Exception $e) {
            return $e->getMessage();
        }

    }

    public function test(){
        try {
            $client = new Client([
                'host' => '116.197.129.247',
                'user' => 'admin',
                'pass' => 'kingking2000',
                'port' => 8728,
            ]);

            $query = new Query('/ip/pool/print');
            $result = $client->query($query)->read();

            $pool = IpPool::all();

            return response()->json([
                'success' => true,
                'data' => [$result, $pool],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
