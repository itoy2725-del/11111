@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    @if(auth()->user()->isAdmin())
        <!-- Admin Dashboard Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 flex items-center space-x-4 hover:shadow-md transition-all">
                <div class="p-3.5 bg-indigo-50 dark:bg-indigo-950/60 rounded-2xl">
                    <svg class="w-7 h-7 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Toplam Lead</p>
                    <p class="text-3xl font-black text-slate-900 dark:text-slate-100 mt-0.5">{{ number_format($stats['total_leads']) }}</p>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 flex items-center space-x-4 hover:shadow-md transition-all">
                <div class="p-3.5 bg-blue-50 dark:bg-blue-950/60 rounded-2xl">
                    <svg class="w-7 h-7 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Bugünkü Leadler</p>
                    <p class="text-3xl font-black text-slate-900 dark:text-slate-100 mt-0.5">{{ number_format($stats['today_leads']) }}</p>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 flex items-center space-x-4 hover:shadow-md transition-all">
                <div class="p-3.5 bg-amber-50 dark:bg-amber-950/60 rounded-2xl">
                    <svg class="w-7 h-7 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Bekleyen Görevler</p>
                    <p class="text-3xl font-black text-slate-900 dark:text-slate-100 mt-0.5">{{ number_format($stats['pending_tasks']) }}</p>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 flex items-center space-x-4 hover:shadow-md transition-all">
                <div class="p-3.5 bg-emerald-50 dark:bg-emerald-950/60 rounded-2xl">
                    <svg class="w-7 h-7 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aktif Operatörler</p>
                    <p class="text-3xl font-black text-slate-900 dark:text-slate-100 mt-0.5">{{ $stats['active_operators'] }}</p>
                </div>
            </div>
        </div>

        <!-- Status Distribution -->
        @if($stats['status_distribution']->count() > 0)
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
            <h3 class="font-bold text-slate-800 dark:text-slate-100 text-sm mb-4">Lead Durum Dağılımı</h3>
            <div class="space-y-3">
                @php $maxCount = $stats['status_distribution']->max('count') ?: 1; @endphp
                @foreach($stats['status_distribution'] as $status)
                <div class="flex items-center space-x-3">
                    <span class="w-36 text-xs font-semibold text-slate-600 dark:text-slate-300 truncate">{{ $status->isim }}</span>
                    <div class="flex-1 bg-slate-100 dark:bg-slate-800 rounded-full h-5 relative overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500 flex items-center justify-end pr-2"
                             style="width: {{ max(10, ($status->count / $maxCount) * 100) }}%; background-color: {{ $status->renk }};">
                            <span class="text-[10px] font-extrabold text-white drop-shadow">{{ $status->count }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Operator Performance -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950">
                    <h3 class="font-bold text-slate-800 dark:text-slate-100 text-sm">Operatör Performansı</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left text-slate-600 dark:text-slate-300">
                        <thead class="text-[11px] text-slate-500 dark:text-slate-400 uppercase bg-slate-100 dark:bg-slate-900 font-bold border-b border-slate-200 dark:border-slate-800">
                            <tr>
                                <th class="px-6 py-3">İsim</th>
                                <th class="px-6 py-3 text-center">Toplam</th>
                                <th class="px-6 py-3 text-center">Tamamlanan</th>
                                <th class="px-6 py-3 text-center">Bekleyen</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($stats['operator_performance'] as $op)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4 font-bold text-slate-900 dark:text-slate-100">{{ $op['isim'] }}</td>
                                <td class="px-6 py-4 text-center font-mono font-bold">{{ $op['total_leads'] }}</td>
                                <td class="px-6 py-4 text-center text-emerald-600 font-extrabold">{{ $op['completed'] }}</td>
                                <td class="px-6 py-4 text-center text-amber-600 font-extrabold">{{ $op['pending'] }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-6 py-4 text-center text-slate-400">Henüz operatör bulunmuyor.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Campaign Performance -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950">
                    <h3 class="font-bold text-slate-800 dark:text-slate-100 text-sm">Kampanya Performansı</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left text-slate-600 dark:text-slate-300">
                        <thead class="text-[11px] text-slate-500 dark:text-slate-400 uppercase bg-slate-100 dark:bg-slate-900 font-bold border-b border-slate-200 dark:border-slate-800">
                            <tr>
                                <th class="px-6 py-3">Kampanya</th>
                                <th class="px-6 py-3 text-center">Lead Sayısı</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($stats['campaign_stats'] as $camp)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4 font-bold text-slate-900 dark:text-slate-100">{{ $camp->campaign_name }}</td>
                                <td class="px-6 py-4 text-center font-mono font-extrabold text-indigo-600 dark:text-indigo-400">{{ $camp->count }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="px-6 py-4 text-center text-slate-400">Henüz kampanya verisi yok.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Leads -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 dark:text-slate-100 text-sm">Son Eklenen Leadler</h3>
                <a href="{{ route('leads.index') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline font-bold">Tümünü Gör →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left text-slate-600 dark:text-slate-300">
                    <thead class="text-[11px] text-slate-500 dark:text-slate-400 uppercase bg-slate-100 dark:bg-slate-900 font-bold border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="px-6 py-3">ID</th>
                            <th class="px-6 py-3">Telefon</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3">Durum</th>
                            <th class="px-6 py-3">Operatör</th>
                            <th class="px-6 py-3">Tarih</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($stats['recent_leads'] as $lead)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors cursor-pointer" onclick="window.location='{{ route('leads.show', $lead->id) }}'">
                            <td class="px-6 py-4 font-mono font-bold text-slate-900 dark:text-slate-100">#{{ $lead->id }}</td>
                            <td class="px-6 py-4 font-mono font-bold text-slate-900 dark:text-slate-100">{{ $lead->telefon }}</td>
                            <td class="px-6 py-4">{{ $lead->email ?: '-' }}</td>
                            <td class="px-6 py-4">
                                @if($lead->status)
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold border" style="background-color: {{ $lead->status->renk }}20; color: {{ $lead->status->renk }}; border-color: {{ $lead->status->renk }}">
                                    {{ $lead->status->isim }}
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-medium">{{ $lead->operator->isim ?? 'Atanmamış' }}</td>
                            <td class="px-6 py-4 text-slate-400 font-mono">{{ $lead->created_at ? $lead->created_at->format('d.m.Y H:i') : '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-6 py-4 text-center text-slate-400">Henüz lead bulunmuyor.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
    @else
        <!-- Operator Dashboard -->
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 rounded-2xl shadow-lg p-6 text-white relative overflow-hidden">
            <div class="relative z-10">
                <h2 class="text-2xl font-black mb-2">Hoş geldin, {{ auth()->user()->isim }}!</h2>
                <p class="text-indigo-100 text-sm">
                    Bugün ilgilenmen gereken <span class="font-black text-white underline">{{ $todayTasks->count() }}</span> görev ve
                    toplam <span class="font-black text-white underline">{{ $myLeadsCount }}</span> atanmış lead bulunuyor.
                </p>
            </div>
        </div>

        <!-- Operator Stat Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 flex items-center space-x-4">
                <div class="p-3.5 bg-indigo-50 dark:bg-indigo-950/60 rounded-2xl">
                    <svg class="w-7 h-7 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Toplam Leadlerim</p>
                    <p class="text-3xl font-black text-slate-900 dark:text-slate-100 mt-0.5">{{ $myLeadsCount }}</p>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 flex items-center space-x-4">
                <div class="p-3.5 bg-blue-50 dark:bg-blue-950/60 rounded-2xl">
                    <svg class="w-7 h-7 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Bugün Gelen</p>
                    <p class="text-3xl font-black text-slate-900 dark:text-slate-100 mt-0.5">{{ $myTodayLeadsCount }}</p>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 flex items-center space-x-4">
                <div class="p-3.5 bg-amber-50 dark:bg-amber-950/60 rounded-2xl">
                    <svg class="w-7 h-7 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Bekleyen Görevler</p>
                    <p class="text-3xl font-black text-slate-900 dark:text-slate-100 mt-0.5">{{ $myPendingTasks }}</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
