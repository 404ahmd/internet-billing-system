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
                'name' => '3Mbps',
                'description' => '-',
                'price' => 150000.00,
                'cycle' => 'monthly',
                'type' => 'pppoe',
                'bandwidth' => '3Mbps',
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '7Mbps',
                'description' => '-',
                'price' => 200000.00,
                'cycle' => 'monthly',
                'type' => 'pppoe',
                'bandwidth' => '7Mbps',
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '10Mbps',
                'description' => '-',
                'price' => 250000.00,
                'cycle' => 'monthly',
                'type' => 'pppoe',
                'bandwidth' => '10Mbps',
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '15Mbps',
                'description' => '-',
                'price' => 300000.00,
                'cycle' => 'monthly',
                'type' => 'pppoe',
                'bandwidth' => '15Mbps',
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
