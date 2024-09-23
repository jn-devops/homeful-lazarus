<?php

namespace Database\Seeders;

use App\Models\EmploymentStatus;
use Illuminate\Database\Seeder;

class EmploymentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $data=[
            'Contractual',
            'Regular'
        ];
        foreach ($data as $index => $d) {
            EmploymentStatus::updateOrCreate(['code' => str_pad($index + 1, 3, '0', STR_PAD_LEFT)], ['description' => $d]);
        }
    }
}
