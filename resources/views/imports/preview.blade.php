@extends('layouts.app')

@section('title', 'İçe Aktarma Önizleme')

@section('content')
<div class="space-y-6">
    <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-xl text-amber-800 flex items-center space-x-3 shadow-sm">
        <svg class="w-6 h-6 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <div>
            <p class="font-bold text-sm">Bu bir önizleme ekranıdır.</p>
            <p class="text-xs text-amber-700 mt-0.5"><span class="font-bold">{{ $originalName }}</span> dosyasındaki kayıtlar henüz veritabanına eklenmedi. Lütfen aşağıdaki mükerrer kayıt tercihini yapıp aktarımı başlatın.</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 text-center border-t-4 border-indigo-500">
            <p class="text-xs font-semibold text-gray-500 uppercase">Toplam Kayıt</p>
            <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ number_format($totalCount) }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 text-center border-t-4 border-emerald-500">
            <p class="text-xs font-semibold text-gray-500 uppercase">Yeni Kayıt</p>
            <p class="text-3xl font-extrabold text-emerald-600 mt-1">{{ number_format($newCount) }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 text-center border-t-4 border-amber-500">
            <p class="text-xs font-semibold text-gray-500 uppercase">Mükerrer</p>
            <p class="text-3xl font-extrabold text-amber-600 mt-1">{{ number_format($duplicateCount) }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 text-center border-t-4 border-red-500">
            <p class="text-xs font-semibold text-gray-500 uppercase">Hatalı Satır</p>
            <p class="text-3xl font-extrabold text-red-600 mt-1">{{ number_format($errorCount) }}</p>
        </div>
    </div>

    <!-- Duplicate Action Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <form action="{{ route('import.process') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="raw_payload" value="{{ $rawPayload }}" />

            <h3 class="font-bold text-gray-800 text-lg">Mükerrer Kayıt İşlem Tercihi</h3>
            <p class="text-sm text-gray-500">Sistemde daha önceden kayıtlı aynı telefon numarasına sahip mükerrer veriler için ne yapılacağını seçin:</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                <label class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-slate-50 cursor-pointer transition-colors">
                    <input type="radio" name="duplicate_action" value="skip" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-gray-300" checked />
                    <div class="ml-3">
                        <span class="block text-sm font-bold text-gray-900">Atla (Mükerrerleri Ekleme)</span>
                        <span class="block text-xs text-gray-500 mt-0.5">Sadece yeni telefon numaralarını kaydeder.</span>
                    </div>
                </label>
                
                <label class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-slate-50 cursor-pointer transition-colors">
                    <input type="radio" name="duplicate_action" value="update" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-gray-300" />
                    <div class="ml-3">
                        <span class="block text-sm font-bold text-gray-900">Mevcut Kaydı Güncelle</span>
                        <span class="block text-xs text-gray-500 mt-0.5">Var olan kaydın bilgilerini yeni CSV ile tazeler.</span>
                    </div>
                </label>
                
                <label class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-slate-50 cursor-pointer transition-colors">
                    <input type="radio" name="duplicate_action" value="create_new" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-gray-300" />
                    <div class="ml-3">
                        <span class="block text-sm font-bold text-gray-900">Yeni Kayıt Olarak Ekle</span>
                        <span class="block text-xs text-gray-500 mt-0.5">Mükerrer olsa da ayrı bir lead olarak kaydeder.</span>
                    </div>
                </label>
            </div>
            
            <div class="flex justify-between items-center pt-6 border-t border-gray-100 mt-4">
                <a href="{{ route('import.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm rounded-xl transition-colors">
                    ← İptal Et & Geri Dön
                </a>
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-sm shadow-md transition-colors flex items-center space-x-2">
                    <span>🚀 İçe Aktarmayı Başlat</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Full Lead Data Preview Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Lead Detay Verileri Önizleme (Tüm Kolonlar)</h3>
            <span class="text-xs bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full font-semibold">Gereksiz ID'ler Temizlendi</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-gray-600">
                <thead class="text-[11px] text-gray-500 uppercase bg-slate-100 font-bold border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 whitespace-nowrap">Ad Soyad</th>
                        <th class="px-4 py-3 whitespace-nowrap">Telefon</th>
                        <th class="px-4 py-3 whitespace-nowrap">E-posta</th>
                        <th class="px-4 py-3 whitespace-nowrap">Tarih / Platform</th>
                        <th class="px-4 py-3 whitespace-nowrap">Kampanya / Reklam</th>
                        <th class="px-4 py-3 whitespace-nowrap">Dolandırıcılık Türü</th>
                        <th class="px-4 py-3 whitespace-nowrap">Kayıp Miktarı</th>
                        <th class="px-4 py-3 whitespace-nowrap">Cüzdan Türü</th>
                        <th class="px-4 py-3 whitespace-nowrap">Polise Şikayet</th>
                        <th class="px-4 py-3 whitespace-nowrap">Ek Güvenlik</th>
                        <th class="px-4 py-3 whitespace-nowrap">Mevcut Kripto</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($previewRows as $row)
                        @php
                            $adSoyad = \App\Services\ImportService::cleanMetaText($row['ad_soyad'] ?? 'Meta Lead');
                            $phone = preg_replace('/^p:/i', '', trim($row['normalized_phone'] ?? '-'));
                            $email = $row['normalized_email'] ?? '-';
                            $adName = \App\Services\ImportService::cleanMetaText($row['clean_ad_name'] ?? 'New Leads Ad');
                            $campaignName = \App\Services\ImportService::cleanMetaText($row['clean_campaign_name'] ?? 'New Leads Campaign');
                            $platform = strtoupper($row['col_11'] ?? $row['platform'] ?? 'FB');
                            
                            $createdTimeVal = $row['col_1'] ?? $row['created_time'] ?? null;
                            $createdTime = '-';
                            if (!empty($createdTimeVal)) {
                                try {
                                    $createdTime = \Carbon\Carbon::parse($createdTimeVal)->format('d.m.Y H:i');
                                } catch (\Throwable $e) {
                                    $createdTime = '-';
                                }
                            }
                            
                            // Clean form answers using cleanMetaText
                            $fraud = \App\Services\ImportService::cleanMetaText($row['form_fraud'] ?? '-');
                            $loss = \App\Services\ImportService::cleanMetaText($row['form_loss'] ?? '-');
                            $wallet = \App\Services\ImportService::cleanMetaText($row['form_wallet'] ?? '-');
                            $complaint = \App\Services\ImportService::cleanMetaText($row['form_complaint'] ?? '-');
                            $security = \App\Services\ImportService::cleanMetaText($row['form_security'] ?? '-');
                            $crypto = \App\Services\ImportService::cleanMetaText($row['form_crypto'] ?? '-');
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap font-bold text-gray-900">
                                {{ $adSoyad }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap font-mono font-bold text-indigo-600">
                                {{ $phone }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                {{ $email }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-semibold text-gray-800 block">{{ $createdTime }}</span>
                                <span class="inline-block px-1.5 py-0.5 text-[10px] font-bold rounded bg-indigo-50 text-indigo-700 border border-indigo-100 mt-0.5">{{ $platform }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-semibold text-gray-800 block whitespace-nowrap">{{ $adName ?: 'New Leads Ad' }}</span>
                                <span class="text-gray-500 text-[11px] block whitespace-nowrap">{{ $campaignName ?: 'New Leads Campaign' }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-red-50 text-red-700 border border-red-100">
                                    {{ str_replace('_', ' ', $fraud) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap font-bold text-gray-800">
                                {{ str_replace('_', ' ', $loss) }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                {{ str_replace('_', ' ', $wallet) }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ strtolower($complaint) === 'evet' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst($complaint) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ strtolower($security) === 'evet' ? 'bg-blue-50 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst($security) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap font-medium text-gray-700">
                                {{ str_replace('_', ' ', $crypto) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-6 py-4 text-center text-gray-500">Önizleme satırı bulunmuyor.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
