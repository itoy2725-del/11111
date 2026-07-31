<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Models\Lead;
use App\Models\LeadStatus;
use App\Models\User;
use App\Services\LeadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadController extends Controller
{
    protected LeadService $leadService;

    public function __construct(LeadService $leadService)
    {
        $this->leadService = $leadService;
    }

    public function index(Request $request)
    {
        // Auto-heal corrupted emails in MariaDB database (.cm -> .com, htmail -> hotmail, fix missing letters)
        try {
            \Illuminate\Support\Facades\DB::statement("UPDATE leads SET email = REPLACE(email, '.cm', '.com') WHERE email LIKE '%.cm'");
            \Illuminate\Support\Facades\DB::statement("UPDATE leads SET email = REPLACE(email, 'htmail', 'hotmail') WHERE email LIKE '%htmail%'");
            \Illuminate\Support\Facades\DB::statement("UPDATE leads SET email = REPLACE(email, 'kedi vadi', 'kedi_vadi') WHERE email LIKE '%kedi vadi%'");
            \Illuminate\Support\Facades\DB::statement("UPDATE leads SET email = REPLACE(email, 'hasancban4801', 'hasancoban4801') WHERE email LIKE '%hasancban4801%'");
            \Illuminate\Support\Facades\DB::statement("UPDATE leads SET email = REPLACE(email, 'gkhanyumusak', 'gokhanyumusak') WHERE email LIKE '%gkhanyumusak%'");
        } catch (\Throwable $e) {}

        $filters = $request->all();
        
        if (Auth::user()->isOperator()) {
            $filters['atanan_operator_id'] = Auth::id();
        }

        $perPage = $request->input('per_page', 25);
        $leads = $this->leadService->getLeads($filters, $perPage);
        $statuses = LeadStatus::orderBy('sira')->get();
        $operators = User::where('rol', 'operator')->where('aktif', true)->get();
        $campaigns = Lead::select('campaign_name')->whereNotNull('campaign_name')->where('campaign_name', '!=', '')->distinct()->pluck('campaign_name');

        return view('leads.index', compact('leads', 'filters', 'statuses', 'operators', 'campaigns'));
    }

    public function show(int $id)
    {
        $lead = $this->leadService->getLeadWithRelations($id);
        
        if (Auth::user()->isOperator() && $lead->atanan_operator_id !== Auth::id()) {
            abort(403, 'Bu lead\'e erişim yetkiniz yok.');
        }

        $statuses = LeadStatus::orderBy('sira')->get();
        $operators = User::where('rol', 'operator')->where('aktif', true)->get();

        return view('leads.show', compact('lead', 'statuses', 'operators'));
    }

    public function store(StoreLeadRequest $request)
    {
        $this->leadService->createLead($request->validated());
        return redirect()->route('leads.index')->with('success', 'Lead başarıyla oluşturuldu.');
    }

    public function update(UpdateLeadRequest $request, int $id)
    {
        $lead = $this->leadService->getLeadWithRelations($id);
        
        if (Auth::user()->isOperator() && $lead->atanan_operator_id !== Auth::id()) {
            abort(403);
        }

        $this->leadService->updateLead($lead, $request->validated());
        
        return redirect()->route('leads.show', $id)->with('success', 'Lead başarıyla güncellendi.');
    }

    public function destroy(int $id)
    {
        $lead = $this->leadService->getLeadWithRelations($id);
        $this->leadService->deleteLead($lead);
        
        return redirect()->route('leads.index')->with('success', 'Lead silindi.');
    }

    public function assignOperator(Request $request, int $id)
    {
        $request->validate([
            'operator_id' => 'required|exists:users,id',
            'sebep' => 'required|string|max:255'
        ]);

        $lead = $this->leadService->getLeadWithRelations($id);
        $this->leadService->assignOperator($lead, $request->operator_id, $request->sebep);
        
        return redirect()->back()->with('success', 'Operatör atandı.');
    }

    public function addNote(Request $request, int $id)
    {
        $request->validate(['note' => 'required|string']);
        
        $lead = $this->leadService->getLeadWithRelations($id);
        
        if (Auth::user()->isOperator() && $lead->atanan_operator_id !== Auth::id()) {
            abort(403);
        }

        $this->leadService->addNote($lead, $request->note, Auth::user());
        
        return redirect()->back()->with('success', 'Not eklendi.');
    }

    public function getLeadLogs(int $id)
    {
        $lead = Lead::with('histories.user')->findOrFail($id);
        
        if (Auth::user()->isOperator() && $lead->atanan_operator_id !== Auth::id()) {
            return response()->json(['error' => 'Yetkisiz erişim'], 403);
        }

        return response()->json([
            'lead' => [
                'id' => $lead->id,
                'ad_soyad' => $lead->ad_soyad ?? 'İsimsiz Lead',
                'telefon' => $lead->telefon,
            ],
            'logs' => $lead->histories
        ]);
    }

    public function getLeadData(int $id)
    {
        $lead = $this->leadService->getLeadWithRelations($id);
        
        if (Auth::user()->isOperator() && $lead->atanan_operator_id !== Auth::id()) {
            return response()->json(['error' => 'Yetkisiz erişim'], 403);
        }

        return response()->json($lead);
    }
}
