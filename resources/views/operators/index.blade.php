@extends('layouts.app')

@section('title', 'Operatörler')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center bg-white p-4 rounded-lg shadow-sm border border-gray-100">
        <h2 class="font-semibold text-gray-800">Sistem Operatörleri</h2>
        <button class="btn btn-primary text-sm py-1.5" @click="$store.modal.openModal({title: 'Yeni Operatör', content: 'Operatör ekleme formu...'})">+ Yeni Operatör</button>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3">İsim</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Rol</th>
                        <th class="px-6 py-3">Durum</th>
                        <th class="px-6 py-3">Aktif Lead</th>
                        <th class="px-6 py-3 text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">Ahmet Admin</td>
                        <td class="px-6 py-4">admin@example.com</td>
                        <td class="px-6 py-4">Super Admin</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Aktif</span>
                        </td>
                        <td class="px-6 py-4">-</td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="#" class="text-indigo-600 hover:text-indigo-900 font-medium">Düzenle</a>
                        </td>
                    </tr>
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">Mehmet Operatör</td>
                        <td class="px-6 py-4">mehmet@example.com</td>
                        <td class="px-6 py-4">Operatör</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Aktif</span>
                        </td>
                        <td class="px-6 py-4">42</td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="#" class="text-indigo-600 hover:text-indigo-900 font-medium">İncele</a>
                            <a href="#" class="text-red-600 hover:text-red-900 font-medium">Pasife Al</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
