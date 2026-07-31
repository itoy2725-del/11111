@extends('layouts.app')

@section('title', 'Operatör Detayı')

@section('content')
<div class="mb-4">
    <a href="{{ route('operators.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">&larr; Operatörlere Dön</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Profile Info -->
    <div class="lg:col-span-1 space-y-6">
        <div class="card p-6 text-center">
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-indigo-100 text-indigo-700 text-3xl font-bold mb-4">
                MO
            </div>
            <h2 class="text-xl font-bold text-gray-900">Mehmet Operatör</h2>
            <p class="text-gray-500">mehmet@example.com</p>
            
            <div class="mt-4 flex justify-center">
                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Aktif</span>
                <span class="ml-2 inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">Operatör</span>
            </div>
            
            <div class="mt-6 border-t border-gray-100 pt-6">
                <button class="btn btn-secondary w-full">Şifre Sıfırla</button>
            </div>
        </div>
        
        <!-- Stats Mini -->
        <div class="card p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Performans Özeti</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Toplam Atanan</span>
                    <span class="font-bold text-gray-900">452</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Dönüştürülen</span>
                    <span class="font-bold text-green-600">84</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Kayıp</span>
                    <span class="font-bold text-red-600">120</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                    <div class="bg-indigo-600 h-1.5 rounded-full" style="width: 18%"></div>
                </div>
                <p class="text-xs text-gray-500 text-right">%18 Dönüşüm Oranı</p>
            </div>
        </div>
    </div>

    <!-- Right Side (Leads & Activity) -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Assigned Leads -->
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="font-semibold text-gray-800">Üzerindeki Leadler</h3>
                <a href="#" class="text-sm text-indigo-600 font-medium">Tümünü Gör</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-white">
                        <tr>
                            <th class="px-6 py-3">Lead İsim</th>
                            <th class="px-6 py-3">Durum</th>
                            <th class="px-6 py-3">Atanma Tarihi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="bg-white border-b">
                            <td class="px-6 py-4 font-medium text-gray-900">Canan Yıldız</td>
                            <td class="px-6 py-4">@include('components.status-badge', ['color' => 'yellow', 'text' => 'Aranacak'])</td>
                            <td class="px-6 py-4 text-xs">Bugün 14:00</td>
                        </tr>
                        <tr class="bg-white border-b">
                            <td class="px-6 py-4 font-medium text-gray-900">Burak Kaan</td>
                            <td class="px-6 py-4">@include('components.status-badge', ['color' => 'blue', 'text' => 'Ulaşıldı'])</td>
                            <td class="px-6 py-4 text-xs">Dün 16:30</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="font-semibold text-gray-800">Son Aktiviteler</h3>
            </div>
            <div class="p-6">
                @include('leads.partials.timeline')
            </div>
        </div>
    </div>
</div>
@endsection
