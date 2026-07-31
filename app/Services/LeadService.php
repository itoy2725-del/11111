<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\LeadHistory;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LeadService
{
    public function getLeads(array $filters, int $perPage = 25): LengthAwarePaginator
    {
        $query = Lead::with(['status', 'operator', 'fraudType']);
        
        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('telefon', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('ad_soyad', 'like', '%' . $filters['search'] . '%');
            });
        }
        
        if (!empty($filters['status_id'])) {
            $query->where('status_id', $filters['status_id']);
        }
        if (!empty($filters['atanan_operator_id'])) {
            $query->where('atanan_operator_id', $filters['atanan_operator_id']);
        }
        if (!empty($filters['campaign_name'])) {
            $query->where('campaign_name', $filters['campaign_name']);
        }
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('id', 'desc')->paginate($perPage);
    }

    public function getLeadWithRelations(int $id): Lead
    {
        return Lead::with([
            'status', 'operator', 'fraudType', 'lossRange', 'walletType',
            'histories' => fn($q) => $q->orderByDesc('created_at'),
            'histories.user',
            'tasks.operator'
        ])->findOrFail($id);
    }

    public function createLead(array $data): Lead
    {
        $lead = Lead::create($data);
        
        LeadHistory::create([
            'lead_id' => $lead->id,
            'islem' => 'Lead oluşturuldu',
            'yapan_kullanici' => Auth::id(),
        ]);
        
        AuditService::logStatic('Lead oluşturuldu', 'Lead', $lead->id);
        
        return $lead;
    }

    public function updateLead(Lead $lead, array $data): Lead
    {
        $oldData = $lead->toArray();
        $lead->update($data);
        
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $oldData) && $oldData[$key] != $value) {
                LeadHistory::create([
                    'lead_id' => $lead->id,
                    'islem' => $key . ' güncellendi',
                    'eski_deger' => is_array($oldData[$key]) ? json_encode($oldData[$key]) : ($oldData[$key] ?? ''),
                    'yeni_deger' => is_array($value) ? json_encode($value) : ($value ?? ''),
                    'yapan_kullanici' => Auth::id(),
                ]);
            }
        }
        
        AuditService::logStatic('Lead güncellendi', 'Lead', $lead->id);
        
        return $lead;
    }

    public function assignOperator(Lead $lead, int $newOperatorId, string $sebep): void
    {
        $oldOperator = $lead->operator;
        $newOperator = User::findOrFail($newOperatorId);
        
        $lead->update(['atanan_operator_id' => $newOperatorId]);
        
        LeadHistory::create([
            'lead_id' => $lead->id,
            'islem' => 'Operatör değiştirildi. Sebep: ' . $sebep,
            'eski_deger' => $oldOperator ? $oldOperator->isim : 'Atanmamış',
            'yeni_deger' => $newOperator->isim,
            'yapan_kullanici' => Auth::id(),
        ]);
        
        AuditService::logStatic(
            'Operatör atandı',
            'Lead',
            $lead->id,
            $oldOperator ? $oldOperator->isim : 'Atanmamış',
            $newOperator->isim . ' - Sebep: ' . $sebep
        );
    }

    public function deleteLead(Lead $lead): void
    {
        $leadId = $lead->id;
        $lead->delete();
        AuditService::logStatic('Lead silindi', 'Lead', $leadId);
    }

    public function addNote(Lead $lead, string $note, User $user): void
    {
        $oldNote = $lead->operator_notu;

        // Operatör 15 dakika kuralı
        if ($user->isOperator() && $oldNote) {
            $lastNoteHistory = LeadHistory::where('lead_id', $lead->id)
                ->where('yapan_kullanici', $user->id)
                ->whereIn('islem', ['Not eklendi', 'Not güncellendi'])
                ->orderByDesc('created_at')
                ->first();

            if ($lastNoteHistory && $lastNoteHistory->created_at->diffInMinutes(now()) > 15) {
                abort(403, '15 dakikadan eski notlar düzenlenemez.');
            }
        }

        $lead->update(['operator_notu' => $note]);

        LeadHistory::create([
            'lead_id' => $lead->id,
            'islem' => $oldNote ? 'Not güncellendi' : 'Not eklendi',
            'eski_deger' => $oldNote,
            'yeni_deger' => $note,
            'yapan_kullanici' => $user->id,
        ]);

        AuditService::logStatic('Not güncellendi', 'Lead', $lead->id, $oldNote, $note);
    }
}
