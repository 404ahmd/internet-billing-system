<?php

namespace App\Http\Controllers;

use App\Services\RouterOsService;
use Illuminate\Http\Request;

class RouterOsController extends Controller
{
    public function test(RouterOsService $service){
        $result =$service->testCOnnection();
        return response()->json($result);
    }
}
