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
            ['setting_key' => 'rental_duration', 'setting_value' => '1'],
            ['setting_key' => 'days_before_order', 'setting_value' => '7'],
        ]);
    }
}
