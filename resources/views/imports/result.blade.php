@extends('layouts.app')

@section('title', 'İçe Aktarma Sonucu')

@section('content')
<div class="max-w-3xl mx-auto mt-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 text-center border-t-8 border-emerald-500">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-emerald-100 mb-6">
            <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">İçe Aktarma Başarıyla Tamamlandı! 🎉</h2>
        <p class="text-gray-600 mb-8"><span class="font-bold">{{ $import->dosya_adi }}</span> dosyasındaki kayıtlar veritabanına işlendi.</p>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-slate-50 p-4 rounded-xl">
                <p class="text-xs font-semibold text-gray-500 uppercase">Toplam Satır</p>
                <p class="text-2xl font-extrabold text-gray-900 mt-1">{{ number_format($import->toplam_kayit) }}</p>
            </div>
            <div class="bg-emerald-50 p-4 rounded-xl">
                <p class="text-xs font-semibold text-emerald-600 uppercase">Başarılı Kayıt</p>
                <p class="text-2xl font-extrabold text-emerald-700 mt-1">{{ number_format($import->basarili) }}</p>
            </div>
            <div class="bg-amber-50 p-4 rounded-xl">
                <p class="text-xs font-semibold text-amber-600 uppercase">Mükerrer</p>
                <p class="text-2xl font-extrabold text-amber-700 mt-1">{{ number_format($import->mukerrer) }}</p>
            </div>
            <div class="bg-red-50 p-4 rounded-xl">
                <p class="text-xs font-semibold text-red-600 uppercase">Hatalı Satır</p>
                <p class="text-2xl font-extrabold text-red-700 mt-1">{{ number_format($import->hata_sayisi) }}</p>
            </div>
        </div>
        
        <div class="flex flex-col sm:flex-row justify-center space-y-3 sm:space-y-0 sm:space-x-4">
            <a href="{{ route('import.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm rounded-xl transition-colors">
                ← Yeni Dosya Yükle
            </a>
            <a href="{{ route('leads.index') }}" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-md transition-colors">
                Yüklenen Leadleri Gör →
            </a>
        </div>
    </div>
</div>
@endsection
