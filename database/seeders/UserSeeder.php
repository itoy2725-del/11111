<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'isim' => 'Sistem Yöneticisi',
            'email' => 'admin@sistem.local',
            'password' => Hash::make('SiberCRM2024!'),
            'rol' => 'super_admin',
        ]);

        User::create([
            'isim' => 'Ali Yılmaz',
            'email' => 'ali@sistem.local',
            'password' => Hash::make('Operator2024!'),
            'rol' => 'operator',
        ]);

        User::create([
            'isim' => 'Ayşe Kaya',
            'email' => 'ayse@sistem.local',
            'password' => Hash::make('Operator2024!'),
            'rol' => 'operator',
        ]);
    }
}
