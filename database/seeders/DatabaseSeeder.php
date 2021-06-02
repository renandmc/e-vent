<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Event;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'eventfatec@gmail.com',
            'password' => Hash::make('senhaadmin123456'),
            'group' => 'Organizador'
        ]);
        Event::factory()->count(10)->create([
            'user_id' => 1
        ]);
    }
}
