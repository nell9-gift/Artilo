<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'ornelladzah720@gmail.com')],
            [
                'name' => env('ADMIN_NAME', 'DZAH Akou Ornella'),
                'password' => Hash::make(env('ADMIN_PASSWORD', '@recluM2005')),
                'telephone' => env('ADMIN_PHONE', '92893797'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
    }
}
