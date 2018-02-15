<?php

use App\Settings;
use Illuminate\Database\Seeder;

class SettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Settings::insert([
            ['name' => 'Rental Duration', 'key' => 'rental_duration', 'value' => '90'],
            ['name' => 'Days Before Order', 'key' => 'days_before_order', 'value' => '7'],
        ]);
    }
}
