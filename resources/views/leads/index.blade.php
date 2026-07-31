@extends('layouts.app')

@section('title', 'Lead Yönetimi')

@section('content')
<div class="space-y-5">

    <!-- Top Executive Metric KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Toplam Lead -->
        <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-md rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-400">Toplam Lead</p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-slate-100 mt-0.5">{{ number_format($leads->total()) }}</h3>
                <span class="inline-flex items-center text-[10px] font-bold text-emerald-600 dark:text-emerald-400 mt-1">
                    <span>▲ Canlı Veri</span>
                </span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-indigo-600 to-violet-500 text-white flex items-center justify-center shadow-lg shadow-indigo-500/20 text-lg font-black">
                👥
            </div>
        </div>

        <!-- Card 2: Yeni Bekleyen -->
        <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-md rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-400">Yeni Leadler</p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-slate-100 mt-0.5">
                    {{ $leads->where('status_id', 1)->count() }}
                </h3>
                <span class="inline-flex items-center text-[10px] font-bold text-amber-600 dark:text-amber-400 mt-1">
                    <span>● İşlem Bekliyor</span>
                </span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-500 text-white flex items-center justify-center shadow-lg shadow-amber-500/20 text-lg font-black">
                ⚡
            </div>
        </div>

        <!-- Card 3: Atanan -->
        <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-md rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-400">Atanan Operatör</p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-slate-100 mt-0.5">
                    {{ $leads->whereNotNull('atanan_operator_id')->count() }}
                </h3>
                <span class="inline-flex items-center text-[10px] font-bold text-blue-600 dark:text-blue-400 mt-1">
                    <span>✓ Operatörde</span>
                </span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-blue-600 to-cyan-500 text-white flex items-center justify-center shadow-lg shadow-blue-500/20 text-lg font-black">
                🎧
            </div>
        </div>

        <!-- Card 4: Aktif Sayfa -->
        <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-md rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-400">Sayfa Durumu</p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-slate-100 mt-0.5">
                    {{ $leads->currentPage() }} / {{ $leads->lastPage() }}
                </h3>
                <span class="inline-flex items-center text-[10px] font-bold text-purple-600 dark:text-purple-400 mt-1">
                    <span>📄 Dinamik Sayfa</span>
                </span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-purple-600 to-pink-500 text-white flex items-center justify-center shadow-lg shadow-purple-500/20 text-lg font-black">
                📊
            </div>
        </div>
    </div>

    <!-- Header Title & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md p-5 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800/80 transition-colors">
        <div>
            <h1 class="text-xl font-black text-slate-900 dark:text-slate-100 tracking-tight">Lead Listesi & Müşteri Takibi</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-medium">Sistemdeki tüm potansiyel müşterilerinizi ve form yanıtlarını ekrana tam sığacak şekilde görüntüleyin.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('import.index') }}" class="px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-xl text-xs font-bold shadow-lg shadow-indigo-600/30 transition-all flex items-center space-x-2 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                <span>+ Yeni CSV Yükle</span>
            </a>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-md rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800/80 p-4 transition-colors">
        <form method="GET" action="{{ route('leads.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-[10px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Canlı Arama</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ad Soyad, Telefon, Email..." class="w-full px-3 py-1.5 text-xs border border-slate-300/80 dark:border-slate-700/80 dark:bg-slate-800/80 dark:text-slate-100 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium">
            </div>
            
            <div>
                <label class="block text-[10px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Durum</label>
                <select name="status_id" class="w-full px-3 py-1.5 text-xs border border-slate-300/80 dark:border-slate-700/80 dark:bg-slate-800/80 dark:text-slate-100 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium">
                    <option value="">Tüm Durumlar</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st->id }}" {{ request('status_id') == $st->id ? 'selected' : '' }}>{{ $st->isim }}</option>
                    @endforeach
                </select>
            </div>

            @if(auth()->user()->isAdmin())
            <div>
                <label class="block text-[10px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Operatör</label>
                <select name="atanan_operator_id" class="w-full px-3 py-1.5 text-xs border border-slate-300/80 dark:border-slate-700/80 dark:bg-slate-800/80 dark:text-slate-100 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium">
                    <option value="">Tüm Operatörler</option>
                    @foreach($operators as $op)
                        <option value="{{ $op->id }}" {{ request('atanan_operator_id') == $op->id ? 'selected' : '' }}>{{ $op->isim }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 py-1.5 bg-slate-900 hover:bg-slate-800 dark:bg-indigo-600 dark:hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md transition-colors flex items-center justify-center space-x-1">
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
    <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-md rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200/80 dark:border-slate-800/80 overflow-hidden transition-colors">
        <div class="px-5 py-3 border-b border-slate-200/80 dark:border-slate-800/80 bg-slate-50/80 dark:bg-slate-950/80 flex justify-between items-center">
            <h3 class="font-black text-slate-800 dark:text-slate-100 text-xs flex items-center space-x-2">
                <span>📋 Lead Verileri & Form Yanıtları</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300">
                    {{ $leads->total() }} Kayıt
                </span>
            </h3>
            <span class="text-[11px] text-slate-500 dark:text-slate-400 font-bold">Sayfa {{ $leads->currentPage() }} / {{ $leads->lastPage() }}</span>
        </div>
        
        <div class="w-full overflow-hidden">
            <table class="w-full text-[11px] text-left text-slate-600 dark:text-slate-300">
                <thead class="text-[10px] text-slate-400 dark:text-slate-400 uppercase bg-slate-100/70 dark:bg-slate-900/70 font-extrabold border-b border-slate-200/80 dark:border-slate-800/80 tracking-wider">
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
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
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
                        <tr class="hover:bg-indigo-50/40 dark:hover:bg-indigo-950/20 transition-all duration-150">
                            <!-- Ad Soyad & Telefon & Email -->
                            <td class="px-3 py-2.5">
                                <div class="flex items-center space-x-2.5">
                                    <div class="w-7 h-7 rounded-xl bg-gradient-to-tr from-indigo-500 to-violet-500 text-white font-black flex items-center justify-center text-[11px] shadow-sm shrink-0">
                                        {{ mb_substr($lead->ad_soyad ?? 'M', 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('leads.show', $lead->id) }}" class="font-extrabold text-slate-900 dark:text-slate-100 hover:text-indigo-600 dark:hover:text-indigo-400 block truncate">
                                            {{ $lead->ad_soyad ?? 'İsimsiz Lead' }}
                                        </a>
                                        <div class="flex items-center space-x-2 text-[10px] text-slate-500 dark:text-slate-400">
                                            <span class="font-mono font-bold select-all text-slate-800 dark:text-slate-200">{{ $lead->telefon }}</span>
                                            @if($lead->email)
                                                <span>•</span>
                                                <span class="truncate max-w-[110px] inline-block font-medium" title="{{ $lead->email }}">{{ $lead->email }}</span>
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
                                        class="px-2 py-0.5 text-[10px] font-extrabold rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500 outline-none cursor-pointer shadow-sm transition-all"
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
                                    <div class="flex items-center space-x-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span class="font-bold text-slate-800 dark:text-slate-200 truncate block max-w-[90px]" title="{{ $lead->operator->isim }}">{{ $lead->operator->isim }}</span>
                                    </div>
                                @else
                                    <span class="px-2 py-0.5 rounded-lg text-[9px] font-extrabold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/80">Atanmamış</span>
                                @endif
                            </td>
                            
                            <!-- Dolandırıcılık Türü -->
                            <td class="px-2 py-2.5">
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-extrabold bg-red-50 dark:bg-red-950/40 text-red-700 dark:text-red-300 border border-red-200/60 dark:border-red-900/60 inline-block truncate max-w-[120px]" title="{{ $fraud }}">
                                    {{ $fraud }}
                                </span>
                            </td>

                            <!-- Kayıp Miktarı -->
                            <td class="px-2 py-2.5 font-bold text-slate-800 dark:text-slate-200">
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-black bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200/60 dark:border-indigo-900/60">
                                    {{ $loss }}
                                </span>
                            </td>

                            <!-- Cüzdan Türü -->
                            <td class="px-2 py-2.5 text-slate-800 dark:text-slate-200 font-bold">
                                {{ $wallet }}
                            </td>

                            <!-- İhbar & Güvenlik -->
                            <td class="px-2 py-2.5">
                                <div class="flex items-center space-x-1 text-[9px] font-bold">
                                    <span class="px-1.5 py-0.5 rounded-md {{ strtolower($complaint) === 'evet' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400' }}" title="Polise İhbar">
                                        İhbar: {{ ucfirst($complaint) }}
                                    </span>
                                    <span class="px-1.5 py-0.5 rounded-md {{ strtolower($security) === 'evet' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300 border border-blue-200 dark:border-blue-800' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400' }}" title="Ek Güvenlik">
                                        Güv: {{ ucfirst($security) }}
                                    </span>
                                </div>
                            </td>

                            <!-- Mevcut Kripto -->
                            <td class="px-2 py-2.5 font-bold text-slate-700 dark:text-slate-300">
                                {{ $crypto }}
                            </td>

                            <!-- Tarih -->
                            <td class="px-2 py-2.5 text-slate-500 dark:text-slate-400 font-mono text-[10px] font-medium">
                                {{ $lead->created_at ? $lead->created_at->format('d.m.Y H:i') : '-' }}
                            </td>
                            
                            <!-- İşlem -->
                            <td class="px-2 py-2.5 text-right">
                                <a href="{{ route('leads.show', $lead->id) }}" class="px-2.5 py-1 bg-indigo-50 dark:bg-indigo-950/50 hover:bg-indigo-600 hover:text-white dark:hover:bg-indigo-600 text-indigo-700 dark:text-indigo-300 rounded-xl text-[10px] font-extrabold transition-all duration-150 inline-flex items-center space-x-1 shadow-sm">
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
            <div class="px-5 py-3 border-t border-slate-200/80 dark:border-slate-800/80 bg-white dark:bg-slate-900">
                {{ $leads->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
