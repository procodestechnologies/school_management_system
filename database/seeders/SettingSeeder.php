<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (array_keys(Setting::FEATURES) as $key) {
            Setting::firstOrCreate(['key' => $key], ['enabled' => true]);
        }
    }
}
