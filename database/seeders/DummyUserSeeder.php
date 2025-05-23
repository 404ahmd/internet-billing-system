<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Mas Operator',
                'email' => 'operator@example.com',
                'password' => bcrypt('Password123'),
                'role' => 'operator'
            ],
            [
                'name' => 'Mas Finance',
                'email' => 'finance@example.com',
                'password' => bcrypt('Password123'),
                'role' => 'finance'
            ],
            [
                'name' => 'Mas Manager',
                'email' => 'manager@example.com',
                'password' => bcrypt('Password123'),
                'role' => 'manager'
            ],
            [
                'name' => 'Mas Admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('Password123'),
                'role' => 'administrator'
            ],
        ];

        foreach ($data as $index) {
            User::create($index);
        }
    }
}
