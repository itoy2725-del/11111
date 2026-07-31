@extends('layouts.app')

@section('title', 'İçe Aktarma Sonucu')

@section('content')
<div class="max-w-3xl mx-auto mt-8">
    <div class="card p-8 text-center bg-white shadow-lg border-t-8 border-green-500">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 mb-6">
            <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">İçe Aktarma Tamamlandı!</h2>
        <p class="text-gray-600 mb-8">CSV dosyasındaki veriler başarıyla işlendi.</p>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-500">İşlenen</p>
                <p class="text-xl font-bold text-gray-900">150</p>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <p class="text-sm text-green-600">Eklenen</p>
                <p class="text-xl font-bold text-green-700">142</p>
            </div>
            <div class="bg-yellow-50 p-4 rounded-lg">
                <p class="text-sm text-yellow-600">Güncellenen</p>
                <p class="text-xl font-bold text-yellow-700">8</p>
            </div>
            <div class="bg-red-50 p-4 rounded-lg">
                <p class="text-sm text-red-600">Hatalı</p>
                <p class="text-xl font-bold text-red-700">0</p>
            </div>
        </div>
        
        <div class="flex flex-col sm:flex-row justify-center space-y-3 sm:space-y-0 sm:space-x-4">
            <a href="{{ route('imports.index') }}" class="btn btn-secondary">Yeni Dosya Yükle</a>
            <a href="{{ route('leads.index') }}" class="btn btn-primary">Leadleri Görüntüle</a>
        </div>
    </div>
</div>
@endsection
