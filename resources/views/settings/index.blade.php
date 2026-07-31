@extends('layouts.app')

@section('title', 'Ayarlar')

@section('content')
<div class="space-y-6" x-data="{ tab: 'status' }">
    
    <!-- Tabs Navigation -->
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button @click="tab = 'status'" :class="tab === 'status' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'" class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium">
                Lead Durumları
            </button>
            <button @click="tab = 'fraud'" :class="tab === 'fraud' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'" class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium">
                Dolandırıcılık Türleri
            </button>
            <button @click="tab = 'loss'" :class="tab === 'loss' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'" class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium">
                Kayıp Aralıkları
            </button>
            <button @click="tab = 'wallet'" :class="tab === 'wallet' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'" class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium">
                Cüzdan Türleri
            </button>
        </nav>
    </div>

    <!-- Tab Contents -->
    <div class="card p-6 min-h-[400px]">
        
        <!-- Status Tab -->
        <div x-show="tab === 'status'" class="space-y-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Lead Durumları</h3>
                <button class="btn btn-primary text-sm py-1.5">+ Yeni Ekle</button>
            </div>
            <div class="bg-white border border-gray-200 rounded-md shadow-sm">
                <ul role="list" class="divide-y divide-gray-200">
                    <li class="flex items-center justify-between py-4 pl-4 pr-5 text-sm leading-6">
                        <div class="flex w-0 flex-1 items-center">
                            <div class="w-4 h-4 rounded-full bg-blue-500 mr-4"></div>
                            <span class="truncate font-medium text-gray-900">Yeni Kayıt</span>
                        </div>
                        <div class="ml-4 flex-shrink-0 space-x-2">
                            <button class="font-medium text-indigo-600 hover:text-indigo-500">Düzenle</button>
                        </div>
                    </li>
                    <li class="flex items-center justify-between py-4 pl-4 pr-5 text-sm leading-6">
                        <div class="flex w-0 flex-1 items-center">
                            <div class="w-4 h-4 rounded-full bg-yellow-500 mr-4"></div>
                            <span class="truncate font-medium text-gray-900">Aranacak</span>
                        </div>
                        <div class="ml-4 flex-shrink-0 space-x-2">
                            <button class="font-medium text-indigo-600 hover:text-indigo-500">Düzenle</button>
                        </div>
                    </li>
                    <li class="flex items-center justify-between py-4 pl-4 pr-5 text-sm leading-6">
                        <div class="flex w-0 flex-1 items-center">
                            <div class="w-4 h-4 rounded-full bg-green-500 mr-4"></div>
                            <span class="truncate font-medium text-gray-900">Dönüştü</span>
                        </div>
                        <div class="ml-4 flex-shrink-0 space-x-2">
                            <button class="font-medium text-indigo-600 hover:text-indigo-500">Düzenle</button>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Fraud Tab -->
        <div x-show="tab === 'fraud'" style="display: none;" class="space-y-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Dolandırıcılık Türleri</h3>
                <button class="btn btn-primary text-sm py-1.5">+ Yeni Ekle</button>
            </div>
            <div class="bg-white border border-gray-200 rounded-md shadow-sm">
                <ul role="list" class="divide-y divide-gray-200">
                    <li class="flex items-center justify-between py-4 pl-4 pr-5 text-sm leading-6">
                        <span class="truncate font-medium text-gray-900">Kripto Yatırım Dolandırıcılığı</span>
                        <button class="font-medium text-indigo-600 hover:text-indigo-500">Düzenle</button>
                    </li>
                    <li class="flex items-center justify-between py-4 pl-4 pr-5 text-sm leading-6">
                        <span class="truncate font-medium text-gray-900">Forex Kayıpları</span>
                        <button class="font-medium text-indigo-600 hover:text-indigo-500">Düzenle</button>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Other Tabs similarly -->
        <div x-show="tab === 'loss'" style="display: none;" class="text-gray-500 text-center py-10">Kayıp Aralıkları yönetimi...</div>
        <div x-show="tab === 'wallet'" style="display: none;" class="text-gray-500 text-center py-10">Cüzdan Türleri yönetimi...</div>

    </div>
</div>
@endsection
