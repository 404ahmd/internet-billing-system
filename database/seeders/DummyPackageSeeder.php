<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DummyPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
    {
        $pacakges = [
            [
                'name' => '10Mbps',
                'description' => '-',
                'price' => 150000.00,
                'cycle' => 'monthly',
                'type' => 'pppoe',
                'bandwidth' => '10Mbps',
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '20Mbps',
                'description' => '-',
                'price' => 250000.00,
                'cycle' => 'monthly',
                'type' => 'pppoe',
                'bandwidth' => '20Mbps',
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '25Mbps',
                'description' => '-',
                'price' => 300000.00,
                'cycle' => 'monthly',
                'type' => 'pppoe',
                'bandwidth' => '25Mbps',
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '30Mbps',
                'description' => '-',
                'price' => 350000.00,
                'cycle' => 'monthly',
                'type' => 'pppoe',
                'bandwidth' => '30Mbps',
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($pacakges as $index ) {
            Package::create($index);
        }
    }
}
