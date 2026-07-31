<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LossRange;

class LossRangeSeeder extends Seeder
{
    public function run(): void
    {
        $ranges = [
            ['isim' => '0 - 1.000 USD', 'min' => 0, 'max' => 1000],
            ['isim' => '1.000 - 5.000 USD', 'min' => 1000, 'max' => 5000],
            ['isim' => '5.000 - 10.000 USD', 'min' => 5000, 'max' => 10000],
            ['isim' => '10.000 - 50.000 USD', 'min' => 10000, 'max' => 50000],
            ['isim' => '50.000 - 100.000 USD', 'min' => 50000, 'max' => 100000],
            ['isim' => '100.000+ USD', 'min' => 100000, 'max' => null],
        ];

        foreach ($ranges as $index => $range) {
            LossRange::create([
                'isim' => $range['isim'],
                'min_deger' => $range['min'],
                'max_deger' => $range['max'],
                'sira' => $index + 1,
            ]);
        }
    }
}
