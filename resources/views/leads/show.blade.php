@extends('layouts.app')

@section('title', 'Lead Detayı #' . $lead->id)

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm">
                <li>
                    <a href="{{ route('leads.index') }}" class="text-gray-500 hover:text-indigo-600 font-medium">Leadler</a>
                </li>
                <li>
                    <span class="text-gray-400 mx-2">/</span>
                    <span class="text-gray-900 font-bold">#{{ $lead->id }} - {{ $lead->ad_soyad ?? 'İsimsiz Lead' }}</span>
                </li>
            </ol>
        </nav>
        <a href="{{ route('leads.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition-colors">
            ← Listeye Dön
        </a>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Left 2 Columns: Information -->
        <div class="xl:col-span-2 space-y-6">
            
            <!-- Main Lead Info Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <div class="flex justify-between items-start mb-6">
                    <div class="flex items-center space-x-4">
                        <div class="w-14 h-14 rounded-2xl bg-indigo-600 text-white font-extrabold text-xl flex items-center justify-center shadow-md">
                            {{ mb_substr($lead->ad_soyad ?? 'M', 0, 1) }}
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-gray-900">{{ $lead->ad_soyad ?? 'İsimsiz Lead' }}</h2>
                            <p class="text-xs text-gray-500 mt-1">Oluşturulma: {{ $lead->created_at ? $lead->created_at->format('d F Y H:i') : '-' }}</p>
                        </div>
                    </div>
                    <div>
                        <span class="px-3 py-1.5 rounded-full text-xs font-extrabold border shadow-sm" style="background-color: {{ $lead->status->renk ?? '#e2e8f0' }}20; color: {{ $lead->status->renk ?? '#475569' }}; border-color: {{ $lead->status->renk ?? '#cbd5e1' }}">
                            {{ $lead->status->isim ?? 'Yeni' }}
                        </span>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-gray-100">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Telefon Numarası</p>
                        <a href="tel:{{ $lead->telefon }}" class="text-base font-bold text-indigo-600 hover:underline block mt-1">
                            📞 {{ $lead->telefon }}
                        </a>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">E-posta Adresi</p>
                        <p class="text-base font-semibold text-gray-900 mt-1">{{ $lead->email ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Atanan Operatör</p>
                        <p class="text-base font-semibold text-gray-900 mt-1">
                            {{ $lead->operator ? $lead->operator->isim : 'Atanmamış' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form Answers & Cyber Security Details Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-base font-bold text-gray-900 mb-4 pb-3 border-b border-gray-100 flex items-center space-x-2">
                    <span>📋 Başvuru Formu & Siber Güvenlik Yanıtları</span>
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div class="bg-slate-50 dark:bg-slate-800 p-4 rounded-xl border border-gray-100 dark:border-slate-700">
                        <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Dolandırıcılık Türü</p>
                        <p class="text-base font-bold text-red-600 dark:text-red-400 mt-1">
                            {{ \App\Services\ImportService::cleanMetaText($lead->fraudType->isim ?? $lead->sikayet_durumu ?? '-') }}
                        </p>
                    </div>
                    
                    <div class="bg-slate-50 dark:bg-slate-800 p-4 rounded-xl border border-gray-100 dark:border-slate-700">
                        <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Kayıp Miktarı</p>
                        <p class="text-base font-bold text-gray-900 dark:text-slate-100 mt-1">
                            {{ \App\Services\ImportService::formatCryptoAmount($lead->lossRange->isim ?? '-') }}
                        </p>
                    </div>
                    
                    <div class="bg-slate-50 dark:bg-slate-800 p-4 rounded-xl border border-gray-100 dark:border-slate-700">
                        <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Cüzdan Türü</p>
                        <p class="text-base font-bold text-gray-900 dark:text-slate-100 mt-1">
                            {{ \App\Services\ImportService::cleanMetaText($lead->walletType->isim ?? '-') }}
                        </p>
                    </div>
                    
                    <div class="bg-slate-50 dark:bg-slate-800 p-4 rounded-xl border border-gray-100 dark:border-slate-700">
                        <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Polise / İhbar Şikayet</p>
                        <p class="text-base font-bold text-gray-900 dark:text-slate-100 mt-1">
                            {{ \App\Services\ImportService::cleanMetaText($lead->sikayet_durumu ?: '-') }}
                        </p>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-800 p-4 rounded-xl border border-gray-100 dark:border-slate-700">
                        <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Ek Güvenlik Hizmeti</p>
                        <p class="text-base font-bold text-gray-900 dark:text-slate-100 mt-1">
                            {{ \App\Services\ImportService::cleanMetaText($lead->ek_guvenlik_hizmeti ?: '-') }}
                        </p>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-800 p-4 rounded-xl border border-gray-100 dark:border-slate-700">
                        <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Mevcut Toplam Kripto</p>
                        <p class="text-base font-bold text-gray-900 dark:text-slate-100 mt-1">
                            {{ \App\Services\ImportService::formatCryptoAmount($lead->toplam_kripto ?: '-') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Meta Ads Information Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-base font-bold text-gray-900 mb-4 pb-3 border-b border-gray-100 flex items-center space-x-2">
                    <span>🎯 Meta Reklam Detayları</span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div>
                        <p class="text-gray-400 font-semibold">Kampanya Adı</p>
                        <p class="font-bold text-gray-800 mt-0.5">{{ $lead->campaign_name ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 font-semibold">Reklam Seti</p>
                        <p class="font-bold text-gray-800 mt-0.5">{{ $lead->adset_name ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 font-semibold">Reklam Adı</p>
                        <p class="font-bold text-gray-800 mt-0.5">{{ $lead->ad_name ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 font-semibold">Platform</p>
                        <p class="font-bold text-indigo-600 mt-0.5 uppercase">{{ $lead->platform ?: 'FB' }}</p>
                    </div>
                </div>
            </div>

            <!-- Operator Notes Section -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-base font-bold text-gray-900 mb-4 pb-3 border-b border-gray-100">📝 Operatör Notu</h3>
                
                @if($lead->operator_notu)
                    <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-xl mb-4 text-sm text-gray-800">
                        <p class="font-bold text-amber-900 text-xs mb-1">Mevcut Not:</p>
                        <p class="whitespace-pre-wrap">{{ $lead->operator_notu }}</p>
                    </div>
                @endif

                <form action="{{ route('leads.add-note', $lead->id) }}" method="POST" class="space-y-3">
                    @csrf
                    <textarea name="note" rows="3" placeholder="Lead hakkında notunuzu yazın..." class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
                    <div class="flex justify-between items-center">
                        <span class="text-[11px] text-gray-400">* Operatörler notu 15 dakika içinde güncelleyebilir.</span>
                        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-sm transition-colors">Notu Kaydet</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Column: Operator Assign & Timeline -->
        <div class="space-y-6">
            
            <!-- Actions Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-4">
                <h3 class="text-base font-bold text-gray-900 pb-3 border-b border-gray-100">⚡ Hızlı İşlemler</h3>

                <!-- Status Update Form -->
                <form action="{{ route('leads.update', $lead->id) }}" method="POST" class="space-y-2">
                    @csrf
                    @method('PUT')
                    <label class="block text-xs font-bold text-gray-700">Durum Güncelle</label>
                    <div class="flex space-x-2">
                        <select name="status_id" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl bg-white focus:ring-2 focus:ring-indigo-500">
                            @foreach($statuses as $st)
                                <option value="{{ $st->id }}" {{ $lead->status_id == $st->id ? 'selected' : '' }}>{{ $st->isim }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-3 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition-colors">Kaydet</button>
                    </div>
                </form>

                <!-- Assign Operator Form -->
                <form action="{{ route('leads.assign-operator', $lead->id) }}" method="POST" class="space-y-2 pt-3 border-t border-gray-100">
                    @csrf
                    <label class="block text-xs font-bold text-gray-700">Operatör Ata</label>
                    <select name="operator_id" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl bg-white focus:ring-2 focus:ring-indigo-500">
                        <option value="">Operatör Seçin</option>
                        @foreach($operators as $op)
                            <option value="{{ $op->id }}" {{ $lead->atanan_operator_id == $op->id ? 'selected' : '' }}>{{ $op->isim }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="sebep" placeholder="Atama Sebebi..." required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl outline-none" />
                    <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-colors shadow-sm">Operatörü Ata</button>
                </form>

                <!-- Delete Lead -->
                @if(auth()->user()->isAdmin())
                <form action="{{ route('leads.destroy', $lead->id) }}" method="POST" onsubmit="return confirm('Bu leadi silmek istediğinizden emin misiniz?');" class="pt-3 border-t border-gray-100">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full py-2 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-bold rounded-xl transition-colors">Leadi Sil</button>
                </form>
                @endif
            </div>

            <!-- Timeline Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-base font-bold text-gray-900 mb-4 pb-3 border-b border-gray-100">📜 İşlem Geçmişi (Timeline)</h3>
                
                <div class="space-y-4">
                    @forelse($lead->histories as $history)
                        <div class="relative pl-6 border-l-2 border-indigo-200 text-xs space-y-1">
                            <span class="absolute -left-1.5 top-1 w-3 h-3 rounded-full bg-indigo-600"></span>
                            <p class="font-bold text-gray-800">{{ $history->islem }}</p>
                            @if($history->eski_deger || $history->yeni_deger)
                                <p class="text-gray-500 text-[11px]">{{ $history->eski_deger }} → {{ $history->yeni_deger }}</p>
                            @endif
                            <p class="text-[10px] text-gray-400">
                                {{ $history->user ? $history->user->isim : 'Sistem' }} • {{ $history->created_at ? $history->created_at->format('d.m.Y H:i') : '' }}
                            </p>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 text-center">İşlem geçmişi bulunmuyor.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
