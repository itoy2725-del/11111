@extends('layouts.app')

@section('title', 'Audit Log')

@section('content')
<div class="space-y-4">
    <!-- Filter -->
    <div class="card p-4">
        <form class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Kullanıcı</label>
                <select class="input-field py-1.5 text-sm">
                    <option value="">Tümü</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">İşlem Tipi</label>
                <select class="input-field py-1.5 text-sm">
                    <option value="">Tümü</option>
                    <option value="created">Oluşturma</option>
                    <option value="updated">Güncelleme</option>
                    <option value="deleted">Silme</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Tarih</label>
                <input type="date" class="input-field py-1.5 text-sm">
            </div>
            <div class="flex items-end">
                <button type="submit" class="btn btn-secondary py-1.5 w-full text-sm">Filtrele</button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3">Tarih</th>
                        <th class="px-6 py-3">Kullanıcı</th>
                        <th class="px-6 py-3">İşlem</th>
                        <th class="px-6 py-3">Modül</th>
                        <th class="px-6 py-3">Kayıt ID</th>
                        <th class="px-6 py-3">IP Adresi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">24 Eki 2023 15:30</td>
                        <td class="px-6 py-4 font-medium text-gray-900">Mehmet Operatör</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">updated</span>
                        </td>
                        <td class="px-6 py-4">Lead</td>
                        <td class="px-6 py-4">#1001</td>
                        <td class="px-6 py-4 font-mono text-xs text-gray-400">192.168.1.1</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
