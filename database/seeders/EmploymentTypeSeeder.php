<?php

namespace Database\Seeders;

use App\Filament\Resources\EmploymentStatusResource\Pages\EditEmploymentStatus;
use App\Models\EmploymentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmploymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data=[
            'Locally Employed',
            'Self-Employed with Business',
            'Overseas Filipino Worker (OFW)'
        ];
        foreach ($data as $index => $d) {
            EmploymentType::updateOrCreate(['code' => str_pad($index + 1, 3, '0', STR_PAD_LEFT)], ['description' => $d]);
        }
    }
}
