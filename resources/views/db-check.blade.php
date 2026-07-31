<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veritabanı Durum Kontrolü - Siber Güvenlik CRM</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 font-sans antialiased text-gray-900 min-h-screen p-6">
    <div class="max-w-4xl mx-auto space-y-6">
        
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Veritabanı Bağlantı & Durum Kontrolü</h1>
                <p class="text-sm text-gray-500 mt-1">Siber Güvenlik Başvuru Yönetim Sistem Bağlantı Analizörü</p>
            </div>
            <a href="{{ route('login') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium text-sm transition-colors">
                Giriş Ekranına Git →
            </a>
        </div>

        <!-- Connection Status Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center space-x-4">
                @if($status['connected'])
                    <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-emerald-600">Veritabanı Bağlantısı Başarılı! ✅</h2>
                        <p class="text-sm text-gray-600 mt-0.5">Uygulama veritabanına sorunsuz şekilde erişebiliyor.</p>
                    </div>
                @else
                    <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-red-600">Veritabanı Bağlantısı Başarısız! ❌</h2>
                        <p class="text-sm text-gray-600 mt-0.5">Lütfen .env veya Vercel Environment Variables ayarlarınızı kontrol edin.</p>
                    </div>
                @endif
            </div>

            <!-- Error Details if Any -->
            @if($status['error'])
            <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm font-mono overflow-x-auto">
                <strong>Hata Detayı:</strong> {{ $status['error'] }}
            </div>
            @endif

            <!-- Config Details -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6 pt-6 border-t border-gray-100">
                <div class="bg-slate-50 p-4 rounded-xl">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Sürücü (Driver)</span>
                    <span class="text-lg font-bold text-gray-800 mt-1 block">{{ strtoupper($status['driver']) }}</span>
                </div>
                <div class="bg-slate-50 p-4 rounded-xl">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Veritabanı Adı</span>
                    <span class="text-lg font-bold text-gray-800 mt-1 block">{{ $status['database'] ?: '(Tanımsız)' }}</span>
                </div>
                <div class="bg-slate-50 p-4 rounded-xl">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Sunucu Host</span>
                    <span class="text-lg font-bold text-gray-800 mt-1 block">{{ $status['host'] ?: '(Tanımsız)' }}</span>
                </div>
            </div>
        </div>

        @if($status['connected'])
        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 text-center">
                <span class="text-sm font-medium text-gray-500">Süper Admin Hesabı</span>
                <span class="text-3xl font-extrabold text-indigo-600 block mt-2">{{ $status['stats']['admin_count'] ?? 0 }}</span>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 text-center">
                <span class="text-sm font-medium text-gray-500">Operatör Hesapları</span>
                <span class="text-3xl font-extrabold text-blue-600 block mt-2">{{ $status['stats']['operator_count'] ?? 0 }}</span>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 text-center">
                <span class="text-sm font-medium text-gray-500">Kayıtlı Lead Sayısı</span>
                <span class="text-3xl font-extrabold text-emerald-600 block mt-2">{{ $status['stats']['lead_count'] ?? 0 }}</span>
            </div>
        </div>

        <!-- Table Check Matrix -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="font-bold text-gray-800">Tablo Yapısı & Kayıt Sayıları</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-gray-500">
                        <tr>
                            <th class="px-6 py-3">Tablo Adı</th>
                            <th class="px-6 py-3">Açıklama</th>
                            <th class="px-6 py-3 text-center">Durum</th>
                            <th class="px-6 py-3 text-right">Kayıt Sayısı</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($status['tables'] as $table)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 font-mono font-bold text-gray-800">{{ $table['name'] }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $table['label'] }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($table['exists'])
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                        Mevcut
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                        Eksik
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-gray-900">
                                {{ number_format($table['count']) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>
</body>
</html>
