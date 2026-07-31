@extends('layouts.app')

@section('title', 'İçe Aktarma')

@section('content')
<div class="space-y-6">
    <!-- Upload Card -->
    <div class="card p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Meta Lead CSV Yükle</h2>
        
        <form action="{{ route('import.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="flex items-center justify-center w-full">
                <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-64 border-2 border-indigo-300 border-dashed rounded-lg cursor-pointer bg-indigo-50 hover:bg-indigo-100 transition-colors">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        <svg class="w-10 h-10 mb-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        <p class="mb-2 text-sm text-gray-700"><span class="font-semibold">Yüklemek için tıklayın</span> veya sürükleyip bırakın</p>
                        <p class="text-xs text-gray-500">Sadece CSV dosyaları</p>
                    </div>
                    <input id="dropzone-file" type="file" name="csv_file" class="hidden" accept=".csv" required />
                </label>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="btn btn-primary">Önizle ve Yükle</button>
            </div>
        </form>
    </div>

    <!-- History Card -->
    <div class="card overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="font-semibold text-gray-800">Geçmiş Aktarımlar</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100">
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
                <tbody>
                    @forelse($imports as $import)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-4">{{ $import->created_at->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $import->dosya_adi }}</td>
                            <td class="px-6 py-4">{{ $import->toplam_kayit }}</td>
                            <td class="px-6 py-4 text-green-600">{{ $import->basarili }}</td>
                            <td class="px-6 py-4 text-yellow-600">{{ $import->mukerrer }}</td>
                            <td class="px-6 py-4 text-red-600">{{ $import->hata_sayisi }}</td>
                            <td class="px-6 py-4">{{ $import->user->isim ?? 'Sistem' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">Henüz yapılmış bir aktarım bulunmuyor.</td>
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
