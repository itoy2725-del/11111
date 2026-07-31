@extends('layouts.app')

@section('title', 'Lead Detayı #1001')

@section('content')
<div class="mb-4">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('leads.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">
                    Leadler
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">#1001</span>
                </div>
            </li>
        </ol>
    </nav>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <!-- Left Column: Details -->
    <div class="xl:col-span-2 space-y-6">
        
        <!-- Main Info -->
        <div class="card p-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Ahmet Yılmaz</h2>
                    <p class="text-sm text-gray-500 mt-1">Oluşturulma: 24 Ekim 2023 14:30</p>
                </div>
                <div>
                    @include('components.status-badge', ['color' => 'blue', 'text' => 'Yeni Kayıt'])
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <p class="text-sm font-medium text-gray-500">Telefon</p>
                    <p class="text-base font-semibold text-gray-900 mt-1">+90 532 123 4567</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">E-posta</p>
                    <p class="text-base font-semibold text-gray-900 mt-1">ahmet@example.com</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Atanan Operatör</p>
                    <p class="text-base font-semibold text-gray-900 mt-1">Mehmet Operatör</p>
                </div>
            </div>
        </div>

        <!-- Meta Info -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b border-gray-100 pb-2">Meta Reklam Bilgileri</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-500">Kampanya Adı</p>
                    <p class="text-sm text-gray-900 mt-1">TR_Genel_Ekim</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Reklam Seti</p>
                    <p class="text-sm text-gray-900 mt-1">Hedef_Kitle_1</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Reklam Adı</p>
                    <p class="text-sm text-gray-900 mt-1">Görsel_V2</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Platform</p>
                    <p class="text-sm text-gray-900 mt-1">Instagram</p>
                </div>
            </div>
        </div>

        <!-- Application Info -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b border-gray-100 pb-2">Başvuru Formu Bilgileri</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-500">Dolandırıcılık Türü</p>
                    <p class="text-sm text-gray-900 mt-1">Kripto Yatırım</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Kayıp Miktarı</p>
                    <p class="text-sm text-gray-900 mt-1">50.000 TL - 100.000 TL</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Cüzdan Türü</p>
                    <p class="text-sm text-gray-900 mt-1">Binance</p>
                </div>
            </div>
        </div>

        <!-- Notes -->
        @include('leads.partials.notes')

    </div>

    <!-- Right Column: Actions & Timeline -->
    <div class="space-y-6">
        
        <!-- Actions -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksiyonlar</h3>
            
            <div class="space-y-4">
                @if(auth()->user()->role === 'super_admin')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Durum Değiştir</label>
                    <select class="input-field">
                        <option>Yeni Kayıt</option>
                        <option>Aranacak</option>
                        <option>Ulaşıldı</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Operatör Ata</label>
                    <div class="flex space-x-2">
                        <select class="input-field flex-1">
                            <option>Mehmet Operatör</option>
                        </select>
                        <button class="btn btn-secondary">Ata</button>
                    </div>
                </div>
                @endif
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sonraki Arama Tarihi</label>
                    <input type="datetime-local" class="input-field">
                </div>
                
                <hr class="border-gray-100">
                
                <button class="btn btn-primary w-full justify-center">Görev Oluştur</button>
                @if(auth()->user()->role === 'super_admin')
                <button class="btn btn-danger w-full justify-center mt-2">Leadi Sil</button>
                @endif
            </div>
        </div>

        <!-- Timeline -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Zaman Çizelgesi</h3>
            @include('leads.partials.timeline')
        </div>

    </div>
</div>
@endsection
