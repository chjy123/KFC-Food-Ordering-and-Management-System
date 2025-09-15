<?php
#author’s name： Chow Jun Yu
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Default Admin
        User::create([
            'name'     => 'admin',
            'email'    => 'admin@gmail.com',
            'password' => Hash::make('12345678'),
            'phoneNo'  => '0123456789',
            'role'     => 'admin',
        ]);

        // Default Customer
        User::create([
            'name'     => 'customer',
            'email'    => 'customer@gmail.com',
            'password' => Hash::make('12345678'),
            'phoneNo'  => '0198765432',
            'role'     => 'customer',
        ]);
    }
}
