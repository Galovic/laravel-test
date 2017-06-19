<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = \App\Models\User::create([
            'username' => 'root',
            'firstname' => 'Root',
            'lastname' => 'SuperUser',
            'email' => 'root@email.com',
            'enabled' => true,
            'password' => bcrypt('Password1'),
        ]);

        $user->attachRole(\App\Models\Entrust\Role::where('name', 'administrator')->first());
    }
}
