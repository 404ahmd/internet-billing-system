<?php

namespace Database\Seeders;

use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DummyCustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $faker = Faker::create('id_ID');
        $packages = [1, 2, 3, 4];
        $statuses = ['active', 'inactive', 'terminated'];

        for ($i = 1; $i <= 50; $i++) {
            $joinDate = $faker->dateTimeBetween('-1 year', 'now');
            $lastPayment = rand(0, 1) ? Carbon::parse($joinDate)->addMonths(rand(1, 6)) : null;
            $dueDate = $lastPayment ? Carbon::parse($lastPayment)->copy()->addMonth() : null;

            Customer::create([
                'name' => $faker->name,
                'username' => strtolower($faker->unique()->userName . $i),
                'package' => $packages[array_rand($packages)],
                'address' => $faker->address,
                'group' => rand(0, 1) ? 'Group ' . rand(1, 5) : null,
                'phone' => $faker->phoneNumber,
                'join_date' => $joinDate->format('Y-m-d'),
                'status' => $statuses[array_rand($statuses)],
                'last_payment_date' => $lastPayment ? $lastPayment->format('Y-m-d') : null,
                'due_date' => $dueDate ? $dueDate->format('Y-m-d') : null,
                'notes' => rand(0, 1) ? $faker->sentence : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ 50 pelanggan berhasil ditambahkan.');
    }
}
