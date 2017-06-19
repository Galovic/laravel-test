<?php

use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        \App\Models\Entrust\Role::create([
            'name' => 'programmer',
            'display_name' => 'Programátor',
            'description' => 'Přístup na všechny místa.',
            'enabled' => true,
            'protected' => true,
        ]);

        \App\Models\Entrust\Role::create([
            'name' => 'administrator',
            'display_name' => 'Administrátor',
            'description' => 'Přístup na všechny přístupné místa v administraci.',
            'enabled' => true,
            'protected' => true,
        ]);

        \App\Models\Entrust\Role::create([
            'name' => 'editor',
            'display_name' => 'Redaktor/ka',
            'description' => 'Přístup pouze k článkům.',
            'enabled' => true,
        ]);

    }
}
