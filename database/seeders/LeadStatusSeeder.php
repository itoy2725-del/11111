<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LeadStatus;

class LeadStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['isim' => 'Yeni', 'renk' => '#3B82F6'],
            ['isim' => 'Aranmadı', 'renk' => '#6B7280'],
            ['isim' => 'Ulaşılamadı', 'renk' => '#F97316'],
            ['isim' => 'Tekrar Ara', 'renk' => '#EAB308'],
            ['isim' => 'Ön Onay', 'renk' => '#8B5CF6'],
            ['isim' => 'Avukata Aktarıldı', 'renk' => '#1E3A5F'],
            ['isim' => 'Tamamlandı', 'renk' => '#22C55E'],
            ['isim' => 'Kapatıldı', 'renk' => '#EF4444'],
        ];

        foreach ($statuses as $index => $status) {
            LeadStatus::create([
                'isim' => $status['isim'],
                'renk' => $status['renk'],
                'sira' => $index + 1,
            ]);
        }
    }
}
