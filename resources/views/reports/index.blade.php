@extends('layouts.app')

@section('title', 'Raporlar')

@section('content')
<div class="space-y-6">
    <!-- Date Filter -->
    <div class="card p-4 bg-white flex justify-between items-center">
        <h2 class="font-semibold text-gray-800">Performans Raporları</h2>
        <form class="flex space-x-2">
            <input type="date" class="input-field py-1.5 text-sm">
            <span class="self-center text-gray-500">-</span>
            <input type="date" class="input-field py-1.5 text-sm">
            <button class="btn btn-secondary py-1.5 text-sm">Uygula</button>
        </form>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="card p-6 text-center">
            <p class="text-sm font-medium text-gray-500 mb-1">Toplam Lead</p>
            <p class="text-3xl font-bold text-gray-900">3,450</p>
        </div>
        <div class="card p-6 text-center">
            <p class="text-sm font-medium text-gray-500 mb-1">Dönüşüm Oranı</p>
            <p class="text-3xl font-bold text-indigo-600">%12.4</p>
        </div>
        <div class="card p-6 text-center">
            <p class="text-sm font-medium text-gray-500 mb-1">Kayıp Lead</p>
            <p class="text-3xl font-bold text-red-600">450</p>
        </div>
        <div class="card p-6 text-center">
            <p class="text-sm font-medium text-gray-500 mb-1">Ortalama Yanıt Süresi</p>
            <p class="text-3xl font-bold text-gray-900">2s 15d</p>
        </div>
    </div>

    <!-- Chart via CSS -->
    <div class="card p-6">
        <h3 class="font-semibold text-gray-800 mb-6">Lead Durum Dağılımı</h3>
        <div class="space-y-4">
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-medium">Yeni Kayıt</span>
                    <span>%40 (1380)</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: 40%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-medium">Ulaşıldı</span>
                    <span>%35 (1207)</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-yellow-400 h-2.5 rounded-full" style="width: 35%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-medium">Dönüştü</span>
                    <span>%15 (517)</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-green-500 h-2.5 rounded-full" style="width: 15%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-medium">Kayıp</span>
                    <span>%10 (346)</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-red-500 h-2.5 rounded-full" style="width: 10%"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
