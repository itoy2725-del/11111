<div x-show="$store.drawer.open" 
     class="relative z-40" 
     aria-labelledby="slide-over-title" 
     role="dialog" 
     aria-modal="true"
     style="display: none;">
     
    <!-- Background backdrop -->
    <div x-show="$store.drawer.open"
         x-transition:enter="ease-in-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in-out duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm"
         @click="$store.drawer.closeDrawer()"></div>

    <div class="fixed inset-0 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16">
                
                <div x-show="$store.drawer.open"
                     x-transition:enter="transform transition ease-in-out duration-300 sm:duration-500"
                     x-transition:enter-start="translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transform transition ease-in-out duration-300 sm:duration-500"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="translate-x-full"
                     class="pointer-events-auto w-screen max-w-md">
                     
                    <div class="flex h-full flex-col overflow-y-scroll bg-white shadow-xl custom-scrollbar">
                        <div class="bg-indigo-700 px-4 py-6 sm:px-6">
                            <div class="flex items-center justify-between">
                                <h2 class="text-base font-semibold leading-6 text-white" id="slide-over-title">Lead Hızlı Bakış</h2>
                                <div class="ml-3 flex h-7 items-center">
                                    <button type="button" @click="$store.drawer.closeDrawer()" class="relative rounded-md bg-indigo-700 text-indigo-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white">
                                        <span class="absolute -inset-2.5"></span>
                                        <span class="sr-only">Kapat</span>
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="mt-1">
                                <p class="text-sm text-indigo-300">Detayları görmek için profil sayfasına gidin.</p>
                            </div>
                        </div>
                        
                        <div class="relative flex-1 px-4 py-6 sm:px-6">
                            <!-- Loading state -->
                            <div x-show="$store.drawer.loading" class="flex justify-center items-center h-32">
                                <svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                            
                            <!-- Content -->
                            <div x-show="!$store.drawer.loading && $store.drawer.leadData" class="space-y-6">
                                <div>
                                    <h3 class="font-bold text-lg text-gray-900" x-text="$store.drawer.leadData?.name || 'Yükleniyor...'"></h3>
                                    <p class="text-sm text-gray-500">ID: <span x-text="$store.drawer.leadId"></span></p>
                                </div>
                                
                                <dl class="divide-y divide-gray-100">
                                    <div class="px-0 py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                        <dt class="text-sm font-medium leading-6 text-gray-900">Telefon</dt>
                                        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0" x-text="$store.drawer.leadData?.phone || '+90 555 000 0000'"></dd>
                                    </div>
                                    <div class="px-0 py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                        <dt class="text-sm font-medium leading-6 text-gray-900">E-posta</dt>
                                        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0" x-text="$store.drawer.leadData?.email || 'email@example.com'"></dd>
                                    </div>
                                    <div class="px-0 py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                        <dt class="text-sm font-medium leading-6 text-gray-900">Durum</dt>
                                        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                            <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">Yeni Kayıt</span>
                                        </dd>
                                    </div>
                                    <div class="px-0 py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                        <dt class="text-sm font-medium leading-6 text-gray-900">Operatör</dt>
                                        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0" x-text="$store.drawer.leadData?.operator || 'Atanmamış'"></dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-200 px-4 py-4 sm:px-6 bg-gray-50 flex justify-end space-x-3">
                            <button type="button" @click="$store.drawer.closeDrawer()" class="btn btn-secondary">Kapat</button>
                            <a :href="`/leads/${$store.drawer.leadId}`" class="btn btn-primary">Tüm Detayları Gör</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
