<?php

use Illuminate\Database\Seeder;

class PageTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('page_types')->insert(
            [
                [
                    'name' => 'Strana s composerem'
                ],
                [
                    'name' => 'Strana s view'
                ]
            ]
        );
    }
}
