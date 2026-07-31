<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FraudType;

class FraudTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Forex Dolandırıcılığı',
            'Borsa Dolandırıcılığı',
            'Rug Pull',
            'Yanlış Adrese Gönderim',
            'Diğer',
        ];

        foreach ($types as $index => $type) {
            FraudType::create([
                'isim' => $type,
                'sira' => $index + 1,
            ]);
        }
    }
}
