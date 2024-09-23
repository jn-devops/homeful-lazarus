<?php

namespace Database\Seeders;

use App\Models\NameSuffix;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SuffixSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        NameSuffix::updateOrCreate(['code' => '001'], ['description' => 'N/A']);
        NameSuffix::updateOrCreate(['code' => '002'], ['description' => 'Jr.']);
        NameSuffix::updateOrCreate(['code' => '003'], ['description' => 'Sr.']);
        NameSuffix::updateOrCreate(['code' => '004'], ['description' => 'II']);
        NameSuffix::updateOrCreate(['code' => '005'], ['description' => 'III']);
        NameSuffix::updateOrCreate(['code' => '006'], ['description' => 'IV']);
        NameSuffix::updateOrCreate(['code' => '007'], ['description' => 'V']);
    }
}
