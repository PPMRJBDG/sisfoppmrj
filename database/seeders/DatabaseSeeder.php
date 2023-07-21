<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $superadmin = User::firstOrCreate(
            ['email' => env('SUPERADMIN_EMAIL')],
            [
                'fullname' => env('SUPERADMIN_FULLNAME'),
                'email' => env('SUPERADMIN_EMAIL'),
                'password' => Hash::make(env('SUPERADMIN_PASSWORD', 123)),
                'gender' => 'male',
                'birthdate' => '2001-01-30'          
            ]
        );

        if($superadmin)        
            $superadmin->assignRole('superadmin');       
    }
}
