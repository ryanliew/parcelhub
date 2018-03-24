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
            ['name' => 'Rental Duration', 'setting_key' => 'rental_duration', 'setting_value' => '1'],
            ['name' => 'Days Before Order', 'setting_key' => 'days_before_order', 'setting_value' => '7'],
        ]);
    }
}
