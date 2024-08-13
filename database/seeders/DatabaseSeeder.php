<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Dokter;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory(1)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
class UserTableSeeder extends Seeder
{

    public function run()
    {
        User::create(
            array(
                'name' => 'dokter',
                'email' => 'dokter@gmail.com',
                'password' => Hash::make('12345678'),
                'status_role' => 1,
                'no_telpon' => '0980866587'
            )
        );
        Dokter::create(
            array(
                'id_user' => '',
                'spesalis' => 'gizi',
                'pengalaman' => 'gizi',
                'jenis_dokter' => 'Psikolog'
            )
            );
    }
}
