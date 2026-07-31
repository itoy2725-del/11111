<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getDashboardStats(): array
    {
        $totalLeads = Lead::count();
        $todayLeads = Lead::whereDate('created_at', Carbon::today())->count();
        $pendingTasks = Task::where('durum', 'bekliyor')->count();
        $activeOperators = User::where('rol', 'operator')->where('aktif', true)->count();
        
        $statusDistribution = DB::table('leads')
            ->join('lead_statuses', 'leads.status_id', '=', 'lead_statuses.id')
            ->select('lead_statuses.isim', 'lead_statuses.renk', DB::raw('count(*) as count'))
            ->whereNull('leads.deleted_at')
            ->groupBy('lead_statuses.id', 'lead_statuses.isim', 'lead_statuses.renk')
            ->orderBy('lead_statuses.sira')
            ->get();
            
        $operatorPerformance = User::where('rol', 'operator')
            ->where('aktif', true)
            ->withCount('leads as total_leads')
            ->get()
            ->map(function ($op) {
                $completed = Lead::where('atanan_operator_id', $op->id)
                    ->whereHas('status', fn($q) => $q->where('isim', 'Tamamlandı'))
                    ->count();
                $pending = Lead::where('atanan_operator_id', $op->id)
                    ->whereHas('status', fn($q) => $q->whereNotIn('isim', ['Tamamlandı', 'Kapatıldı']))
                    ->count();
                    
                return [
                    'isim' => $op->isim,
                    'total_leads' => $op->total_leads,
                    'completed' => $completed,
                    'pending' => $pending,
                ];
            });
            
        $campaignStats = Lead::select('campaign_name', DB::raw('count(*) as count'))
            ->whereNotNull('campaign_name')
            ->where('campaign_name', '!=', '')
            ->groupBy('campaign_name')
            ->orderByDesc('count')
            ->get();
            
        $fraudTypeStats = DB::table('leads')
            ->join('fraud_types', 'leads.fraud_type_id', '=', 'fraud_types.id')
            ->select('fraud_types.isim', DB::raw('count(*) as count'))
            ->whereNull('leads.deleted_at')
            ->groupBy('fraud_types.id', 'fraud_types.isim')
            ->orderByDesc('count')
            ->get();
            
        $adPerformance = Lead::select('ad_name', DB::raw('count(*) as count'))
            ->whereNotNull('ad_name')
            ->where('ad_name', '!=', '')
            ->groupBy('ad_name')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        $recentLeads = Lead::with(['status', 'operator'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return [
            'total_leads' => $totalLeads,
            'today_leads' => $todayLeads,
            'pending_tasks' => $pendingTasks,
            'active_operators' => $activeOperators,
            'status_distribution' => $statusDistribution,
            'operator_performance' => $operatorPerformance,
            'campaign_stats' => $campaignStats,
            'fraud_type_stats' => $fraudTypeStats,
            'ad_performance' => $adPerformance,
            'recent_leads' => $recentLeads,
        ];
    }

    public function getOperatorPerformance(?string $dateFrom = null, ?string $dateTo = null): Collection
    {
        $query = DB::table('leads')
            ->join('users', 'leads.atanan_operator_id', '=', 'users.id')
            ->select('users.isim', 'users.id', DB::raw('count(*) as lead_count'))
            ->where('users.rol', 'operator')
            ->whereNull('leads.deleted_at')
            ->groupBy('users.id', 'users.isim');
            
        if ($dateFrom) {
            $query->whereDate('leads.created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('leads.created_at', '<=', $dateTo);
        }
        
        return $query->get();
    }

    public function getCampaignStats(?string $dateFrom = null, ?string $dateTo = null): Collection
    {
        $query = Lead::select('campaign_name', DB::raw('count(*) as count'))
            ->whereNotNull('campaign_name')
            ->where('campaign_name', '!=', '')
            ->groupBy('campaign_name')
            ->orderByDesc('count');
            
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }
        
        return $query->get();
    }
}
