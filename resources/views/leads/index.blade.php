@extends('layouts.app')

@section('title', 'Lead Yönetimi')

@section('content')
<div class="space-y-6">
    <!-- Header Title & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Lead Listesi & Müşteri Takibi</h1>
            <p class="text-xs text-gray-500 mt-1">Sistemdeki tüm potansiyel müşterilerinizi görüntüleyin, filtreleyin ve yönetin.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('import.index') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-sm transition-colors flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                <span>+ Yeni CSV Yükle</span>
            </a>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('leads.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Canlı Arama</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ad Soyad, Telefon, Email..." class="w-full px-3.5 py-2 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
            </div>
            
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Durum</label>
                <select name="status_id" class="w-full px-3.5 py-2 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all bg-white">
                    <option value="">Tüm Durumlar</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st->id }}" {{ request('status_id') == $st->id ? 'selected' : '' }}>{{ $st->isim }}</option>
                    @endforeach
                </select>
            </div>

            @if(auth()->user()->isAdmin())
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Operatör</label>
                <select name="atanan_operator_id" class="w-full px-3.5 py-2 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all bg-white">
                    <option value="">Tüm Operatörler</option>
                    @foreach($operators as $op)
                        <option value="{{ $op->id }}" {{ request('atanan_operator_id') == $op->id ? 'selected' : '' }}>{{ $op->isim }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Kampanya</label>
                <select name="campaign_name" class="w-full px-3.5 py-2 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all bg-white">
                    <option value="">Tüm Kampanyalar</option>
                    @foreach($campaigns as $camp)
                        <option value="{{ $camp }}" {{ request('campaign_name') == $camp ? 'selected' : '' }}>{{ $camp }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-sm transition-colors flex items-center justify-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    <span>Filtrele</span>
                </button>
                <a href="{{ route('leads.index') }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition-colors" title="Filtreleri Sıfırla">
                    🔄
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800 text-sm">Lead Verileri (Toplam: {{ $leads->total() }})</h3>
            <span class="text-xs text-gray-500 font-medium">Sayfa {{ $leads->currentPage() }} / {{ $leads->lastPage() }}</span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-gray-600">
                <thead class="text-[11px] text-gray-500 uppercase bg-slate-100 font-bold border-b border-gray-200">
                    <tr>
                        <th class="px-5 py-3.5 whitespace-nowrap">ID / Ad Soyad</th>
                        <th class="px-5 py-3.5 whitespace-nowrap">Telefon</th>
                        <th class="px-5 py-3.5 whitespace-nowrap">E-posta</th>
                        <th class="px-5 py-3.5 whitespace-nowrap">Durum</th>
                        <th class="px-5 py-3.5 whitespace-nowrap">Atanan Operatör</th>
                        <th class="px-5 py-3.5 whitespace-nowrap">Kampanya</th>
                        <th class="px-5 py-3.5 whitespace-nowrap">Tarih</th>
                        <th class="px-5 py-3.5 whitespace-nowrap text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($leads as $lead)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-xs">
                                        {{ mb_substr($lead->ad_soyad ?? 'M', 0, 1) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('leads.show', $lead->id) }}" class="font-bold text-gray-900 hover:text-indigo-600 block">
                                            {{ $lead->ad_soyad ?? 'İsimsiz Lead' }}
                                        </a>
                                        <span class="text-[10px] text-gray-400 font-mono">#{{ $lead->id }}</span>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="px-5 py-4 whitespace-nowrap font-mono font-bold text-indigo-600">
                                <a href="tel:{{ $lead->telefon }}" class="hover:underline flex items-center space-x-1">
                                    <span>📞 {{ $lead->telefon }}</span>
                                </a>
                            </td>
                            
                            <td class="px-5 py-4 whitespace-nowrap text-gray-700">
                                @if($lead->email)
                                    <a href="mailto:{{ $lead->email }}" class="hover:underline text-gray-600">
                                        {{ $lead->email }}
                                    </a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold border" style="background-color: {{ $lead->status->renk ?? '#e2e8f0' }}20; color: {{ $lead->status->renk ?? '#475569' }}; border-color: {{ $lead->status->renk ?? '#cbd5e1' }}">
                                    {{ $lead->status->isim ?? 'Yeni' }}
                                </span>
                            </td>
                            
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if($lead->operator)
                                    <div class="flex items-center space-x-1.5">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        <span class="font-semibold text-gray-800">{{ $lead->operator->isim }}</span>
                                    </div>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Atanmamış</span>
                                @endif
                            </td>
                            
                            <td class="px-5 py-4 whitespace-nowrap text-xs text-gray-600 font-medium">
                                {{ $lead->campaign_name ?: '-' }}
                            </td>
                            
                            <td class="px-5 py-4 whitespace-nowrap text-xs text-gray-500 font-mono">
                                {{ $lead->created_at ? $lead->created_at->format('d.m.Y H:i') : '-' }}
                            </td>
                            
                            <td class="px-5 py-4 whitespace-nowrap text-right space-x-2">
                                <a href="{{ route('leads.show', $lead->id) }}" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-xs font-bold transition-colors inline-flex items-center space-x-1">
                                    <span>İncele</span>
                                    <span>→</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                                <div class="max-w-xs mx-auto text-center space-y-2">
                                    <p class="text-3xl">📭</p>
                                    <p class="font-bold text-gray-700 text-base">Kayıtlı Lead Bulunamadı</p>
                                    <p class="text-xs text-gray-500">Arama kriterlerinize uygun lead bulunamadı veya henüz hiç CSV yüklenmedi.</p>
                                    <a href="{{ route('import.index') }}" class="inline-block mt-2 px-4 py-2 bg-indigo-600 text-white font-bold rounded-xl text-xs">CSV Yükle</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($leads->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-white">
                {{ $leads->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
