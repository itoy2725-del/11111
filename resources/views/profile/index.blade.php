@extends('layouts.app')

@section('title', 'Profilim')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="card overflow-hidden">
        <div class="bg-indigo-700 px-6 py-8 text-center text-white">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-white text-indigo-700 text-3xl font-bold mb-4">
                {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
            </div>
            <h2 class="text-2xl font-bold">{{ auth()->user()->name ?? 'Kullanıcı Adı' }}</h2>
            <p class="text-indigo-200">{{ auth()->user()->email ?? 'email@example.com' }}</p>
            <p class="mt-2 inline-flex items-center rounded-full bg-indigo-800 px-3 py-0.5 text-sm font-medium">
                {{ (auth()->user()->role ?? '') === 'super_admin' ? 'Super Admin' : 'Operatör' }}
            </p>
        </div>
    </div>

    <div class="card p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b border-gray-100 pb-2">Şifre Değiştir</h3>
        <form class="space-y-4 max-w-md">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mevcut Şifre</label>
                <input type="password" name="current_password" class="input-field" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Yeni Şifre</label>
                <input type="password" name="password" class="input-field" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Yeni Şifre (Tekrar)</label>
                <input type="password" name="password_confirmation" class="input-field" required>
            </div>
            <div class="pt-2">
                <button type="submit" class="btn btn-primary w-full sm:w-auto">Şifreyi Güncelle</button>
            </div>
        </form>
    </div>
</div>
@endsection
