@extends('layouts.app')

@section('title', 'Lead Yönetimi')

@section('content')
<div class="space-y-6">
    <!-- Header Title & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 transition-colors">
        <div>
            <h1 class="text-xl font-black text-slate-900 dark:text-slate-100 tracking-tight">Lead Listesi & Müşteri Takibi</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-medium">Sistemdeki tüm potansiyel müşterilerinizi ve form yanıtlarını ekrana tam sığacak şekilde görüntüleyin.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('import.index') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md transition-colors flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                <span>+ Yeni CSV Yükle</span>
            </a>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-4 transition-colors">
        <form method="GET" action="{{ route('leads.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Canlı Arama</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ad Soyad, Telefon, Email..." class="w-full px-3 py-1.5 text-xs border border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
            </div>
            
            <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Durum</label>
                <select name="status_id" class="w-full px-3 py-1.5 text-xs border border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                    <option value="">Tüm Durumlar</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st->id }}" {{ request('status_id') == $st->id ? 'selected' : '' }}>{{ $st->isim }}</option>
                    @endforeach
                </select>
            </div>

            @if(auth()->user()->isAdmin())
            <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Operatör</label>
                <select name="atanan_operator_id" class="w-full px-3 py-1.5 text-xs border border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                    <option value="">Tüm Operatörler</option>
                    @foreach($operators as $op)
                        <option value="{{ $op->id }}" {{ request('atanan_operator_id') == $op->id ? 'selected' : '' }}>{{ $op->isim }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 py-1.5 bg-slate-900 hover:bg-slate-800 dark:bg-indigo-600 dark:hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-sm transition-colors flex items-center justify-center space-x-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    <span>Filtrele</span>
                </button>
                <a href="{{ route('leads.index') }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-colors" title="Filtreleri Sıfırla">
                    🔄
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table Card (FITS 100% ON SCREEN WITHOUT HORIZONTAL SCROLL) -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden transition-colors">
        <div class="px-5 py-3 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 flex justify-between items-center">
            <h3 class="font-bold text-slate-800 dark:text-slate-100 text-xs">Lead Verileri & Form Yanıtları (Toplam: {{ $leads->total() }})</h3>
            <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Sayfa {{ $leads->currentPage() }} / {{ $leads->lastPage() }}</span>
        </div>
        
        <div class="w-full overflow-hidden">
            <table class="w-full text-[11px] text-left text-slate-600 dark:text-slate-300">
                <thead class="text-[10px] text-slate-500 dark:text-slate-400 uppercase bg-slate-100 dark:bg-slate-900 font-bold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-3 py-2.5">Ad Soyad / İletişim</th>
                        <th class="px-2 py-2.5">Durum</th>
                        <th class="px-2 py-2.5">Operatör</th>
                        <th class="px-2 py-2.5">Dolandırıcılık Türü</th>
                        <th class="px-2 py-2.5">Kayıp Miktarı</th>
                        <th class="px-2 py-2.5">Cüzdan</th>
                        <th class="px-2 py-2.5">İhbar & Güvenlik</th>
                        <th class="px-2 py-2.5">Mevcut Kripto</th>
                        <th class="px-2 py-2.5">Tarih</th>
                        <th class="px-2 py-2.5 text-right">İşlem</th>
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
                            <!-- Ad Soyad & Telefon & Email -->
                            <td class="px-3 py-2.5">
                                <div class="flex items-center space-x-2">
                                    <div class="w-6 h-6 rounded bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 font-extrabold flex items-center justify-center text-[10px] shrink-0">
                                        {{ mb_substr($lead->ad_soyad ?? 'M', 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('leads.show', $lead->id) }}" class="font-bold text-slate-900 dark:text-slate-100 hover:text-indigo-600 dark:hover:text-indigo-400 block truncate">
                                            {{ $lead->ad_soyad ?? 'İsimsiz Lead' }}
                                        </a>
                                        <div class="flex items-center space-x-2 text-[10px] text-slate-500 dark:text-slate-400">
                                            <span class="font-mono font-semibold select-all text-slate-800 dark:text-slate-200">{{ $lead->telefon }}</span>
                                            @if($lead->email)
                                                <span>•</span>
                                                <span class="truncate max-w-[110px] inline-block" title="{{ $lead->email }}">{{ $lead->email }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Durum (Selectable) -->
                            <td class="px-2 py-2.5">
                                <form action="{{ route('leads.update', $lead->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PUT')
                                    <select 
                                        name="status_id" 
                                        onchange="this.form.submit()" 
                                        class="px-1.5 py-0.5 text-[10px] font-bold rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 focus:ring-1 focus:ring-indigo-500 outline-none cursor-pointer shadow-sm"
                                        style="border-left-width: 3px; border-left-color: {{ $lead->status->renk ?? '#6366f1' }}"
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
                            <td class="px-2 py-2.5">
                                @if($lead->operator)
                                    <span class="font-semibold text-slate-800 dark:text-slate-200 truncate block max-w-[90px]" title="{{ $lead->operator->isim }}">{{ $lead->operator->isim }}</span>
                                @else
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">Atanmamış</span>
                                @endif
                            </td>
                            
                            <!-- Dolandırıcılık Türü -->
                            <td class="px-2 py-2.5">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-50 dark:bg-red-950/40 text-red-700 dark:text-red-300 border border-red-100 dark:border-red-900 inline-block truncate max-w-[120px]" title="{{ $fraud }}">
                                    {{ $fraud }}
                                </span>
                            </td>

                            <!-- Kayıp Miktarı -->
                            <td class="px-2 py-2.5 font-bold text-slate-800 dark:text-slate-200">
                                <span class="px-1.5 py-0.5 rounded text-[10px] bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200">
                                    {{ $loss }}
                                </span>
                            </td>

                            <!-- Cüzdan Türü -->
                            <td class="px-2 py-2.5 text-slate-700 dark:text-slate-300 font-medium">
                                {{ $wallet }}
                            </td>

                            <!-- İhbar & Güvenlik -->
                            <td class="px-2 py-2.5">
                                <div class="flex items-center space-x-1 text-[9px] font-bold">
                                    <span class="px-1 rounded {{ strtolower($complaint) === 'evet' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400' }}" title="Polise İhbar">
                                        İhbar: {{ ucfirst($complaint) }}
                                    </span>
                                    <span class="px-1 rounded {{ strtolower($security) === 'evet' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400' }}" title="Ek Güvenlik">
                                        Güv: {{ ucfirst($security) }}
                                    </span>
                                </div>
                            </td>

                            <!-- Mevcut Kripto -->
                            <td class="px-2 py-2.5 font-medium text-slate-700 dark:text-slate-300">
                                {{ $crypto }}
                            </td>

                            <!-- Tarih -->
                            <td class="px-2 py-2.5 text-slate-500 dark:text-slate-400 font-mono text-[10px]">
                                {{ $lead->created_at ? $lead->created_at->format('d.m.Y H:i') : '-' }}
                            </td>
                            
                            <!-- İşlem -->
                            <td class="px-2 py-2.5 text-right">
                                <a href="{{ route('leads.show', $lead->id) }}" class="px-2.5 py-1 bg-indigo-50 dark:bg-indigo-950/50 hover:bg-indigo-100 dark:hover:bg-indigo-900 text-indigo-700 dark:text-indigo-300 rounded-lg text-[10px] font-bold transition-colors inline-flex items-center space-x-0.5">
                                    <span>İncele</span>
                                    <span>→</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center text-slate-400">
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
            <div class="px-5 py-3 border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
                {{ $leads->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
