<?php

namespace App\Services;

use App\Models\Task;
use App\Models\LeadHistory;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TaskService
{
    public function getTasks(array $filters, ?int $operatorId = null): LengthAwarePaginator
    {
        $query = Task::with(['lead', 'operator']);
        
        if ($operatorId) {
            $query->where('operator_id', $operatorId);
        }
        
        if (!empty($filters['durum'])) {
            $query->where('durum', $filters['durum']);
        }
        
        if (!empty($filters['date_from'])) {
            $query->whereDate('tarih', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('tarih', '<=', $filters['date_to']);
        }

        return $query->orderBy('tarih', 'asc')->paginate(25);
    }

    public function createTask(array $data): Task
    {
        $task = Task::create([
            'lead_id' => $data['lead_id'] ?? null,
            'operator_id' => $data['operator_id'],
            'baslik' => $data['baslik'],
            'aciklama' => $data['aciklama'] ?? null,
            'tarih' => $data['tarih'],
            'durum' => 'bekliyor',
        ]);
        
        if ($task->lead_id) {
            LeadHistory::create([
                'lead_id' => $task->lead_id,
                'islem' => 'Görev oluşturuldu: ' . $task->baslik,
                'yapan_kullanici' => Auth::id(),
            ]);
        }
        
        AuditService::logStatic('Görev oluşturuldu', 'Task', $task->id);
        
        return $task;
    }

    public function updateTaskStatus(Task $task, string $newDurum): Task
    {
        $oldDurum = $task->durum;
        $task->update(['durum' => $newDurum]);
        
        if ($task->lead_id) {
            LeadHistory::create([
                'lead_id' => $task->lead_id,
                'islem' => 'Görev durumu güncellendi: ' . $task->baslik,
                'eski_deger' => $oldDurum,
                'yeni_deger' => $newDurum,
                'yapan_kullanici' => Auth::id(),
            ]);
        }
        
        AuditService::logStatic('Görev durumu güncellendi', 'Task', $task->id, $oldDurum, $newDurum);
        
        return $task;
    }

    public function getTodayTasks(int $operatorId): Collection
    {
        return Task::with('lead')
            ->where('operator_id', $operatorId)
            ->whereDate('tarih', Carbon::today())
            ->where('durum', '!=', 'tamamlandi')
            ->orderBy('tarih', 'asc')
            ->get();
    }
}
