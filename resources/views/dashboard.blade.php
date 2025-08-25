<x-layouts.app title="Dashboard - Production Management System">
    <!-- Welcome Section -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl p-8 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Selamat Datang di Production Management System</h1>
                    <p class="text-blue-100 text-lg">{{ now()->format('l, d F Y') }} - {{ now()->format('H:i') }} WIB</p>
                    <p class="text-blue-100 mt-2">Pilih plant yang ingin Anda akses untuk memulai pengelolaan produksi</p>
                </div>
                <div class="hidden md:block">
                    <div class="w-24 h-24 bg-white/10 rounded-full flex items-center justify-center">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Help & Support Section -->
    <x-ui.card title="Bantuan & Dukungan" description="Butuh bantuan? Temukan sumber daya yang Anda perlukan">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center p-6 bg-blue-50 rounded-lg">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h4 class="font-semibold text-gray-900 mb-2">Dokumentasi</h4>
                <p class="text-sm text-gray-600">Panduan lengkap penggunaan sistem</p>
            </div>

            <div class="text-center p-6 bg-green-50 rounded-lg">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                </div>
                <h4 class="font-semibold text-gray-900 mb-2">Hotline</h4>
                <p class="text-sm text-gray-600">Hubungi support 24/7</p>
            </div>

            <div class="text-center p-6 bg-orange-50 rounded-lg">
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <h4 class="font-semibold text-gray-900 mb-2">Live Chat</h4>
                <p class="text-sm text-gray-600">Chat dengan team support</p>
            </div>
        </div>
    </x-ui.card>

    <script>
        function toggleFaq(faqId) {
            const content = document.getElementById(faqId + '-content');
            const icon = document.getElementById(faqId + '-icon');
            
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                content.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }
    </script>
</x-layouts.app>