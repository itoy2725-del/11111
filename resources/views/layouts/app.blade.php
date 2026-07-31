<!DOCTYPE html>
<html lang="tr" x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }" x-init="$watch('darkMode', val => localStorage.setItem('theme', val ? 'dark' : 'light'))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CRM') - Siber CRM Executive</title>
    <!-- Google Fonts: Inter & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, sans-serif; }
    </style>
</head>
<body class="font-sans antialiased text-slate-900 bg-slate-100/90 dark:bg-slate-950 dark:text-slate-100 transition-colors duration-200" x-data>

    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        <aside 
            class="flex flex-col bg-slate-900 dark:bg-slate-950 text-slate-200 border-r border-slate-800/80 transition-all duration-300 ease-in-out z-20 shrink-0 shadow-xl"
            :class="$store.sidebar.collapsed ? 'w-16' : 'w-64'"
        >
            <!-- Sidebar Header -->
            <div class="flex items-center justify-between h-16 px-4 border-b border-slate-800/80 bg-slate-950/40">
                <div class="flex items-center space-x-3 overflow-hidden">
                    <div class="w-9 h-9 rounded-2xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-violet-500 text-white font-black flex items-center justify-center text-base shadow-lg shadow-indigo-500/25 shrink-0 ring-2 ring-indigo-400/20">
                        🛡️
                    </div>
                    <div class="flex flex-col transition-opacity" :class="$store.sidebar.collapsed ? 'opacity-0 w-0' : 'opacity-100'">
                        <span class="font-black text-sm text-white tracking-wider whitespace-nowrap leading-none">
                            SİBER CRM
                        </span>
                        <span class="text-[9px] text-indigo-400 font-bold uppercase tracking-widest mt-1 flex items-center space-x-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>PRO CONSOLE</span>
                        </span>
                    </div>
                </div>
                <button @click="$store.sidebar.toggle()" class="p-1.5 rounded-xl hover:bg-slate-800/80 text-slate-400 hover:text-white transition-colors focus:outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto custom-scrollbar">
                @php
                    $role = auth()->user()->rol ?? 'operator'; 
                @endphp
                
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('dashboard') }}" class="flex items-center px-3.5 py-2.5 text-xs font-bold rounded-2xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' }}" title="Dashboard">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        <span class="ml-3 transition-opacity" :class="$store.sidebar.collapsed ? 'hidden' : 'block'">Dashboard</span>
                    </a>
                    <a href="{{ route('leads.index') }}" class="flex items-center px-3.5 py-2.5 text-xs font-bold rounded-2xl transition-all duration-200 {{ request()->routeIs('leads.*') ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' }}" title="Leadler">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span class="ml-3 transition-opacity" :class="$store.sidebar.collapsed ? 'hidden' : 'block'">Leadler</span>
                    </a>
                    <a href="{{ route('import.index') }}" class="flex items-center px-3.5 py-2.5 text-xs font-bold rounded-2xl transition-all duration-200 {{ request()->routeIs('import.*') ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' }}" title="CSV İçe Aktarma">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        <span class="ml-3 transition-opacity" :class="$store.sidebar.collapsed ? 'hidden' : 'block'">İçe Aktar</span>
                    </a>
                    <a href="{{ route('tasks.index') }}" class="flex items-center px-3.5 py-2.5 text-xs font-bold rounded-2xl transition-all duration-200 {{ request()->routeIs('tasks.*') ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' }}" title="Görevler">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span class="ml-3 transition-opacity" :class="$store.sidebar.collapsed ? 'hidden' : 'block'">Görevler</span>
                    </a>
                    <a href="{{ route('operators.index') }}" class="flex items-center px-3.5 py-2.5 text-xs font-bold rounded-2xl transition-all duration-200 {{ request()->routeIs('operators.*') ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' }}" title="Operatörler">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span class="ml-3 transition-opacity" :class="$store.sidebar.collapsed ? 'hidden' : 'block'">Operatörler</span>
                    </a>
                    <a href="{{ route('reports.index') }}" class="flex items-center px-3.5 py-2.5 text-xs font-bold rounded-2xl transition-all duration-200 {{ request()->routeIs('reports.*') ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' }}" title="Raporlar">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                        <span class="ml-3 transition-opacity" :class="$store.sidebar.collapsed ? 'hidden' : 'block'">Raporlar</span>
                    </a>
                    <a href="{{ route('audit-logs.index') }}" class="flex items-center px-3.5 py-2.5 text-xs font-bold rounded-2xl transition-all duration-200 {{ request()->routeIs('audit-logs.*') ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' }}" title="Audit Log">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span class="ml-3 transition-opacity" :class="$store.sidebar.collapsed ? 'hidden' : 'block'">Audit Log</span>
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="flex items-center px-3.5 py-2.5 text-xs font-bold rounded-2xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' }}" title="Bugün Yapılacaklar">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="ml-3 transition-opacity" :class="$store.sidebar.collapsed ? 'hidden' : 'block'">Bugün</span>
                    </a>
                    <a href="{{ route('leads.index') }}" class="flex items-center px-3.5 py-2.5 text-xs font-bold rounded-2xl transition-all duration-200 {{ request()->routeIs('leads.*') ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' }}" title="Leadlerim">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span class="ml-3 transition-opacity" :class="$store.sidebar.collapsed ? 'hidden' : 'block'">Leadlerim</span>
                    </a>
                    <a href="{{ route('tasks.index') }}" class="flex items-center px-3.5 py-2.5 text-xs font-bold rounded-2xl transition-all duration-200 {{ request()->routeIs('tasks.*') ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' }}" title="Görevlerim">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span class="ml-3 transition-opacity" :class="$store.sidebar.collapsed ? 'hidden' : 'block'">Görevlerim</span>
                    </a>
                @endif
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-100/90 dark:bg-slate-950">
            <!-- Glassmorphism Header -->
            <header class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800/80 h-16 flex items-center justify-between px-6 z-10 shrink-0 shadow-sm transition-colors duration-200">
                <h1 class="text-base font-extrabold text-slate-800 dark:text-slate-100 tracking-tight flex items-center space-x-2">
                    <span>@yield('title')</span>
                </h1>
                
                <div class="flex items-center space-x-4">
                    <!-- Day / Night Dark Mode Switcher -->
                    <button 
                        @click="darkMode = !darkMode" 
                        class="px-3 py-1.5 rounded-2xl bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/80 text-slate-700 dark:text-amber-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all focus:outline-none flex items-center space-x-2 text-xs font-extrabold shadow-sm"
                        title="Gece / Gündüz Modu Değiştir"
                    >
                        <span x-show="!darkMode" class="flex items-center space-x-1.5">
                            <span>🌙 Koyu Tema</span>
                        </span>
                        <span x-show="darkMode" class="flex items-center space-x-1.5 text-amber-400" style="display: none;">
                            <span>☀️ Açık Tema</span>
                        </span>
                    </button>

                    <div class="h-4 w-px bg-slate-200 dark:bg-slate-800"></div>

                    <!-- User Profile & Logout -->
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-2xl bg-gradient-to-tr from-indigo-600 to-violet-600 text-white font-extrabold text-xs flex items-center justify-center shadow-md shadow-indigo-600/20">
                            {{ mb_substr(auth()->user()->isim ?? 'K', 0, 1) }}
                        </div>
                        <div class="hidden sm:block">
                            <span class="text-xs font-black text-slate-800 dark:text-slate-100 block leading-none">{{ auth()->user()->isim ?? 'Kullanıcı' }}</span>
                            <span class="text-[10px] text-slate-500 dark:text-slate-400 block uppercase font-bold tracking-wider mt-0.5">
                                {{ auth()->user()->isAdmin() ? 'Sistem Yöneticisi' : 'Operatör' }}
                            </span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="px-2.5 py-1.5 rounded-xl text-slate-500 dark:text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors text-xs font-bold" title="Çıkış Yap">
                                🚪 Çıkış
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="flex-1 overflow-auto p-5 custom-scrollbar">
                @yield('content')
            </div>
            
            <footer class="bg-white/60 dark:bg-slate-900/60 backdrop-blur-sm border-t border-slate-200/80 dark:border-slate-800/80 px-6 py-3 text-center text-xs font-bold text-slate-400 shrink-0 flex items-center justify-between">
                <span>&copy; {{ date('Y') }} Siber CRM Pro. Tüm hakları saklıdır.</span>
                <span class="text-[10px] text-emerald-500 font-mono flex items-center space-x-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>SİSTEM AKTİF</span>
                </span>
            </footer>
        </main>
    </div>

    <!-- Global Components -->
    @include('components.toast')
    @include('components.modal')

</body>
</html>
