<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WalletType;

class WalletTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'MetaMask',
            'Trust Wallet',
            'Binance',
            'Coinbase',
            'Ledger',
            'Diğer',
        ];

        foreach ($types as $index => $type) {
            WalletType::create([
                'isim' => $type,
                'sira' => $index + 1,
            ]);
        }
    }
}
