<?php

namespace Database\Seeders;

use App\Models\EmploymentType;
use App\Models\Tenure;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TenureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data=[
            'less than 1 year',
            '1yr-2yrs',
            'beyond 2 years',
        ];
        foreach ($data as $index => $d) {
            Tenure::updateOrCreate(['code' => str_pad($index + 1, 3, '0', STR_PAD_LEFT)], ['description' => $d]);
        }
    }
}
