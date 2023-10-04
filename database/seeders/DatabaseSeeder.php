<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
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

        \App\Models\User::create([
            'full_name' => 'Eko suprianto',
            'uuid' => Str::uuid(32),
            'username' => 'ekosaputra',
            'no_hape' => '08123456789',
            'alamat' => 'Jakarta Utara, Kali Baru',
            'tgl_lahir' => '14-09-1996',
            'password' => Hash::make(12345678),
            'email' => 'ekhosaputra23@gmail.com',
        ]);
    }
}
