<?php

namespace Database\Seeders;

use App\Models\CivilStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CivilStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CivilStatus::updateOrCreate(['code' => '001'], ['description' => 'Single']);
        CivilStatus::updateOrCreate(['code' => '002'], ['description' => 'Married']);
        CivilStatus::updateOrCreate(['code' => '003'], ['description' => 'Widowed']);
        CivilStatus::updateOrCreate(['code' => '004'], ['description' => 'Divorced']);
        CivilStatus::updateOrCreate(['code' => '005'], ['description' => 'Separated']);
    }
}
