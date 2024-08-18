<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'npwp' => '099999910085000',
                'password' => Hash::make('password1'),
                'nama' => 'John Doe',
                'alamat' => '123 Main St, City A',
                'status' => 'admin',
                'active' => 'true'
            ],
            [
                'npwp' => '955805077086000',
                'password' => Hash::make('password2'),
                'nama' => 'Jane Smith',
                'alamat' => '456 Elm St, City B',
                'status' => 'user',
                'active' => 'true'
            ]
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
