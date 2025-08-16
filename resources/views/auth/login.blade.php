<x-layouts.guest>
    <x-slot:title>
        Login
    </x-slot:title>

    {{-- KONTEN UTAMA HALAMAN LOGIN --}}
    <div class="flex min-h-full items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-4">
            <div class="flex flex-col items-center justify-center space-y-3">
                <div class="h-20 w-20 p-3 rounded-full shadow-lg bg-white flex items-center justify-center">
                    <img src="{{ asset('images/KMI.png') }}" alt="Logo KMI" >
                </div>
                <h1 class="text-xl font-semibold text-gray-700">PT. Kayu Mabel Indonesia</h1>
            </div>

            <div class="bg-white/70 backdrop-blur-xl shadow-2xl rounded-2xl p-6 sm:p-8">
                <div class="text-left mb-6">
                    <h2 id="form-title" class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900">Login Korlap</h2>
                    <p class="mt-2 text-sm text-gray-600">Masuk untuk melanjutkan.</p>
                </div>

                @if($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-md" role="alert">
                        <p class="font-bold">Error</p>
                        <p>{{ $errors->first() }}</p>
                    </div>
                @endif

                <form id="korlap-form" class="space-y-3" action="{{ route('login.korlap') }}" method="POST">
                    @csrf
                    <div>
                        <label for="kode_korlap" class="sr-only">Kode Korlap</label>
                        <input id="kode_korlap" name="kode_korlap" type="text" required autofocus placeholder="Kode Korlap"
                               class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2.5 text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm transition">
                    </div>
                    <div>
                        <label for="nik" class="sr-only">NIK</label>
                        <input id="nik" name="nik" type="number" required placeholder="NIK"
                               class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2.5 text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm transition">
                    </div>
                    <div>
                        <label for="korlap_sap_user_id" class="sr-only">SAP User ID</label>
                        <input id="korlap_sap_user_id" name="sap_user_id" type="text" required readonly placeholder="SAP User ID (Otomatis)"
                               class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2.5 text-gray-900 placeholder-gray-500 bg-gray-100 cursor-not-allowed sm:text-sm transition">
                    </div>
                    <div>
                        <label for="korlap_password" class="sr-only">Password</label>
                        <input id="korlap_password" name="sap_password" type="password" required placeholder="Password"
                               class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2.5 text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm transition">
                    </div>
                    <div class="pt-2">
                        <button type="submit" class="group relative flex w-full justify-center rounded-md border border-transparent bg-gray-800 py-2.5 px-4 text-sm font-semibold text-white hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 transition-all duration-300 ease-in-out">
                            Masuk
                        </button>
                    </div>
                </form>

                <form id="admin-form" class="space-y-3 hidden" action="{{ route('login.admin') }}" method="POST">
                    @csrf
                    <div>
                        <label for="admin_sap_id" class="sr-only">SAP ID</label>
                        <input id="admin_sap_id" name="sap_id" type="text" required placeholder="SAP ID"
                               class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2.5 text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm transition">
                    </div>
                    <div>
                        <label for="admin_password" class="sr-only">Password</label>
                        <input id="admin_password" name="password" type="password" required placeholder="Password"
                               class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2.5 text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm transition">
                    </div>
                    <div class="pt-2">
                        <button type="submit" class="group relative flex w-full justify-center rounded-md border border-transparent bg-gray-800 py-2.5 px-4 text-sm font-semibold text-white hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 transition-all duration-300 ease-in-out">
                            Masuk sebagai Admin
                        </button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <button id="switch-to-admin" class="text-sm font-medium text-blue-600 hover:text-blue-500">Login sebagai Admin</button>
                    <button id="switch-to-korlap" class="text-sm font-medium text-blue-600 hover:text-blue-500 hidden">Login sebagai Korlap</button>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT KHUSUS UNTUK HALAMAN LOGIN --}}
    @push('scripts')
    <script>
        // NOTE: Kita tidak lagi membutuhkan `handleFormSubmit` karena
        //       sekarang kita menggunakan submit form HTML biasa, bukan AJAX (fetch).
        //       Ini menyederhanakan kode dan mengatasi masalah sebelumnya.

        function initializeApp() {
            const korlapForm = document.getElementById('korlap-form');
            const adminForm = document.getElementById('admin-form');
            const formTitle = document.getElementById('form-title');
            const switchToAdminBtn = document.getElementById('switch-to-admin');
            const switchToKorlapBtn = document.getElementById('switch-to-korlap');
            const kodeKorlapInput = document.getElementById('kode_korlap');
            const sapUserIdInput = document.getElementById('korlap_sap_user_id');

            // --- FUNGSI UNTUK BERALIH TAMPILAN FORM ---
            switchToAdminBtn.addEventListener('click', function(e) {
                e.preventDefault();
                korlapForm.classList.add('hidden');
                adminForm.classList.remove('hidden');
                switchToAdminBtn.classList.add('hidden');
                switchToKorlapBtn.classList.remove('hidden');
                formTitle.textContent = 'Login Admin';
            });

            switchToKorlapBtn.addEventListener('click', function(e) {
                e.preventDefault();
                adminForm.classList.add('hidden');
                korlapForm.classList.remove('hidden');
                switchToKorlapBtn.classList.add('hidden');
                switchToAdminBtn.classList.remove('hidden');
                formTitle.textContent = 'Login Korlap';
            });
            
            // --- FUNGSI OTOMATIS MENGISI SAP USER ID (UNTUK KORLAP) ---
            kodeKorlapInput.addEventListener('blur', async function() {
                const kode = this.value.trim();
                if (!kode) {
                    sapUserIdInput.value = '';
                    return;
                }
                
                // Mengambil CSRF token dari layout
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                try {
                    const response = await fetch('/api/get-sap-user-id', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ kode_admin: kode })
                    });
                    
                    if (response.ok) {
                        const result = await response.json();
                        sapUserIdInput.value = result.sap_user_id || '';
                    } else {
                        // Fallback jika API gagal
                        sapUserIdInput.value = `SAP_${kode.toUpperCase()}`;
                    }
                } catch (error) {
                    console.error('Error fetching SAP ID:', error);
                    // Fallback jika terjadi error
                    sapUserIdInput.value = `SAP_${kode.toUpperCase()}`;
                }
            });
        }

        // Jalankan Inisialisasi
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeApp);
        } else {
            initializeApp();
        }
    </script>
    @endpush

</x-layouts.guest>