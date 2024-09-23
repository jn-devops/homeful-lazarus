<?php

namespace Database\Seeders;

use App\Models\HomeOwnership;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HomeOwnershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data=[
            'Owned',
            'Living with Relatives',
            'Rented'
        ];

        foreach ($data as $index => $d) {
            HomeOwnership::updateOrCreate(['code' => str_pad($index + 1, 3, '0', STR_PAD_LEFT)], ['description' => $d]);
        }
    }
}
