@extends('layouts.app')

@section('title', 'Leadler')

@section('content')
<div class="space-y-4">
    <!-- Filters -->
    <div class="card p-4">
        <form method="GET" action="{{ route('leads.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Arama</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="İsim, Telefon, Email" class="input-field py-1.5">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Durum</label>
                <select name="status" class="input-field py-1.5">
                    <option value="">Tümü</option>
                    <option value="new">Yeni</option>
                    <option value="contacted">Ulaşıldı</option>
                    <option value="converted">Dönüştü</option>
                </select>
            </div>
            @if(auth()->user()->role === 'super_admin')
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Operatör</label>
                <select name="operator" class="input-field py-1.5">
                    <option value="">Tümü</option>
                    <option value="unassigned">Atanmamış</option>
                    <!-- other options -->
                </select>
            </div>
            @endif
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Tarih</label>
                <input type="date" name="date" value="{{ request('date') }}" class="input-field py-1.5">
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="btn btn-primary py-1.5 w-full">Filtrele</button>
                <a href="{{ route('leads.index') }}" class="btn btn-secondary py-1.5 px-3" title="Temizle">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </a>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="card overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h2 class="font-semibold text-gray-800">Lead Listesi</h2>
            <div class="flex space-x-2">
                @if(auth()->user()->role === 'super_admin')
                <button type="button" class="btn btn-secondary py-1.5 text-xs font-semibold">Toplu Ata</button>
                @endif
                <button type="button" class="btn btn-secondary py-1.5 text-xs font-semibold">Dışa Aktar</button>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                    <tr>
                        <th class="px-6 py-3">ID</th>
                        <th class="px-6 py-3">İsim Soyisim</th>
                        <th class="px-6 py-3">Telefon</th>
                        <th class="px-6 py-3">Durum</th>
                        <th class="px-6 py-3">Operatör</th>
                        <th class="px-6 py-3">Kampanya</th>
                        <th class="px-6 py-3">Kayıt Tarihi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Mock Data -->
                    @for($i=1; $i<=10; $i++)
                    <tr class="bg-white border-b hover:bg-indigo-50 cursor-pointer transition-colors" @click="$store.drawer.openDrawer({{ $i }})">
                        <td class="px-6 py-4 font-medium text-gray-900">#100{{ $i }}</td>
                        <td class="px-6 py-4">Örnek Kişi {{ $i }}</td>
                        <td class="px-6 py-4">+90 555 123 456{{ $i % 10 }}</td>
                        <td class="px-6 py-4">
                            @include('components.status-badge', ['color' => ['blue', 'green', 'yellow', 'red'][$i % 4], 'text' => 'Durum ' . $i])
                        </td>
                        <td class="px-6 py-4">Operatör {{ $i % 3 + 1 }}</td>
                        <td class="px-6 py-4">Meta Kampanya</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ date('Y-m-d H:i', strtotime("-$i hours")) }}</td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t border-gray-100 bg-white">
            <!-- Mock Pagination, replace with actual $leads->links('components.pagination') -->
            <div class="text-sm text-gray-500">Sayfalandırma burada olacak.</div>
        </div>
    </div>
</div>
@endsection
