<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DbCheckController extends Controller
{
    public function index()
    {
        $status = [
            'connected' => false,
            'driver' => config('database.default'),
            'database' => config('database.connections.' . config('database.default') . '.database'),
            'host' => config('database.connections.' . config('database.default') . '.host'),
            'error' => null,
            'tables' => [],
            'stats' => [],
        ];

        try {
            // Test DB Connection
            DB::connection()->getPdo();
            $status['connected'] = true;

            // List Tables & Record Counts
            $expectedTables = [
                'users' => 'Kullanıcılar',
                'lead_statuses' => 'Lead Durumları',
                'fraud_types' => 'Dolandırıcılık Türleri',
                'loss_ranges' => 'Kayıp Aralıkları',
                'wallet_types' => 'Cüzdan Türleri',
                'leads' => 'Leadler',
                'lead_histories' => 'Lead Geçmişi',
                'tasks' => 'Görevler',
                'imports' => 'İçe Aktarmalar',
                'audit_logs' => 'Audit Logları',
            ];

            foreach ($expectedTables as $tableName => $label) {
                $exists = Schema::hasTable($tableName);
                $count = $exists ? DB::table($tableName)->count() : 0;
                
                $status['tables'][] = [
                    'name' => $tableName,
                    'label' => $label,
                    'exists' => $exists,
                    'count' => $count,
                ];
            }

            // Fetch System Summary
            if (Schema::hasTable('users')) {
                $status['stats']['admin_count'] = DB::table('users')->where('rol', 'super_admin')->count();
                $status['stats']['operator_count'] = DB::table('users')->where('rol', 'operator')->count();
            }
            if (Schema::hasTable('leads')) {
                $status['stats']['lead_count'] = DB::table('leads')->count();
            }

        } catch (\Exception $e) {
            $status['connected'] = false;
            $status['error'] = $e->getMessage();
        }

        return view('db-check', compact('status'));
    }
}
