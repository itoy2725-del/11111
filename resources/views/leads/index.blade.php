@extends('layouts.app')

@section('title', 'Lead Yönetimi')

@section('content')
<div class="space-y-6">
    <!-- Header Title & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 transition-colors">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-slate-100 tracking-tight">Lead Listesi & Müşteri Takibi</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">Sistemdeki tüm potansiyel müşterilerinizi ve form yanıtlarını detaylıca inceleyin.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('import.index') }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md transition-colors flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                <span>+ Yeni CSV Yükle</span>
            </a>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 transition-colors">
        <form method="GET" action="{{ route('leads.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Canlı Arama</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ad Soyad, Telefon, Email..." class="w-full px-3.5 py-2 text-sm border border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Durum</label>
                <select name="status_id" class="w-full px-3.5 py-2 text-sm border border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                    <option value="">Tüm Durumlar</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st->id }}" {{ request('status_id') == $st->id ? 'selected' : '' }}>{{ $st->isim }}</option>
                    @endforeach
                </select>
            </div>

            @if(auth()->user()->isAdmin())
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Operatör</label>
                <select name="atanan_operator_id" class="w-full px-3.5 py-2 text-sm border border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                    <option value="">Tüm Operatörler</option>
                    @foreach($operators as $op)
                        <option value="{{ $op->id }}" {{ request('atanan_operator_id') == $op->id ? 'selected' : '' }}>{{ $op->isim }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 py-2 bg-slate-900 hover:bg-slate-800 dark:bg-indigo-600 dark:hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-sm transition-colors flex items-center justify-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    <span>Filtrele</span>
                </button>
                <a href="{{ route('leads.index') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-colors" title="Filtreleri Sıfırla">
                    🔄
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden transition-colors">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 flex justify-between items-center">
            <h3 class="font-bold text-slate-800 dark:text-slate-100 text-sm">Lead Verileri & Form Yanıtları (Toplam: {{ $leads->total() }})</h3>
            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Sayfa {{ $leads->currentPage() }} / {{ $leads->lastPage() }}</span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-600 dark:text-slate-300">
                <thead class="text-[11px] text-slate-500 dark:text-slate-400 uppercase bg-slate-100 dark:bg-slate-900 font-bold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5 whitespace-nowrap">ID / Ad Soyad</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Telefon</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">E-posta</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Durum (Seçilebilir)</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Atanan Operatör</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Dolandırıcılık Türü</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Kayıp Miktarı</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Cüzdan Türü</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Polise Şikayet</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Ek Güvenlik</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Mevcut Kripto</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Tarih</th>
                        <th class="px-4 py-3.5 whitespace-nowrap text-right">İşlem</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($leads as $lead)
                        @php
                            $fraud = \App\Services\ImportService::cleanMetaText($lead->fraudType->isim ?? 'Diğer');
                            $lossRaw = $lead->lossRange->isim ?? $lead->toplam_kripto ?? null;
                            $loss = \App\Services\ImportService::formatCryptoAmount($lossRaw);
                            if (empty($loss) || $loss === '-') { $loss = 'Belirtilmedi'; }
                            $wallet = \App\Services\ImportService::cleanMetaText($lead->walletType->isim ?? 'Diğer');
                            $complaint = \App\Services\ImportService::cleanMetaText($lead->sikayet_durumu ?: 'Hayır');
                            $security = \App\Services\ImportService::cleanMetaText($lead->ek_guvenlik_hizmeti ?: 'Evet');
                            $crypto = \App\Services\ImportService::formatCryptoAmount($lead->toplam_kripto ?: $lossRaw);
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <!-- Ad Soyad & ID -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="flex items-center space-x-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 font-extrabold flex items-center justify-center text-xs shrink-0">
                                        {{ mb_substr($lead->ad_soyad ?? 'M', 0, 1) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('leads.show', $lead->id) }}" class="font-bold text-slate-900 dark:text-slate-100 hover:text-indigo-600 dark:hover:text-indigo-400 block">
                                            {{ $lead->ad_soyad ?? 'İsimsiz Lead' }}
                                        </a>
                                        <span class="text-[10px] text-slate-400 font-mono">#{{ $lead->id }}</span>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Telefon -->
                            <td class="px-4 py-3.5 whitespace-nowrap font-mono font-bold text-slate-900 dark:text-slate-100 select-all">
                                {{ $lead->telefon }}
                            </td>
                            
                            <!-- E-posta -->
                            <td class="px-4 py-3.5 whitespace-nowrap text-slate-700 dark:text-slate-300">
                                {{ $lead->email ?: '-' }}
                            </td>
                            
                            <!-- Durum (Selectable) -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <form action="{{ route('leads.update', $lead->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PUT')
                                    <select 
                                        name="status_id" 
                                        onchange="this.form.submit()" 
                                        class="px-2.5 py-1 text-xs font-bold rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500 outline-none cursor-pointer shadow-sm"
                                        style="border-left-width: 4px; border-left-color: {{ $lead->status->renk ?? '#6366f1' }}"
                                    >
                                        @foreach($statuses as $st)
                                            <option value="{{ $st->id }}" {{ $lead->status_id == $st->id ? 'selected' : '' }}>
                                                {{ $st->isim }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                            
                            <!-- Atanan Operatör -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if($lead->operator)
                                    <div class="flex items-center space-x-1.5">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $lead->operator->isim }}</span>
                                    </div>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">Atanmamış</span>
                                @endif
                            </td>
                            
                            <!-- Dolandırıcılık Türü -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-red-50 dark:bg-red-950/40 text-red-700 dark:text-red-300 border border-red-100 dark:border-red-900">
                                    {{ $fraud ?: '-' }}
                                </span>
                            </td>

                            <!-- Kayıp Miktarı -->
                            <td class="px-4 py-3.5 whitespace-nowrap font-bold text-slate-800 dark:text-slate-200">
                                {{ $loss ?: '-' }}
                            </td>

                            <!-- Cüzdan Türü -->
                            <td class="px-4 py-3.5 whitespace-nowrap text-slate-700 dark:text-slate-300">
                                {{ $wallet ?: '-' }}
                            </td>

                            <!-- Polise Şikayet -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ strtolower($complaint) === 'evet' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">
                                    {{ ucfirst($complaint ?: '-') }}
                                </span>
                            </td>

                            <!-- Ek Güvenlik -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ strtolower($security) === 'evet' ? 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">
                                    {{ ucfirst($security ?: '-') }}
                                </span>
                            </td>

                            <!-- Mevcut Kripto -->
                            <td class="px-4 py-3.5 whitespace-nowrap font-medium text-slate-700 dark:text-slate-300">
                                {{ $crypto ?: '-' }}
                            </td>

                            <!-- Tarih -->
                            <td class="px-4 py-3.5 whitespace-nowrap text-xs text-slate-500 dark:text-slate-400 font-mono">
                                {{ $lead->created_at ? $lead->created_at->format('d.m.Y H:i') : '-' }}
                            </td>
                            
                            <!-- İşlem -->
                            <td class="px-4 py-3.5 whitespace-nowrap text-right">
                                <a href="{{ route('leads.show', $lead->id) }}" class="px-3.5 py-1.5 bg-indigo-50 dark:bg-indigo-950/50 hover:bg-indigo-100 dark:hover:bg-indigo-900 text-indigo-700 dark:text-indigo-300 rounded-xl text-xs font-bold transition-colors inline-flex items-center space-x-1">
                                    <span>İncele</span>
                                    <span>→</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="px-6 py-12 text-center text-slate-400">
                                <div class="max-w-xs mx-auto text-center space-y-2">
                                    <p class="text-3xl">📭</p>
                                    <p class="font-bold text-slate-700 dark:text-slate-200 text-base">Kayıtlı Lead Bulunamadı</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Arama kriterlerinize uygun lead bulunamadı veya henüz hiç CSV yüklenmedi.</p>
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
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
                {{ $leads->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
