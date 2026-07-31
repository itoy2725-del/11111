<div class="card p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b border-gray-100 pb-2">Notlar</h3>
    
    <!-- Add Note -->
    <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-100">
        <form>
            @csrf
            <div>
                <label class="sr-only">Not Ekle</label>
                <textarea rows="3" class="input-field block w-full resize-none border-0 py-1.5 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6 bg-white" placeholder="Lead hakkında bir not ekleyin..."></textarea>
            </div>
            <div class="mt-2 flex justify-between items-center">
                <p class="text-xs text-gray-500">Not eklendikten sonra ilk 15 dakika düzenlenebilir.</p>
                <button type="button" class="btn btn-primary text-xs py-1.5 px-3">Notu Kaydet</button>
            </div>
        </form>
    </div>
    
    <!-- Notes List -->
    <div class="space-y-4">
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
            <div class="flex justify-between items-start">
                <div class="flex items-center space-x-2">
                    <span class="font-semibold text-sm text-gray-900">Mehmet Operatör</span>
                    <span class="text-xs text-gray-500">&bull; 2 saat önce</span>
                </div>
            </div>
            <div class="mt-2 text-sm text-gray-700">
                <p>Müşteri ile görüşüldü, detaylı bilgi verildi. Yarın tekrar aranmasını rica etti.</p>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
            <div class="flex justify-between items-start">
                <div class="flex items-center space-x-2">
                    <span class="font-semibold text-sm text-gray-900">Admin</span>
                    <span class="text-xs text-gray-500">&bull; 1 gün önce</span>
                </div>
            </div>
            <div class="mt-2 text-sm text-gray-700">
                <p>Mehmet'e atandı.</p>
            </div>
        </div>
    </div>
</div>
