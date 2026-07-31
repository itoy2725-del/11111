@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    @if(auth()->user()->isAdmin())
        <!-- Admin Dashboard Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center space-x-4 hover:shadow-md transition-shadow">
                <div class="p-3 bg-indigo-100 rounded-lg">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Toplam Lead</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_leads']) }}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center space-x-4 hover:shadow-md transition-shadow">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Bugünkü Leadler</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['today_leads']) }}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center space-x-4 hover:shadow-md transition-shadow">
                <div class="p-3 bg-yellow-100 rounded-lg">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Bekleyen Görevler</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['pending_tasks']) }}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center space-x-4 hover:shadow-md transition-shadow">
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Aktif Operatörler</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['active_operators'] }}</p>
                </div>
            </div>
        </div>

        <!-- Status Distribution -->
        @if($stats['status_distribution']->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Durum Dağılımı</h3>
            <div class="space-y-3">
                @php $maxCount = $stats['status_distribution']->max('count') ?: 1; @endphp
                @foreach($stats['status_distribution'] as $status)
                <div class="flex items-center space-x-3">
                    <span class="w-32 text-sm text-gray-600 truncate">{{ $status->isim }}</span>
                    <div class="flex-1 bg-gray-100 rounded-full h-6 relative overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500 flex items-center justify-end pr-2"
                             style="width: {{ ($status->count / $maxCount) * 100 }}%; background-color: {{ $status->renk }};">
                            <span class="text-xs font-bold text-white drop-shadow">{{ $status->count }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Operator Performance -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Operatör Performansı</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                            <tr>
                                <th class="px-6 py-3">İsim</th>
                                <th class="px-6 py-3 text-center">Toplam</th>
                                <th class="px-6 py-3 text-center">Tamamlanan</th>
                                <th class="px-6 py-3 text-center">Bekleyen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stats['operator_performance'] as $op)
                            <tr class="border-b hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $op['isim'] }}</td>
                                <td class="px-6 py-4 text-center">{{ $op['total_leads'] }}</td>
                                <td class="px-6 py-4 text-center text-green-600 font-semibold">{{ $op['completed'] }}</td>
                                <td class="px-6 py-4 text-center text-yellow-600 font-semibold">{{ $op['pending'] }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Henüz operatör bulunmuyor.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Campaign Performance -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Kampanya Performansı</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                            <tr>
                                <th class="px-6 py-3">Kampanya</th>
                                <th class="px-6 py-3 text-center">Lead Sayısı</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stats['campaign_stats'] as $camp)
                            <tr class="border-b hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $camp->campaign_name }}</td>
                                <td class="px-6 py-4 text-center">{{ $camp->count }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="px-6 py-4 text-center text-gray-500">Henüz kampanya verisi yok.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Leads -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">Son Eklenen Leadler</h3>
                <a href="{{ route('leads.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Tümünü Gör →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">ID</th>
                            <th class="px-6 py-3">Telefon</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3">Durum</th>
                            <th class="px-6 py-3">Operatör</th>
                            <th class="px-6 py-3">Tarih</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stats['recent_leads'] as $lead)
                        <tr class="border-b hover:bg-gray-50 transition-colors cursor-pointer" onclick="window.location='{{ route('leads.show', $lead->id) }}'">
                            <td class="px-6 py-4 font-medium text-gray-900">#{{ $lead->id }}</td>
                            <td class="px-6 py-4">{{ $lead->telefon }}</td>
                            <td class="px-6 py-4">{{ $lead->email }}</td>
                            <td class="px-6 py-4">
                                @if($lead->status)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white" style="background-color: {{ $lead->status->renk }}">
                                    {{ $lead->status->isim }}
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">{{ $lead->operator->isim ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ $lead->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Henüz lead bulunmuyor.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
    @else
        <!-- Operator Dashboard -->
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 rounded-xl shadow-lg p-6 text-white relative overflow-hidden">
            <div class="relative z-10">
                <h2 class="text-2xl font-bold mb-2">Hoş geldin, {{ auth()->user()->isim }}!</h2>
                <p class="text-indigo-100">
                    Bugün ilgilenmen gereken <span class="font-bold text-white">{{ $todayTasks->count() }}</span> görev ve
                    toplam <span class="font-bold text-white">{{ $myLeadsCount }}</span> atanmış lead bulunuyor.
                </p>
            </div>
            <svg class="absolute right-0 bottom-0 opacity-10 h-32 w-32 transform translate-x-8 translate-y-8" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>
        </div>

        <!-- Operator Stat Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center space-x-4">
                <div class="p-3 bg-indigo-100 rounded-lg">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Toplam Leadlerim</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $myLeadsCount }}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center space-x-4">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Bugün Gelen</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $myTodayLeadsCount }}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center space-x-4">
                <div class="p-3 bg-yellow-100 rounded-lg">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Bekleyen Görevler</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $myPendingTasks }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Today's Tasks -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-800">Bugünkü Görevlerim</h3>
                    <a href="{{ route('tasks.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Tümünü Gör →</a>
                </div>
                <div class="p-4 space-y-3">
                    @forelse($todayTasks as $task)
                    <div class="p-4 bg-gray-50 border border-gray-100 rounded-lg flex justify-between items-center hover:bg-gray-100 transition-colors">
                        <div>
                            <p class="font-medium text-gray-900">{{ $task->baslik }}</p>
                            <p class="text-sm text-gray-500">{{ $task->lead ? ($task->lead->ad_soyad ?? $task->lead->telefon) : 'Genel görev' }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-semibold px-2 py-1 rounded-full {{ $task->tarih->isPast() ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $task->tarih->format('H:i') }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6 text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Bugün için planlanmış göreviniz yok.
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Leads -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-800">Son Atanan Leadler</h3>
                    <a href="{{ route('leads.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Leadlerime Git →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <tbody>
                            @forelse($recentLeads as $lead)
                            <tr class="border-b hover:bg-gray-50 transition-colors cursor-pointer" onclick="window.location='{{ route('leads.show', $lead->id) }}'">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $lead->ad_soyad ?? $lead->telefon }}</td>
                                <td class="px-6 py-4">{{ $lead->telefon }}</td>
                                <td class="px-6 py-4">
                                    @if($lead->status)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white" style="background-color: {{ $lead->status->renk }}">
                                        {{ $lead->status->isim }}
                                    </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="px-6 py-6 text-center text-gray-500">Son atanan lead bulunmuyor.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
