@extends('layouts.app')

@section('title', 'İçe Aktarma')

@section('content')
<div class="space-y-6">
    
    <!-- Flash Messages -->
    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl text-red-700 flex items-center justify-between shadow-sm">
            <div class="flex items-center space-x-3">
                <svg class="w-6 h-6 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-xl text-emerald-700 flex items-center justify-between shadow-sm">
            <div class="flex items-center space-x-3">
                <svg class="w-6 h-6 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Upload Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Meta Lead CSV Yükle</h2>
        
        <form action="{{ route('import.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4" x-data="{ fileName: '', fileSize: '' }">
            @csrf
            <div class="flex items-center justify-center w-full">
                <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-64 border-2 border-indigo-300 border-dashed rounded-xl cursor-pointer bg-indigo-50/50 hover:bg-indigo-50 transition-colors p-6 text-center">
                    
                    <template x-if="!fileName">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-12 h-12 mb-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <p class="mb-2 text-sm text-gray-700 font-medium"><span class="font-bold text-indigo-600">Yüklemek için tıklayın</span> veya CSV dosyanızı buraya sürükleyin</p>
                            <p class="text-xs text-gray-500">Meta Ads Lead Exporter CSV (.csv)</p>
                        </div>
                    </template>

                    <template x-if="fileName">
                        <div class="flex flex-col items-center justify-center text-emerald-700">
                            <svg class="w-12 h-12 mb-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="font-bold text-base" x-text="fileName"></p>
                            <p class="text-xs text-gray-500 mt-1" x-text="fileSize"></p>
                            <span class="mt-3 text-xs bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full font-semibold">Dosya Hazır — Değiştirmek için tıklayın</span>
                        </div>
                    </template>

                    <input id="dropzone-file" type="file" name="csv_file" class="hidden" accept=".csv" required 
                           @change="
                                if ($event.target.files.length > 0) {
                                    fileName = $event.target.files[0].name;
                                    fileSize = ($event.target.files[0].size / 1024).toFixed(1) + ' KB';
                                }
                           " />
                </label>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold text-sm shadow-md transition-colors flex items-center space-x-2">
                    <span>Önizle ve Yükle →</span>
                </button>
            </div>
        </form>
    </div>

    <!-- History Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-slate-50">
            <h3 class="font-bold text-gray-800">Geçmiş Aktarımlar</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="text-xs text-gray-500 uppercase bg-slate-50 font-semibold border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3">Tarih</th>
                        <th class="px-6 py-3">Dosya Adı</th>
                        <th class="px-6 py-3">Toplam</th>
                        <th class="px-6 py-3">Başarılı</th>
                        <th class="px-6 py-3">Mükerrer</th>
                        <th class="px-6 py-3">Hatalı</th>
                        <th class="px-6 py-3">Yükleyen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($imports as $import)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">{{ $import->created_at->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $import->dosya_adi }}</td>
                            <td class="px-6 py-4 font-bold text-gray-800">{{ $import->toplam_kayit }}</td>
                            <td class="px-6 py-4 text-emerald-600 font-bold">{{ $import->basarili }}</td>
                            <td class="px-6 py-4 text-amber-600 font-bold">{{ $import->mukerrer }}</td>
                            <td class="px-6 py-4 text-red-600 font-bold">{{ $import->hata_sayisi }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $import->user->isim ?? 'Sistem' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">Henüz yapılmış bir aktarım bulunmuyor.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($imports->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $imports->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
