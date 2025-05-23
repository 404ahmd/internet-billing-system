<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OperatorController extends Controller
{
    public function dashboard(){

        $startDate = Carbon::now()->subMonths(3);
        $endDate = Carbon::now()->endOfMonth();

        $data = collect();

        for($date = $startDate->copy(); $date->lte($endDate); $date->addDay()){
            $count = Customer::where('status', 'active')
            ->whereDate('join_date', $date->toDateString())
            ->count();

            $data->push([
                'date' => $date->format('Y-m-d'),
                'active_count' => $count,
            ]);
        }


        return view('operator.dashboard',[
            'activeCustomers' => $data,
        ]);
    }

}
