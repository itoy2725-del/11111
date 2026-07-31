<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Siber Güvenlik CRM') - CRM</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-slate-50" x-data>

    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        <aside 
            class="flex flex-col bg-indigo-900 text-white transition-all duration-300 ease-in-out z-20"
            :class="$store.sidebar.collapsed ? 'w-16' : 'w-64'"
        >
            <div class="flex items-center justify-between h-16 px-4 border-b border-indigo-800">
                <span class="font-bold text-lg whitespace-nowrap overflow-hidden transition-opacity" :class="$store.sidebar.collapsed ? 'opacity-0 w-0' : 'opacity-100'">
                    Siber CRM
                </span>
                <button @click="$store.sidebar.toggle()" class="p-1 rounded-md hover:bg-indigo-800 focus:outline-none">
                    <svg class="w-5 h-5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
            
            <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto custom-scrollbar">
                @php
                    $role = auth()->user()->role ?? 'operator'; 
                @endphp
                
                @if($role === 'super_admin')
                    <a href="{{ route('dashboard') }}" class="flex items-center px-2 py-2 text-sm font-medium rounded-md hover:bg-indigo-800 {{ request()->routeIs('dashboard') ? 'bg-indigo-800' : '' }}" title="Dashboard">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        <span class="ml-3 transition-opacity" :class="$store.sidebar.collapsed ? 'hidden' : 'block'">Dashboard</span>
                    </a>
                    <a href="{{ route('leads.index') }}" class="flex items-center px-2 py-2 text-sm font-medium rounded-md hover:bg-indigo-800 {{ request()->routeIs('leads.*') ? 'bg-indigo-800' : '' }}" title="Leadler">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span class="ml-3 transition-opacity" :class="$store.sidebar.collapsed ? 'hidden' : 'block'">Leadler</span>
                    </a>
                    <a href="{{ route('imports.index') }}" class="flex items-center px-2 py-2 text-sm font-medium rounded-md hover:bg-indigo-800 {{ request()->routeIs('imports.*') ? 'bg-indigo-800' : '' }}" title="CSV İçe Aktarma">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        <span class="ml-3 transition-opacity" :class="$store.sidebar.collapsed ? 'hidden' : 'block'">İçe Aktar</span>
                    </a>
                    <a href="{{ route('tasks.index') }}" class="flex items-center px-2 py-2 text-sm font-medium rounded-md hover:bg-indigo-800 {{ request()->routeIs('tasks.*') ? 'bg-indigo-800' : '' }}" title="Görevler">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span class="ml-3 transition-opacity" :class="$store.sidebar.collapsed ? 'hidden' : 'block'">Görevler</span>
                    </a>
                    <a href="{{ route('operators.index') }}" class="flex items-center px-2 py-2 text-sm font-medium rounded-md hover:bg-indigo-800 {{ request()->routeIs('operators.*') ? 'bg-indigo-800' : '' }}" title="Operatörler">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span class="ml-3 transition-opacity" :class="$store.sidebar.collapsed ? 'hidden' : 'block'">Operatörler</span>
                    </a>
                    <a href="{{ route('reports.index') }}" class="flex items-center px-2 py-2 text-sm font-medium rounded-md hover:bg-indigo-800 {{ request()->routeIs('reports.*') ? 'bg-indigo-800' : '' }}" title="Raporlar">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                        <span class="ml-3 transition-opacity" :class="$store.sidebar.collapsed ? 'hidden' : 'block'">Raporlar</span>
                    </a>
                    <a href="{{ route('audit.index') }}" class="flex items-center px-2 py-2 text-sm font-medium rounded-md hover:bg-indigo-800 {{ request()->routeIs('audit.*') ? 'bg-indigo-800' : '' }}" title="Audit Log">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span class="ml-3 transition-opacity" :class="$store.sidebar.collapsed ? 'hidden' : 'block'">Audit Log</span>
                    </a>
                    <a href="{{ route('settings.index') }}" class="flex items-center px-2 py-2 text-sm font-medium rounded-md hover:bg-indigo-800 {{ request()->routeIs('settings.*') ? 'bg-indigo-800' : '' }}" title="Ayarlar">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="ml-3 transition-opacity" :class="$store.sidebar.collapsed ? 'hidden' : 'block'">Ayarlar</span>
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="flex items-center px-2 py-2 text-sm font-medium rounded-md hover:bg-indigo-800 {{ request()->routeIs('dashboard') ? 'bg-indigo-800' : '' }}" title="Bugün Yapılacaklar">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="ml-3 transition-opacity" :class="$store.sidebar.collapsed ? 'hidden' : 'block'">Bugün</span>
                    </a>
                    <a href="{{ route('leads.index') }}" class="flex items-center px-2 py-2 text-sm font-medium rounded-md hover:bg-indigo-800 {{ request()->routeIs('leads.*') ? 'bg-indigo-800' : '' }}" title="Leadlerim">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span class="ml-3 transition-opacity" :class="$store.sidebar.collapsed ? 'hidden' : 'block'">Leadlerim</span>
                    </a>
                    <a href="{{ route('tasks.index') }}" class="flex items-center px-2 py-2 text-sm font-medium rounded-md hover:bg-indigo-800 {{ request()->routeIs('tasks.*') ? 'bg-indigo-800' : '' }}" title="Görevlerim">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span class="ml-3 transition-opacity" :class="$store.sidebar.collapsed ? 'hidden' : 'block'">Görevlerim</span>
                    </a>
                    <a href="{{ route('profile.index') }}" class="flex items-center px-2 py-2 text-sm font-medium rounded-md hover:bg-indigo-800 {{ request()->routeIs('profile.*') ? 'bg-indigo-800' : '' }}" title="Profil">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span class="ml-3 transition-opacity" :class="$store.sidebar.collapsed ? 'hidden' : 'block'">Profil</span>
                    </a>
                @endif
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50">
            <!-- Header -->
            <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6 z-10 shrink-0">
                <h1 class="text-xl font-semibold text-gray-800">@yield('title')</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600 font-medium">{{ auth()->user()->name ?? 'Kullanıcı' }}</span>
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $role === 'super_admin' ? 'bg-indigo-100 text-indigo-800' : 'bg-green-100 text-green-800' }}">
                        {{ $role === 'super_admin' ? 'Admin' : 'Operatör' }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 ml-4">
                            Çıkış Yap
                        </button>
                    </form>
                </div>
            </header>

            <!-- Page Content -->
            <div class="flex-1 overflow-auto p-6 custom-scrollbar">
                @yield('content')
            </div>
            
            <footer class="bg-white border-t border-gray-200 p-4 text-center text-sm text-gray-500 shrink-0">
                &copy; {{ date('Y') }} Siber Güvenlik CRM. Tüm hakları saklıdır.
            </footer>
        </main>
    </div>

    <!-- Global Components -->
    @include('components.toast')
    @include('components.modal')
    @include('leads.partials.drawer')

</body>
</html>
