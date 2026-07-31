@extends('layouts.app')

@section('title', 'İçe Aktarma Önizleme')

@section('content')
<div class="space-y-6">
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-md">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700 font-medium">Bu bir önizlemedir. Kayıtlar henüz sisteme eklenmedi.</p>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="card p-4 text-center border-t-4 border-indigo-500">
            <p class="text-xs font-medium text-gray-500 uppercase">Toplam Kayıt</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">150</p>
        </div>
        <div class="card p-4 text-center border-t-4 border-green-500">
            <p class="text-xs font-medium text-gray-500 uppercase">Yeni</p>
            <p class="text-2xl font-bold text-green-600 mt-1">142</p>
        </div>
        <div class="card p-4 text-center border-t-4 border-yellow-500">
            <p class="text-xs font-medium text-gray-500 uppercase">Mükerrer</p>
            <p class="text-2xl font-bold text-yellow-600 mt-1">8</p>
        </div>
        <div class="card p-4 text-center border-t-4 border-red-500">
            <p class="text-xs font-medium text-gray-500 uppercase">Hatalı</p>
            <p class="text-2xl font-bold text-red-600 mt-1">0</p>
        </div>
    </div>

    <!-- Duplicate Action Form -->
    <div class="card p-6">
        <form action="{{ route('imports.process') ?? '#' }}" method="POST">
            @csrf
            <h3 class="font-semibold text-gray-800 mb-4">Mükerrer Kayıt İşlemi</h3>
            <div class="flex flex-col sm:flex-row gap-4 mb-6">
                <label class="flex items-center space-x-2">
                    <input type="radio" name="duplicate_action" value="skip" class="text-indigo-600 focus:ring-indigo-500" checked>
                    <span class="text-sm text-gray-700">Atla (Kaydetme)</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input type="radio" name="duplicate_action" value="update" class="text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-gray-700">Mevcut Kaydı Güncelle</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input type="radio" name="duplicate_action" value="create" class="text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-gray-700">Yeni Kayıt Olarak Ekle</span>
                </label>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                <a href="{{ route('imports.index') }}" class="btn btn-secondary">İptal</a>
                <button type="submit" class="btn btn-primary">İçe Aktarmayı Başlat</button>
            </div>
        </form>
    </div>

    <!-- Preview Table -->
    <div class="card overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="font-semibold text-gray-800">Örnek Veri (İlk 5 Satır)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                    <tr>
                        <th class="px-6 py-3">İsim Soyisim</th>
                        <th class="px-6 py-3">Telefon</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Kampanya</th>
                        <th class="px-6 py-3">Durum</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-white border-b">
                        <td class="px-6 py-4 font-medium text-gray-900">Ali Veli</td>
                        <td class="px-6 py-4">+90 555 111 2233</td>
                        <td class="px-6 py-4">ali@example.com</td>
                        <td class="px-6 py-4">TR_Kampanya</td>
                        <td class="px-6 py-4"><span class="text-green-600 font-semibold text-xs">Yeni</span></td>
                    </tr>
                    <tr class="bg-yellow-50 border-b">
                        <td class="px-6 py-4 font-medium text-gray-900">Ayşe Yılmaz</td>
                        <td class="px-6 py-4">+90 555 999 8877</td>
                        <td class="px-6 py-4">ayse@example.com</td>
                        <td class="px-6 py-4">TR_Kampanya</td>
                        <td class="px-6 py-4"><span class="text-yellow-600 font-semibold text-xs">Mükerrer</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
