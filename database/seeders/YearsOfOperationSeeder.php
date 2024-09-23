<?php

namespace Database\Seeders;

use App\Models\YearsOfOperation;
use Illuminate\Database\Seeder;

class YearsOfOperationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data=[
            '1 - 3 years',
            'more than 3 years'
        ];
        foreach ($data as $index => $d) {
            YearsOfOperation::updateOrCreate(['code' => str_pad($index + 1, 3, '0', STR_PAD_LEFT)], ['description' => $d]);
        }
    }
}
