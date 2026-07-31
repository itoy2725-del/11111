@extends('layouts.app')

@section('title', 'Lead Detayı #' . $lead->id)

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3 text-xs font-bold text-slate-500 dark:text-slate-400">
                <li>
                    <a href="{{ route('leads.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Leadler</a>
                </li>
                <li>
                    <span class="mx-2">/</span>
                    <span class="text-slate-900 dark:text-slate-100 font-extrabold">#{{ $lead->id }} - {{ $lead->ad_soyad ?? 'İsimsiz Lead' }}</span>
                </li>
            </ol>
        </nav>
        <a href="{{ route('leads.index') }}" class="px-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all shadow-sm">
            ← Listeye Dön
        </a>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Left 2 Columns: Information -->
        <div class="xl:col-span-2 space-y-6">
            
            <!-- Main Lead Info Card -->
            <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-md rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800/80 p-6">
                <div class="flex justify-between items-start mb-6">
                    <div class="flex items-center space-x-4">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-indigo-600 to-violet-600 text-white font-black text-xl flex items-center justify-center shadow-lg shadow-indigo-600/20">
                            {{ mb_substr($lead->ad_soyad ?? 'M', 0, 1) }}
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-slate-900 dark:text-slate-100 tracking-tight">{{ $lead->ad_soyad ?? 'İsimsiz Lead' }}</h2>
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1">Oluşturulma Tarihi: {{ $lead->created_at ? $lead->created_at->format('d F Y H:i') : '-' }}</p>
                        </div>
                    </div>
                    <div>
                        <span class="px-3.5 py-1.5 rounded-xl text-xs font-extrabold border shadow-sm" style="background-color: {{ $lead->status->renk ?? '#e2e8f0' }}20; color: {{ $lead->status->renk ?? '#475569' }}; border-color: {{ $lead->status->renk ?? '#cbd5e1' }}">
                            {{ $lead->status->isim ?? 'Yeni' }}
                        </span>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <div>
                        <p class="text-xs font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider">Telefon Numarası</p>
                        <p class="text-base font-extrabold font-mono text-slate-900 dark:text-slate-100 mt-1 select-all">
                            {{ $lead->telefon }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider">E-posta Adresi</p>
                        <p class="text-base font-bold text-slate-900 dark:text-slate-100 mt-1 select-all">{{ $lead->email ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider">Atanan Operatör</p>
                        <p class="text-base font-bold text-slate-900 dark:text-slate-100 mt-1">
                            {{ $lead->operator ? $lead->operator->isim : 'Atanmamış' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form Answers & Cyber Security Details Card -->
            <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-md rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800/80 p-6">
                <h3 class="text-base font-extrabold text-slate-900 dark:text-slate-100 mb-4 pb-3 border-b border-slate-100 dark:border-slate-800 flex items-center space-x-2">
                    <span>📋 Başvuru Formu & Siber Güvenlik Yanıtları</span>
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-xl border border-slate-200/60 dark:border-slate-700/60">
                        <p class="text-xs font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider">Dolandırıcılık Türü</p>
                        <p class="text-base font-black text-red-600 dark:text-red-400 mt-1">
                            {{ \App\Services\ImportService::cleanMetaText($lead->fraudType->isim ?? $lead->sikayet_durumu ?? '-') }}
                        </p>
                    </div>
                    
                    <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-xl border border-slate-200/60 dark:border-slate-700/60">
                        <p class="text-xs font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider">Kayıp Miktarı</p>
                        <p class="text-base font-black text-indigo-600 dark:text-indigo-400 mt-1">
                            {{ \App\Services\ImportService::formatCryptoAmount($lead->lossRange->isim ?? '-') }}
                        </p>
                    </div>
                    
                    <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-xl border border-slate-200/60 dark:border-slate-700/60">
                        <p class="text-xs font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider">Cüzdan Türü</p>
                        <p class="text-base font-extrabold text-slate-900 dark:text-slate-100 mt-1">
                            {{ \App\Services\ImportService::cleanMetaText($lead->walletType->isim ?? '-') }}
                        </p>
                    </div>
                    
                    <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-xl border border-slate-200/60 dark:border-slate-700/60">
                        <p class="text-xs font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider">Polise / İhbar Şikayet</p>
                        <p class="text-base font-extrabold text-slate-900 dark:text-slate-100 mt-1">
                            {{ \App\Services\ImportService::cleanMetaText($lead->sikayet_durumu ?: '-') }}
                        </p>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-xl border border-slate-200/60 dark:border-slate-700/60">
                        <p class="text-xs font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider">Ek Güvenlik Hizmeti</p>
                        <p class="text-base font-extrabold text-slate-900 dark:text-slate-100 mt-1">
                            {{ \App\Services\ImportService::cleanMetaText($lead->ek_guvenlik_hizmeti ?: '-') }}
                        </p>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-xl border border-slate-200/60 dark:border-slate-700/60">
                        <p class="text-xs font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider">Mevcut Toplam Kripto</p>
                        <p class="text-base font-black text-slate-900 dark:text-slate-100 mt-1">
                            {{ \App\Services\ImportService::formatCryptoAmount($lead->toplam_kripto ?: '-') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Meta Ad Technical Details Card -->
            <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-md rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800/80 p-6">
                <h3 class="text-base font-extrabold text-slate-900 dark:text-slate-100 mb-4 pb-3 border-b border-slate-100 dark:border-slate-800 flex items-center space-x-2">
                    <span>📢 Meta Reklam Detayları</span>
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs font-medium text-slate-600 dark:text-slate-300">
                    <div>
                        <span class="font-bold text-slate-400 block uppercase">Kampanya Adı:</span>
                        <span class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ $lead->campaign_name ?: '-' }}</span>
                    </div>
                    <div>
                        <span class="font-bold text-slate-400 block uppercase">Reklam Seti:</span>
                        <span class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ $lead->adset_name ?: '-' }}</span>
                    </div>
                    <div>
                        <span class="font-bold text-slate-400 block uppercase">Reklam Adı:</span>
                        <span class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ $lead->ad_name ?: '-' }}</span>
                    </div>
                    <div>
                        <span class="font-bold text-slate-400 block uppercase">Platform / Kaynak:</span>
                        <span class="text-sm font-bold uppercase text-indigo-600 dark:text-indigo-400">{{ $lead->platform ?: 'Meta Ads' }}</span>
                    </div>
                </div>
            </div>

            <!-- History Timeline -->
            <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-md rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800/80 p-6">
                <h3 class="text-base font-extrabold text-slate-900 dark:text-slate-100 mb-4 pb-3 border-b border-slate-100 dark:border-slate-800">
                    🕒 İşlem Geçmişi (Audit Trail)
                </h3>
                <div class="space-y-4">
                    @forelse($lead->histories as $history)
                        <div class="flex items-start space-x-3 text-xs">
                            <div class="w-2 h-2 rounded-full bg-indigo-500 mt-1.5 shrink-0"></div>
                            <div class="flex-1 bg-slate-50 dark:bg-slate-800/60 p-3 rounded-xl border border-slate-200/60 dark:border-slate-700/60">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-slate-900 dark:text-slate-100">{{ $history->islem }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $history->created_at->format('d.m.Y H:i') }}</span>
                                </div>
                                @if($history->eski_deger || $history->yeni_deger)
                                    <p class="text-slate-500 dark:text-slate-400 mt-1">
                                        <span class="line-through text-red-500">{{ $history->eski_deger }}</span> ➔ <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $history->yeni_deger }}</span>
                                    </p>
                                @endif
                                <p class="text-[10px] text-slate-400 mt-1 font-medium">İşlemi Yapan: {{ $history->user->isim ?? 'Sistem' }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-4">Henüz işlem geçmişi yok.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Column: Quick Action Controls -->
        <div class="space-y-6">
            
            <!-- Change Status Form -->
            <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-md rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800/80 p-6">
                <h3 class="text-sm font-extrabold text-slate-900 dark:text-slate-100 mb-4 pb-2 border-b border-slate-100 dark:border-slate-800">
                    ⚡ Durum Güncelle
                </h3>
                <form action="{{ route('leads.update', $lead->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Yeni Durum</label>
                        <select name="status_id" class="w-full px-3 py-2 text-xs border border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold">
                            @foreach($statuses as $st)
                                <option value="{{ $st->id }}" {{ $lead->status_id == $st->id ? 'selected' : '' }}>{{ $st->isim }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold rounded-xl text-xs shadow-md transition-colors">
                        Durumu Kaydet
                    </button>
                </form>
            </div>

            <!-- Assign Operator Form -->
            @if(auth()->user()->isAdmin())
            <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-md rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800/80 p-6">
                <h3 class="text-sm font-extrabold text-slate-900 dark:text-slate-100 mb-4 pb-2 border-b border-slate-100 dark:border-slate-800">
                    👤 Operatör Atama
                </h3>
                <form action="{{ route('leads.assign-operator', $lead->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Operatör Seç</label>
                        <select name="operator_id" class="w-full px-3 py-2 text-xs border border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold">
                            <option value="">Operatör Seçin</option>
                            @foreach($operators as $op)
                                <option value="{{ $op->id }}" {{ $lead->atanan_operator_id == $op->id ? 'selected' : '' }}>{{ $op->isim }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Atama Nedeni</label>
                        <input type="text" name="sebep" placeholder="Örn: Müşteri takibi için atandı" class="w-full px-3 py-2 text-xs border border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none font-medium" required>
                    </div>
                    <button type="submit" class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 dark:bg-indigo-600 text-white font-extrabold rounded-xl text-xs shadow-md transition-colors">
                        Operatörü Ata
                    </button>
                </form>
            </div>
            @endif

            <!-- Add Operator Note Form -->
            <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-md rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800/80 p-6">
                <h3 class="text-sm font-extrabold text-slate-900 dark:text-slate-100 mb-4 pb-2 border-b border-slate-100 dark:border-slate-800">
                    📝 Operatör Notu Ekle
                </h3>
                <form action="{{ route('leads.add-note', $lead->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Operatör Notu</label>
                        <textarea name="note" rows="4" placeholder="Müşteri görüşmesi detayları..." class="w-full px-3 py-2 text-xs border border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none font-medium">{{ $lead->operator_notu }}</textarea>
                    </div>
                    <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl text-xs shadow-md transition-colors">
                        Notu Kaydet
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
