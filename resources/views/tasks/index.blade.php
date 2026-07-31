@extends('layouts.app')

@section('title', 'Görevler')

@section('content')
<div class="space-y-4">
    <!-- Header/Filter Area -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0 bg-white p-4 rounded-lg shadow-sm border border-gray-100">
        <form class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <select class="input-field py-1.5 w-auto text-sm">
                <option value="">Tüm Durumlar</option>
                <option value="pending">Bekliyor</option>
                <option value="completed">Tamamlandı</option>
            </select>
            @if(auth()->user()->role === 'super_admin')
            <select class="input-field py-1.5 w-auto text-sm">
                <option value="">Tüm Operatörler</option>
            </select>
            @endif
            <input type="date" class="input-field py-1.5 w-auto text-sm">
            <button type="submit" class="btn btn-secondary py-1.5">Filtrele</button>
        </form>
        <button class="btn btn-primary py-1.5" @click="$store.modal.openModal({title: 'Yeni Görev', content: 'Görev oluşturma formu buraya gelecek.'})">
            + Yeni Görev
        </button>
    </div>

    <!-- Tasks List -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        <!-- Task Card Overdue -->
        <div class="card p-4 border-l-4 border-red-500 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-2">
                <h3 class="font-bold text-gray-900 text-lg">Müşteri Aranacak</h3>
                <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10">Gecikti</span>
            </div>
            <p class="text-sm text-indigo-600 font-medium mb-2">Lead: Ahmet Yılmaz</p>
            <p class="text-sm text-gray-600 mb-4 line-clamp-2">Tekrar arama talep etti, detaylı teklif sunulacak.</p>
            <div class="flex justify-between items-center text-xs text-gray-500 pt-3 border-t border-gray-100">
                <span class="flex items-center text-red-600 font-semibold"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Dün 15:00</span>
                <span class="flex items-center"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg> Mehmet</span>
            </div>
            <div class="mt-4 flex space-x-2">
                <button class="btn btn-secondary w-full py-1.5 text-xs text-green-700 hover:bg-green-50 border-green-200">Tamamla</button>
            </div>
        </div>

        <!-- Task Card Pending -->
        <div class="card p-4 border-l-4 border-yellow-400 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-2">
                <h3 class="font-bold text-gray-900 text-lg">Email Gönderilecek</h3>
                <span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">Bekliyor</span>
            </div>
            <p class="text-sm text-indigo-600 font-medium mb-2">Lead: Ayşe Demir</p>
            <p class="text-sm text-gray-600 mb-4 line-clamp-2">Sunum dosyası iletilecek.</p>
            <div class="flex justify-between items-center text-xs text-gray-500 pt-3 border-t border-gray-100">
                <span class="flex items-center text-gray-900 font-medium"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Bugün 16:30</span>
                <span class="flex items-center"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg> Ali</span>
            </div>
            <div class="mt-4 flex space-x-2">
                <button class="btn btn-secondary w-full py-1.5 text-xs text-green-700 hover:bg-green-50 border-green-200">Tamamla</button>
            </div>
        </div>
    </div>
</div>
@endsection
