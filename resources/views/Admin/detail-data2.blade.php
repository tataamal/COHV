<x-layouts.app>
    {{-- Slot untuk Header Halaman --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
            <div>
                <h1 class="text-xl font-base text-gray-800">Kode Plant: {{ $plant }}</h1>
                <p class="mt-1 text-sm text-gray-600">
                    <span class="font-semibold">Bagian:</span> {{ $bagian }} | 
                    <span class="font-semibold">Kategori:</span> {{ $categories }}
                </p>
            </div>
            <div class="flex items-center space-x-2 mt-4 sm:mt-0">
                {{-- Tombol Sync Data --}}
                <a href="{{ route('detail.data2', $plant) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h5M20 20v-5h-5M4 20h5v-5M20 4h-5v5" />
                    </svg>
                    Sync Data SAP
                </a>
                {{-- Tombol Kembali --}}
                <a href="#" onclick="hideAllDetails(); return false;" class="inline-flex items-center px-4 py-2 bg-yellow-300 border border-transparent rounded-md font-semibold text-xs text-neutral-900 uppercase tracking-widest hover:bg-yellow-500">
                    Hide All Detail
                </a>
                <a href="{{ route('dashboard_all') }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-900">
                    &larr; Back
                </a>
            </div>
        </div>

    {{-- Konten Utama Halaman --}}
    <div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 text-gray-900">

                    {{-- Container untuk Tabel Utama dan Paginasi --}}
                    <div id="outstanding-order-container">
                        
                        {{-- Tabel Utama (T_DATA) --}}
                        <div class="overflow-x-auto">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold">Sales Order (T_DATA)</h3>
                                <form method="GET" class="flex items-center w-full md:w-1/3">
                                    <div class="relative flex-grow">
                                        <input
                                            type="text"
                                            name="search"
                                            value="{{ request('search') }}"
                                            placeholder="Search..."
                                            class="w-full pl-10 pr-4 py-2 border rounded-lg text-gray-700 focus:outline-none focus:border-blue-500"
                                            id="searchInput"
                                        >
                                        <div class="absolute top-0 left-0 inline-flex items-center p-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-400" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M21.71,20.29,18,16.61A9,9,0,1,0,16.61,18l3.68,3.68a1,1,0,0,0,1.42,0A1,1,0,0,0,21.71,20.29ZM11,18a7,7,0,1,1,7-7A7,7,0,0,1,11,18Z"/>
                                            </svg>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <table class="min-w-full table-auto text-sm text-left whitespace-nowrap border">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-3 py-2 border bg-blue-50 text-blue-800 font-semibold">No.</th>
                                        <th class="px-3 py-2 border bg-blue-50 text-blue-800 font-semibold">NAME</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tdata as $index => $item)
                                        @php
                                        // key utk klik/expand
                                        $key = ($item->KDAUF ?? '') . '-' . ($item->KDPOS ?? '');

                                        // format EDATU (YYYYMMDD -> dd-mm-YYYY)
                                        $poDate = '-';
                                        if (!empty($item->EDATU) && strlen($item->EDATU) === 8) {
                                            try {
                                                $poDate = \Carbon\Carbon::createFromFormat('Ymd', $item->EDATU)->format('d-m-Y');
                                            } catch (\Exception $e) {
                                                $poDate = $item->EDATU;
                                            }
                                        }
                                        @endphp

                                        <tr class="hover:bg-blue-50 cursor-pointer"
                                            data-key="{{ ($item->KDAUF ?? '') . '-' . ($item->KDPOS ?? '') }}"
                                            onclick="openSalesItem(this)">
                                            <td class="px-2 py-1 border text-center">{{ $tdata->firstItem() + $index }}</td>  {{-- No. --}}
                                            <td class="px-2 py-1 border">{{ $item->NAME1 ?? '-' }}</td>                         {{-- NAME1 --}}
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="px-3 py-2 text-center text-gray-500 border">Tidak ada data ditemukan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination Links --}}
                        <div class="mt-4">
                            {{ $tdata->appends(['search' => $search])->links() }}
                        </div>
                    </div>

                    <div id="tdata2-section" class="mt-8 hidden"></div>

                    {{-- Container untuk Tabel T_DATA3 (awalnya tersembunyi) --}}
                    <div id="tdata3-container" class="mt-8 hidden">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Order Overview</h3>
                            {{-- Tombol Filter Status --}}
                            <div class="flex items-center gap-2">
                                <div class="flex bg-gray-200 rounded-lg p-1" id="status-filter">
                                    <button id="filter-all" class="px-3 py-1 rounded text-sm font-medium bg-blue-600 text-white" onclick="filterByStatus('all')">All</button>
                                    <button id="filter-plo" class="px-3 py-1 rounded text-sm font-medium text-gray-700" onclick="filterByStatus('plo')">PLO</button>
                                    <button id="filter-crtd" class="px-3 py-1 rounded text-sm font-medium text-gray-700" onclick="filterByStatus('crtd')">PRO (CRTD)</button>
                                    <button id="filter-released" class="px-3 py-1 rounded text-sm font-medium text-gray-700" onclick="filterByStatus('released')">PRO (Released)</button>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Kontrol Aksi Massal (Bulk Action) --}}
                        <div id="bulk-controls" class="flex items-center gap-2 mb-4 hidden">
                            <button id="bulk-convert-btn" class="bg-orange-600 text-white px-4 py-2 rounded text-sm hidden" onclick="bulkConvertPlannedOrders()">Convert Selected PLO</button>
                            <button id="bulk-release-btn" class="bg-green-600 text-white px-4 py-2 rounded text-sm hidden" onclick="bulkReleaseOrders()">Release Selected PRO</button>
                            <button class="bg-gray-500 text-white px-4 py-2 rounded text-sm" onclick="clearAllSelections()">Clear All</button>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table id="tdata3-table" class="min-w-full table-auto text-sm text-left whitespace-nowrap border">
                                <thead class="bg-gray-100  text-gray-700">
                                    <tr>
                                        <th class="px-3 py-2 border bg-blue-50 text-blue-800 font-semibold"><input type="checkbox" id="select-all" onchange="toggleSelectAll()" class="mr-1"> Select</th>
                                        <th class="px-3 py-2 border bg-blue-50 text-blue-800 font-semibold">No.</th> 
                                        <th class="px-3 py-2 border bg-blue-50 text-blue-800 font-semibold">PRO</th>
                                        <th class="px-3 py-2 border bg-blue-50 text-blue-800 font-semibold">STATUS</th>
                                        <th class="px-3 py-2 border bg-blue-50 text-blue-800 font-semibold">ACTION</th>
                                        <th class="px-3 py-2 border bg-blue-50 text-blue-800 font-semibold">MRP</th>
                                        <th class="px-3 py-2 border bg-blue-50 text-blue-800 font-semibold">MATERIAL</th>
                                        <th class="px-3 py-2 border bg-blue-50 text-blue-800 font-semibold">DESCRIPTION</th>
                                        <th class="px-3 py-2 border bg-blue-50 text-blue-800 font-semibold">QTY ORDER</th>
                                        <th class="px-3 py-2 border bg-blue-50 text-blue-800 font-semibold">QTY GR</th>
                                        <th class="px-3 py-2 border bg-blue-50 text-blue-800 font-semibold">OUTS GR</th>
                                        <th class="px-3 py-2 border bg-blue-50 text-blue-800 font-semibold">BASIC START DATE</th>
                                        <th class="px-3 py-2 border bg-blue-50 text-blue-800 font-semibold">BASIC FINISH DATE</th>
                                    </tr>
                                </thead>
                                <tbody id="tdata3-body">
                                    {{-- Data akan diisi oleh JavaScript --}}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Container untuk T_DATA1 dan T_DATA4 --}}
                    <div id="additional-data-container" class="mt-4">
                        {{-- Data akan diisi oleh JavaScript --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modals --}}
    {{-- @include('Admin.add-component-modal') --}}
    {{-- ========= RESULT MODAL (Tailwind) ========= --}}
    <div id="resultModal" class="fixed inset-0 z-50 hidden">
    <!-- overlay -->
    <div id="resultOverlay" class="absolute inset-0 bg-black/40 opacity-0 transition-opacity"></div>

    <!-- panel -->
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div id="resultPanel"
            class="w-full max-w-xl origin-center transform rounded-2xl bg-white shadow-2xl opacity-0 scale-95 transition-all">
        <div class="flex items-center justify-between border-b px-6 py-4">
            <h3 class="text-lg font-semibold">Production Order</h3>
            <button id="resultClose" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100">✕</button>
        </div>

        <!-- SINGLE VIEW -->
        <div id="singleView" class="px-6 py-5 space-y-4">
            <div>
            <div class="text-sm text-gray-500">Plant</div>
            <div id="plantValue" class="mt-1 text-base font-medium text-gray-900">-</div>
            </div>
            <div>
            <div class="text-sm text-gray-500">Production Order</div>
            <div id="poList" class="mt-1 flex flex-wrap gap-2">
                <!-- badges injected -->
            </div>
            </div>
        </div>

        <!-- BATCH VIEW -->
        <div id="batchView" class="hidden px-6 py-5">
            <div class="text-sm text-gray-500 mb-2">Converted Orders</div>
            <div class="overflow-auto rounded-xl border max-h-80">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left text-gray-600">
                <tr>
                    <th class="px-4 py-2">#</th>
                    <th class="px-4 py-2">Planned Order</th>
                    <th class="px-4 py-2">Plant</th>
                    <th class="px-4 py-2">Production Order</th>
                </tr>
                </thead>
                <tbody id="batchTbody" class="divide-y"></tbody>
            </table>
            </div>
        </div>

        <div class="border-t px-6 py-4 flex justify-end">
            <button id="resultOk" class="rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">OK</button>
        </div>
        </div>
    </div>
    </div>

    <!-- ========= SCHEDULE MODAL (FORM POST) ========= -->
    <div id="scheduleModal" class="fixed inset-0 z-50 hidden">
        <div id="scheduleOverlay" class="fixed inset-0 bg-black/30 backdrop-blur-sm opacity-0 transition-opacity"></div>

        <div id="scheduleContainer" class="absolute inset-0 flex items-center justify-center p-4">
            <div id="schedulePanel"
                class="w-full max-w-md origin-center transform rounded-2xl bg-white shadow-2xl opacity-0 scale-95 transition-all">
            <div class="flex items-center justify-between border-b px-6 py-4">
                <h3 class="text-lg font-semibold">Schedule Production Order</h3>
                <button type="button" onclick="closeScheduleModal()"
                        class="rounded-lg p-2 text-gray-500 hover:bg-gray-100">✕</button>
            </div>

            <form action="{{ route('reschedule.store') }}" method="POST" class="px-6 py-5 space-y-4" id="scheduleForm">
                @csrf
                <input type="hidden" name="aufnr" id="scheduleAufnr">

                <div>
                <label class="text-sm text-gray-600">Tanggal</label>
                <input type="date" name="date" id="scheduleDate" required
                        class="mt-1 w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                </div>

                <div>
                <label class="text-sm text-gray-600">Jam (HH.MM.SS)</label>
                <input type="text" name="time" id="scheduleTime" placeholder="00.00.00" required
                        pattern="^\d{2}[\.:]\d{2}[\.:]\d{2}$" inputmode="numeric" autocomplete="off"
                        class="mt-1 w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                <p class="text-xs text-gray-500 mt-1">Format 24 jam, contoh: 13.30.00</p>
                </div>

                <div class="border-t px-0 pt-4 flex justify-end gap-2">
                <button type="button" onclick="closeScheduleModal()"
                        class="rounded-lg px-4 py-2 text-gray-700 hover:bg-gray-100">Batal</button>
                <button type="submit"
                        class="rounded-lg bg-green-600 px-4 py-2 text-white hover:bg-green-700">Simpan</button>
                </div>
            </form>
            </div>
        </div>
        </div>

    <!-- ========= CHANGE WC MODAL ========= -->
    <div id="changeWcModal" class="fixed inset-0 z-50 hidden">
        <div id="changeWcOverlay" class="fixed inset-0 bg-black/30 backdrop-blur-sm opacity-0 transition-opacity"></div>

        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div id="changeWcPanel"
                class="w-full max-w-md origin-center transform rounded-2xl bg-white shadow-2xl opacity-0 scale-95 transition-all">
            <div class="flex items-center justify-between border-b px-6 py-4">
                <h3 class="text-lg font-semibold">Change Work Center</h3>
                <button type="button" onclick="closeChangeWcModal()" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100">✕</button>
            </div>

            <!-- FORM POST ke Controller -->
            <form action="{{ route('change-wc') }}" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                <!-- hidden param -->
                <input type="hidden" id="changeWcAufnr" name="aufnr">
                <input type="hidden" id="changeWcVornr" name="vornr">
                <input type="hidden" id="changeWcSequ"  name="sequ" value="0"><!-- isi jika punya sequence -->

                <div>
                <label for="changeWcInput" class="text-sm text-gray-600">Work Center</label>
                <input type="text" id="changeWcInput" name="work_center" placeholder="Masukkan Work Center baru"
                        class="mt-1 w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500" required>
                <p id="changeWcCurrent" class="text-xs text-gray-500 mt-1"></p>
                </div>

                <div class="border-t pt-4 flex justify-end gap-2">
                <button type="button" onclick="closeChangeWcModal()" class="rounded-lg px-4 py-2 text-gray-700 hover:bg-gray-100">Tutup</button>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">Save</button>
                </div>
            </form>
            </div>
        </div>
    </div>
    <!-- ========= CHANGE PV MODAL (NO SUBMIT YET) ========= -->
    <div id="changePvModal" class="fixed inset-0 z-50 hidden">
    <!-- overlay -->
    <div id="changePvOverlay" class="fixed inset-0 bg-black/30 backdrop-blur-sm opacity-0 transition-opacity"></div>

        <!-- panel -->
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div id="changePvPanel"
                class="w-full max-w-md origin-center transform rounded-2xl bg-white shadow-2xl opacity-0 scale-95 transition-all">
            <div class="flex items-center justify-between border-b px-6 py-4">
                <h3 class="text-lg font-semibold">Change Production Version (PV)</h3>
                <button type="button" onclick="closeChangePvModal()"
                        class="rounded-lg p-2 text-gray-500 hover:bg-gray-100">✕</button>
            </div>

            <div class="px-6 py-5 space-y-4">
                <!-- simpan param utk nanti dipakai saat submit -->
                <input type="hidden" id="changePvAufnr">

                <div>
                <label for="changePvInput" class="text-sm text-gray-600">Production Version (PV)</label>
                <input type="text" id="changePvInput" placeholder="Masukkan PV (mis. 0001)"
                        class="mt-1 w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                <p id="changePvCurrent" class="text-xs text-gray-500 mt-1"></p>
                </div>
            </div>

            <div class="border-t px-6 py-4 flex justify-end gap-2">
                <button type="button" onclick="closeChangePvModal()"
                        class="rounded-lg px-4 py-2 text-gray-700 hover:bg-gray-100">Tutup</button>
                <!-- tombol dummy; belum melakukan apa-apa -->
                <button type="button" id="changePvSubmitBtn"
                        class="rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700"
                        onclick="submitChangePv()">Simpan</button>
            </div>
        </div>
    </div>
    </div>

    {{-- Other modals here --}}

   @if(session('success'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                title: 'Sukses',
                text: "{{ session('success') }}",
                icon: 'success',
                showConfirmButton: true,   // tetap ada tombol OK
                confirmButtonText: 'OK',
                timer: 5000,              // auto close setelah 10 detik
                timerProgressBar: true,    // progress bar di bawah
            });
            
        </script>
    @endif

    @push('scripts')
    
    <script>
        // Variabel global untuk menyimpan state
        let currentSelectedRow = null;
        let currentActiveKey = null; // Bisa AUFNR atau PLNUM
        let selectedPLO = new Set();
        let selectedPRO = new Set();
        let allRowsData = [];
        let refreshPlant = null;
        let refreshOrders = []; // array AUFNR (12 digit)
        let currentSOKey = null;
        let currentFilterName = 'all';
        let currentT2Selection = null;
        function padAufnr(v){ const s=String(v||''); return s.length>=12 ? s : s.padStart(12,'0'); }
        const RELEASE_ORDER_URL = @json(route('release.order.direct', ['aufnr' => '__AUFNR__']));

        // Mengambil data dari controller dan menyimpannya di JavaScript
        const allTData2 = @json($allTData2, JSON_HEX_TAG);
        const allTData3 = @json($allTData3, JSON_HEX_TAG);
        const allTData1 = @json($allTData1, JSON_HEX_TAG);
        const allTData4ByAufnr = @json($allTData4ByAufnr, JSON_HEX_TAG);
        const tdata1ByAufnr = (() => {
        const by = {};
        if (allTData1 && typeof allTData1 === 'object') {
            Object.values(allTData1).forEach(arr => {
            (arr || []).forEach(t1 => {
                const a = (t1?.AUFNR || '').toString();
                if (!a) return;
                if (!by[a]) by[a] = [];
                by[a].push(t1);
            });
            });
        }
        return by;
        })();
        const allTData4ByPlnum = @json($allTData4ByPlnum, JSON_HEX_TAG);

        function openSalesItem(tr) {
            const key = tr.dataset.key; // "KDAUF-KDPOS"
            const t3Container = document.getElementById('tdata3-container');
            const t2Container = document.getElementById('outstanding-order-container');

            // klik baris yang sama -> tutup semua detail
            if (currentSelectedRow === tr) {
            hideAllDetails();
            return;
            }

            // reset + sembunyikan T_DATA3 dulu
            hideAllDetails();
            t3Container.classList.add('hidden');

            currentSelectedRow = tr;

            // tampilkan hanya baris yang diklik, sembunyikan yang lain + header + pager
            document.querySelectorAll('#outstanding-order-container tbody tr')
            .forEach(row => { if (row !== tr) row.classList.add('hidden'); });
            const headerRow = t2Container.querySelector('.flex.justify-between.items-center.mb-4');
            if (headerRow) headerRow.classList.add('hidden');
            const pager = t2Container.querySelector('.mt-4');
            if (pager) pager.classList.add('hidden');

            // render T_DATA2 dulu; T_DATA3 menunggu klik baris T_DATA2
            renderTData2Table(key);
        }
        /** ====== OPEN MODAL WITH DATA ======
         * Bentuk JSON yang didukung:
         *  Single: { planned_order, plant, production_orders:[...], ... }
         *  Batch : { results: [ { planned_order, plant, production_orders:[...] }, ... ] }
         */
        function showResultModal(data) {
        // set refreshPlant & refreshOrders agar tombol OK bisa trigger refresh data AUFNR
        const isBatch = Array.isArray(data?.results);
        if (isBatch) {
            const plant = data.results[0]?.plant || data.results[0]?.PLANT || @json($plant);
            const orders = data.results.flatMap(r => (r.production_orders || [])).filter(Boolean);
            refreshPlant  = plant;
            refreshOrders = Array.from(new Set(orders.map(padAufnr)));
        } else {
            const plant = data.plant || data.PLANT || @json($plant);
            const orders = (data.production_orders && data.production_orders.length)
            ? data.production_orders
            : (data.order_number ? [data.order_number] : []);
            refreshPlant  = plant;
            refreshOrders = Array.from(new Set(orders.map(padAufnr)));
        }

        // render UI (gabungan versi ringkasmu)
        const modalEl   = document.getElementById('resultModal');
        const overlayEl = document.getElementById('resultOverlay');
        const panelEl   = document.getElementById('resultPanel');

        const singleView = document.getElementById('singleView');
        const batchView  = document.getElementById('batchView');

        if (isBatch) {
            singleView.classList.add('hidden');
            batchView.classList.remove('hidden');
            const tbody = document.getElementById('batchTbody');
            tbody.innerHTML = '';
            data.results.forEach((r, i) => {
            const pros = (r.production_orders || []).filter(Boolean);
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50';
            tr.innerHTML = `
                <td class="px-4 py-2">${i+1}</td>
                <td class="px-4 py-2 font-mono">${sanitize(r.planned_order || r.PLANNED_ORDER || '-')}</td>
                <td class="px-4 py-2">${sanitize(r.plant || r.PLANT || '-')}</td>
                <td class="px-4 py-2">
                ${pros.length ? pros.map(p=>`<span class="inline-flex rounded bg-gray-100 px-2 py-0.5 font-mono text-xs">${sanitize(padAufnr(p))}</span>`).join(' ') : '-'}
                </td>
            `;
            tbody.appendChild(tr);
            });
        } else {
            singleView.classList.remove('hidden');
            batchView.classList.add('hidden');
            document.getElementById('plantValue').textContent = data.plant || data.PLANT || '-';
            const poList = document.getElementById('poList');
            const pros = (data.production_orders || []).filter(Boolean);
            poList.innerHTML = pros.length
            ? pros.map(p=>`<span class="inline-flex rounded bg-gray-100 px-2 py-0.5 font-mono text-xs">${sanitize(padAufnr(p))}</span>`).join('')
            : '<span class="text-gray-500">-</span>';
        }

        modalEl.classList.remove('hidden');
        requestAnimationFrame(() => {
            overlayEl.classList.remove('opacity-0');
            panelEl.classList.remove('opacity-0','scale-95');
        });
        }

        function openResultModal(){
        modalEl.classList.remove('hidden');
        requestAnimationFrame(() => {
            overlayEl.classList.remove('opacity-0');
            panelEl.classList.remove('opacity-0','scale-95');
        });
        }
        function closeResultModal(){
        overlayEl.classList.add('opacity-0');
        panelEl.classList.add('opacity-0','scale-95');
        setTimeout(() => modalEl.classList.add('hidden'), 150);
        }

        function toggleViews(isBatch){
        document.getElementById('singleView').classList.toggle('hidden', !!isBatch);
        document.getElementById('batchView').classList.toggle('hidden', !isBatch);
        }

        function sanitize(str){ const d=document.createElement('div'); d.textContent = String(str||''); return d.innerHTML; }
        
        function hideAllDetails() {
            const t2Container = document.getElementById('outstanding-order-container');

            // sembunyikan T_DATA3
            document.getElementById('tdata3-container').classList.add('hidden');

            // reset T_DATA2 card
            const box = document.getElementById('tdata2-section');
            if (box) { box.innerHTML = ''; box.classList.add('hidden'); }

            // bersihkan container detail lainnya (T_DATA1/T_DATA4)
            document.getElementById('additional-data-container').innerHTML = '';

            // tampilkan semua baris T_DATA lagi + bar search + pagination
            const allMainRows = document.querySelectorAll('#outstanding-order-container tbody tr');
            allMainRows.forEach(row => row.classList.remove('hidden'));
            const headerRow = t2Container.querySelector('.flex.justify-between.items-center.mb-4');
            if (headerRow) headerRow.classList.remove('hidden');
            const pager = t2Container.querySelector('.mt-4');
            if (pager) pager.classList.remove('hidden');

            // reset state
            if (currentSelectedRow) currentSelectedRow.classList.remove('bg-blue-100');
            currentSelectedRow = null;
            currentActiveKey = null;
            currentSOKey = null;
            allRowsData = [];
            clearAllSelections();
        }

        function filterByStatus(status) {
            document.querySelectorAll('#status-filter button').forEach(btn => {
                btn.classList.remove('bg-blue-600', 'text-white');
                btn.classList.add('text-gray-700');
            });
            document.getElementById(`filter-${status}`).classList.add('bg-blue-600', 'text-white');
            document.getElementById(`filter-${status}`).classList.remove('text-gray-700');

            let filteredData = allRowsData;
            if (status === 'plo') {
                filteredData = allRowsData.filter(d3 => d3.PLNUM && !d3.AUFNR);
            } else if (status === 'crtd') {
                filteredData = allRowsData.filter(d3 => d3.AUFNR && d3.STATS === 'CRTD');
            } else if (status === 'released') {
                filteredData = allRowsData.filter(d3 => d3.AUFNR && ['PCNF', 'REL', 'CNF REL'].includes(d3.STATS));
            }

            const tbody = document.getElementById('tdata3-body');
            tbody.innerHTML = '';
            filteredData.forEach((d3, index) => {
                const row = createTableRow(d3, index + 1);
                tbody.appendChild(row);
            });
            clearAllSelections();
        }

        // Fungsi untuk membersihkan string dan escape karakter khusus
        function escapeJsonString(str) {
            if (!str) return '';
            return str.toString()
                .replace(/\\/g, '\\\\')  // Escape backslash
                .replace(/"/g, '\\"')    // Escape double quotes
                .replace(/\n/g, '\\n')   // Escape newlines
                .replace(/\r/g, '\\r')   // Escape carriage returns
                .replace(/\t/g, '\\t');  // Escape tabs
        }

        function createTableRow(d3, index) {
            const row = document.createElement('tr');
            row.className = 'border-t';
            
            const canSelectForPLO = d3.PLNUM && !d3.AUFNR;
            const canSelectForPRO = d3.AUFNR && d3.STATS === 'CRTD';
            const canSelect = canSelectForPLO || canSelectForPRO;

            let statusDisplay = d3.STATS || '-';
            let statusClass = 'bg-gray-200 text-gray-800';
            if (d3.STATS === 'CRTD') statusClass = 'bg-orange-100 text-orange-800';
            if (['PCNF', 'REL', 'CNF REL'].includes(d3.STATS)) statusClass = 'bg-green-100 text-green-800';

            row.innerHTML = `
                <td class="px-2 py-1 border text-center">
                    ${canSelect ? `<input type="checkbox" class="bulk-select" data-type="${canSelectForPLO ? 'PLO' : 'PRO'}" data-id="${canSelectForPLO ? d3.PLNUM : d3.AUFNR}" data-auart="${d3.AUART || ''}" onchange="handleBulkSelect(this)">` : ''}
                </td>
                <td class="px-2 py-1 border text-center">${index}</td>
                <td class="px-2 py-1 border">
                    ${d3.AUFNR || '-'}
                    ${d3.AUFNR ? `
                        <button class="bg-indigo-600 text-white px-2 py-1 rounded text-xs hover:bg-indigo-700" onclick="showTData1ByAufnr('${d3.AUFNR}')">Route</button>
                        <button class="bg-blue-600 text-white px-2 py-1 rounded text-xs hover:bg-blue-700" onclick="showTData4ByAufnr('${d3.AUFNR}')">Comp</button>
                        ` : ''}
                </td>
                <td class="px-2 py-1 border text-center">
                    <span class="px-2 py-1 rounded-full text-xs font-medium ${statusClass}">${statusDisplay}</span>
                </td>
                <td class="px-2 py-1 border">
                    <div class="flex gap-1">
                        ${d3.AUFNR ? `
                        <button type="button"
                            class="bg-yellow-200 text-neutral-900 px-2 py-1 rounded text-xs hover:bg-yellow-700"
                            onclick="openSchedule('${encodeURIComponent(padAufnr(d3.AUFNR))}')">
                            Reschedule
                        </button>` : ''}
                    </div>
                </td>
                <td class="px-2 py-1 border">${d3.DISPO || '-'}</td>
                <td class="px-2 py-1 border">${d3.MATNR ? ltrim(d3.MATNR, '0') : '-'}</td>
                <td class="px-2 py-1 border">${escapeJsonString(d3.MAKTX) || '-'}</td>
                <td class="px-2 py-1 border text-right">${d3.PSMNG || '-'}</td>
                <td class="px-2 py-1 border text-right">${d3.WEMNG || '-'}</td>
                <td class="px-2 py-1 border text-right">${d3.MENG2 || '-'}</td>
                <td class="px-2 py-1 border">${formatDate(d3.SSAVD)}</td>
                <td class="px-2 py-1 border">${formatDate(d3.SSSLD)}</td>
            `;

            row.dataset.rowData = JSON.stringify(d3);
            return row;
        }

        // Event listener untuk tombol convert (menggunakan event delegation)
        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('convert-btn')) {
                    const row = e.target.closest('tr');
                    const rowIndex = parseInt(e.target.dataset.rowIndex);
                    const rowData = JSON.parse(row.dataset.rowData);
                    
                    console.log('Convert clicked for row:', rowIndex, rowData);
                    convertPlannedOrderFixed(rowData);
                }
            });
        });
        
        function convertPlannedOrderFixed(d3) {
        console.log('Convert button clicked', d3);

        const plnum = d3.PLNUM;
        const auart = d3.AUART;
        const plant = @json($plant); // aman: akan jadi string "A100" misalnya

        if (!plnum || !auart) {
            console.error('Missing data:', { plnum, auart });
            return alert('PLNUM atau Order Type (AUART) tidak ditemukan.');
        }

        if (!confirm(`Konversi Planned Order ${plnum} (Tipe: ${auart})?`)) return;

        const loader = document.getElementById('global-loading');
        if (loader) loader.style.display = 'flex';

        const url = '/create_prod_order';
        const requestData = {
            PLANNED_ORDER: plnum,
            AUART: auart,
            PLANT: plant
        };

        console.log('Sending request:', requestData);

        fetch(url, {
            method: 'POST',
            headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
            },
            body: JSON.stringify(requestData)
        })
        .then(async (response) => {
            const ct = response.headers.get('content-type') || '';
            const raw = await response.text();            // baca dulu sebagai text
            if (!ct.includes('application/json')) {       // hindari "Unexpected token <"
            throw new Error(`Non-JSON response (status ${response.status}): ${raw.slice(0,120)}...`);
            }
            const data = JSON.parse(raw);
            if (!response.ok) {
            const msg = data?.error || data?.message || response.statusText;
            throw new Error(msg);
            }
            return data;
        })
        .then((data) => {
            console.log('Response data:', data);

            // Normalisasi bentuk respons dari controller (pass-through Flask)
            // SINGLE: { planned_order, plant, production_orders:[...], success, messages:[] }
            // or old:  { success, order_number, return:{...} }
            // BATCH :  { results:[{ planned_order, plant, production_orders:[...], ... }, ...] }

            // Jika batch langsung tampilkan tabel batch
            if (Array.isArray(data.results)) {
            showResultModal({
                results: data.results.map(r => ({
                planned_order: r.planned_order || r.PLANNED_ORDER || plnum,
                plant:        r.plant || r.PLANT || plant,
                production_orders: (r.production_orders || []).map(padAufnr)
                }))
            });
            return;
            }

            // Single: bentukkan data untuk modal sederhana (tanpa form)
            const orders = (data.production_orders && data.production_orders.length)
            ? data.production_orders.map(padAufnr)
            : (data.order_number ? [padAufnr(data.order_number)] : []);

            const modalData = {
            planned_order: data.planned_order || plnum,
            plant: data.plant || plant,
            production_orders: orders
            };

            showResultModal(modalData);   // buka modal + render Plant & PRO
        })
        .catch((error) => {
            console.error('Convert error:', error);
            alert('Error: ' + error.message);
        })
        .finally(() => {
            if (loader) loader.style.display = 'none';
        });
        }

        /* ===== helper: pad AUFNR ke 12 digit ===== */
        function padAufnr(v) {
        const s = String(v || '');
        return s.length >= 12 ? s : s.padStart(12, '0');
        }

        /* ====== jembatan ke modal Tailwind yang sudah ada ======
        Kalau kamu pakai modal yang aku kirim sebelumnya, fungsi showResultModal()
        sudah tersedia. Kalau belum, ini versi ringkas untuk single/batch: */
        function showResultModal(data) {
        const modalEl   = document.getElementById('resultModal');
        const overlayEl = document.getElementById('resultOverlay');
        const panelEl   = document.getElementById('resultPanel');

        const singleView = document.getElementById('singleView');
        const batchView  = document.getElementById('batchView');

        if (Array.isArray(data.results)) {
            // batch
            singleView.classList.add('hidden');
            batchView.classList.remove('hidden');
            const tbody = document.getElementById('batchTbody');
            tbody.innerHTML = '';
            data.results.forEach((r,i) => {
            const pros = (r.production_orders || []).filter(Boolean);
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50';
            tr.innerHTML = `
                <td class="px-4 py-2">${i+1}</td>
                <td class="px-4 py-2 font-mono">${sanitize(r.planned_order) || '-'}</td>
                <td class="px-4 py-2">${sanitize(r.plant) || '-'}</td>
                <td class="px-4 py-2">
                ${pros.length ? pros.map(p=>`<span class="inline-flex rounded bg-gray-100 px-2 py-0.5 font-mono text-xs">${sanitize(p)}</span>`).join(' ') : '-'}
                </td>
            `;
            tbody.appendChild(tr);
            });
        } else {
            // single
            singleView.classList.remove('hidden');
            batchView.classList.add('hidden');
            document.getElementById('plantValue').textContent = data.plant || '-';
            const poList = document.getElementById('poList');
            const pros = (data.production_orders || []).filter(Boolean);
            poList.innerHTML = pros.length
            ? pros.map(p=>`<span class="inline-flex rounded bg-gray-100 px-2 py-0.5 font-mono text-xs">${sanitize(p)}</span>`).join('')
            : '<span class="text-gray-500">-</span>';
        }

        // buka modal (tailwind utility)
        modalEl.classList.remove('hidden');
        requestAnimationFrame(() => {
            overlayEl.classList.remove('opacity-0');
            panelEl.classList.remove('opacity-0','scale-95');
        });
        }

        function sanitize(str){ const d=document.createElement('div'); d.textContent = String(str||''); return d.innerHTML; }


        // --- Fungsi untuk menampilkan T_DATA1 dan T_DATA4 ---
        function showTData1ByAufnr(aufnr) {
            const container = document.getElementById('additional-data-container');
            const divId = `tdata1-${aufnr}`;

            // Toggle: jika sudah tampil → tutup & kembalikan baris T_DATA3
            const existing = document.getElementById(divId);
            if (existing) {
                existing.remove();
                document.querySelectorAll('#tdata3-body tr').forEach(row => row.classList.remove('hidden'));
                return;
            }

            // ⬇️ PENTING: Ambil routing dari index per-AUFNR
            const data = (tdata1ByAufnr && tdata1ByAufnr[aufnr]) ? tdata1ByAufnr[aufnr] : [];

            if (!Array.isArray(data) || data.length === 0) {
                alert('Tidak ada data routing ditemukan.');
                return;
            }

            // Sembunyikan baris T_DATA3 yang bukan milik AUFNR ini
            document.querySelectorAll('#tdata3-body tr').forEach(row => {
                if (!row.textContent.includes(aufnr)) row.classList.add('hidden');
                else row.classList.remove('hidden');
            });

            // --- Logika kolom "NEW WORKCENTER" (mengikuti versi sebelumnya) ---
            const renderNewWC = (t1) => {
                let defaultPv = '-';
                if (t1.VERID === '0001') defaultPv = t1.PV1;
                else if (t1.VERID === '0002') defaultPv = t1.PV2;
                else if (t1.VERID === '0003') defaultPv = t1.PV3;

                const defaultPrefix = defaultPv?.split('-')[0]?.trim();
                if (t1.VERID === '0001' && defaultPrefix && (t1.ARBPL?.trim() === defaultPrefix)) {
                return '-';
                }
                return t1.ARBPL || '-';
            };

            // --- Build rows ---
            const rowsHtml = data.map((t1, i) => {
                const ver = String(t1.VERID ?? '').trim().padStart(4,'0');
                const pv1Class = ver === '0001' ? 'bg-blue-100 font-semibold text-green-900' : '';
                const pv2Class = ver === '0002' ? 'bg-blue-100 font-semibold text-green-900' : '';
                const pv3Class = ver === '0003' ? 'bg-blue-100 font-semibold text-green-900' : '';
                const newWC = renderNewWC(t1);

                return `
                <tr>
                    <td class="border text-md px-2 py-1 text-center">${i + 1}</td>
                    <td class="border text-md px-2 py-1 text-center">${t1.VORNR || '-'}</td>
                    <td class="border text-md px-2 py-1 text-center">${t1.STEUS || '-'}</td>
                    <td class="border text-md px-2 py-1 text-center ">${t1.KTEXT || '-'}</td>
                    <td class="border text-md px-2 py-1 text-center ">${newWC}</td>
                    <td class="border text-md px-2 py-1 text-center ${pv1Class}">${t1.PV1 ?? '-'}</td>
                    <td class="border text-md px-2 py-1 text-center ${pv2Class}">${t1.PV2 ?? '-'}</td>
                    <td class="border text-md px-2 py-1 text-center ${pv3Class}">${t1.PV3 ?? '-'}</td>
                    <td class="border text-md px-2 py-1">
                    <div class="flex gap-2">
                        <button class="bg-blue-600 text-white px-2 py-1 rounded text-xs hover:bg-indigo-600"
                                onclick="openChangeWcModal('${t1.AUFNR}', '${t1.VORNR}', '${t1.ARBPL || ''}')">
                        Edit WC
                        </button>
                        <button class="bg-orange-600 text-white px-2 py-1 rounded text-xs hover:bg-orange-500"
                                onclick="openChangePvModal('${t1.AUFNR}', '${t1.VERID || ''}')">
                        Change PV
                        </button>
                    </div>
                    </td>
                </tr>
                `;
            }).join('');

            // --- Render blok (header & gaya disamakan) ---
            const block = document.createElement('div');
            block.id = divId;
            block.className = 'bg-gray-50 p-4 rounded-lg mb-4';
            block.innerHTML = `
                <div class="flex justify-between items-center mb-2">
                <h4 class="text-md font-semibold">Routing Overview</h4>
                </div>
                <div class="overflow-x-auto">
                <table class="table-auto w-full text-xs border">
                    <thead>
                    <tr>
                        <th class="px-3 py-2 border bg-blue-100 text-blue-900 font-semibold">No.</th>
                        <th class="px-3 py-2 border bg-blue-100 text-blue-900 font-semibold">ACTIVITY (VORNR)</th>
                        <th class="px-3 py-2 border bg-blue-100 text-blue-900 font-semibold">CONTROL KEY (STEUS)</th>
                        <th class="px-3 py-2 border bg-blue-100 text-blue-900 font-semibold">DESCRIPTION (KTEXT)</th>
                        <th class="px-3 py-2 border bg-blue-100 text-blue-900 font-semibold">NEW WORKCENTER</th>
                        <th class="px-3 py-2 border bg-blue-100 text-blue-900 font-semibold">PV1</th>
                        <th class="px-3 py-2 border bg-blue-100 text-blue-900 font-semibold">PV2</th>
                        <th class="px-3 py-2 border bg-blue-100 text-blue-900 font-semibold">PV3</th>
                        <th class="px-3 py-2 border bg-blue-100 text-blue-900 font-semibold">ACTION</th>
                    </tr>
                    </thead>
                    <tbody>${rowsHtml}</tbody>
                </table>
                </div>
            `;

            container.innerHTML = '';
            container.appendChild(block);

            // Fallback agar tidak error kalau handler modal belum didefinisikan
            if (typeof openModalEditWC !== 'function') {
                window.openModalEditWC = (aufnr, vornr, arbpl='') =>
                alert('openModalEditWC belum didefinisikan di halaman ini.');
            }
            if (typeof openModalChangePV !== 'function') {
                window.openModalChangePV = (aufnr, verid) =>
                alert('openModalChangePV belum didefinisikan di halaman ini.');
            }
        }

        function showTData4ByAufnr(aufnr){
            const container = document.getElementById('additional-data-container');
            const blockId   = `tdata4-${aufnr}`;

            // toggle: jika sudah muncul, tutup & tampilkan kembali semua baris T_DATA3
            const existing = document.getElementById(blockId);
            if (existing){
                existing.remove();
                document.querySelectorAll('#tdata3-body tr').forEach(row => row.classList.remove('hidden'));
                return;
            }

            // ambil data komponen per-AUFNR
            const data = (allTData4ByAufnr && allTData4ByAufnr[aufnr]) ? allTData4ByAufnr[aufnr] : [];
            if (!Array.isArray(data) || data.length === 0){
                alert('Tidak ada component list (T_DATA4) untuk order ini.');
                return;
            }

            // hanya tampilkan baris T_DATA3 yang terkait AUFNR ini
            document.querySelectorAll('#tdata3-body tr').forEach(row => {
                if (!row.textContent.includes(aufnr)) row.classList.add('hidden');
                else row.classList.remove('hidden');
            });

            // helper aman untuk teks
            const esc = (v) => {
                const d = document.createElement('div');
                d.textContent = String(v ?? '-');
                return d.innerHTML;
            };
            const ltrim0 = (s) => String(s ?? '').replace(/^0+/, '');

            // bikin rows komponen (field disesuaikan dengan nama dari T_DATA4 kamu)
            const rowsHtml = data.map((c, i) => `
                <tr>
                <td class="border px-2 py-1 text-center">${i+1}</td>
                <td class="border px-2 py-1 ">${ltrim0(c.MATNR)}</td>
                <td class="border px-2 py-1">${esc(c.MAKTX)}</td>
                <td class="border px-2 py-1 text-center">${c.BDMNG ?? c.MENGE ?? '-'}</td>
                <td class="border px-2 py-1 text-center">${c.ENMNG ?? '-'}</td>
                <td class="border px-2 py-1 text-center">${esc(c.MEINS || '-')}</td>
                </tr>
            `).join('');

            // render blok tabel komponen
            const block = document.createElement('div');
            block.id = blockId;
            block.className = 'bg-gray-50 p-4 rounded-lg mb-4';
            block.innerHTML = `
                <div class="flex justify-between items-center mb-2">
                <h4 class="text-md font-semibold">Component List (T_DATA4) — AUFNR: ${esc(aufnr)}</h4>
                </div>
                <div class="overflow-x-auto">
                <table class="table-auto w-full text-sm border">
                    <thead>
                    <tr>
                        <th class="px-3 py-2 border text-md bg-blue-100 text-blue-900 font-semibold">No.</th>
                        <th class="px-3 py-2 border text-md bg-blue-100 text-blue-900 font-semibold">Material</th>
                        <th class="px-3 py-2 border text-md bg-blue-100 text-blue-900 font-semibold">Description</th>
                        <th class="px-3 py-2 border text-md bg-blue-100 text-blue-900 font-semibold">Req. Qty</th>
                        <th class="px-3 py-2 border text-md bg-blue-100 text-blue-900 font-semibold">Stoc</th>
                        <th class="px-3 py-2 border text-md bg-blue-100 text-blue-900 font-semibold">Spec. Procurement</th>
                    </tr>
                    </thead>
                    <tbody>${rowsHtml}</tbody>
                </table>
                </div>
            `;

            // kosongkan container & tampilkan blok baru (atau kamu bisa append kalau mau ditumpuk)
            container.innerHTML = '';
            container.appendChild(block);
        }
        
        // --- Fungsi utilitas dan bulk action (disederhanakan) ---
        function handleBulkSelect(checkbox) {
            const type = checkbox.dataset.type;
            const id = checkbox.dataset.id;
            const auart = checkbox.dataset.auart;

            if (type === 'PLO') {
                // Menyimpan data sebagai string JSON agar Set bisa membedakannya
                const ploDataString = JSON.stringify({ plnum: id, auart: auart });
                if (checkbox.checked) {
                    selectedPLO.add(ploDataString);
                } else {
                    selectedPLO.delete(ploDataString);
                }
            } else { // Untuk PRO (Release)
                if (checkbox.checked) {
                    selectedPRO.add(id);
                } else {
                    selectedPRO.delete(id);
                }
            }
            updateBulkControls();
        }
        
        function updateBulkControls() {
            const bulkControls = document.getElementById('bulk-controls');
            const convertBtn = document.getElementById('bulk-convert-btn');
            const releaseBtn = document.getElementById('bulk-release-btn');
            const hasPLO = selectedPLO.size > 0;
            const hasPRO = selectedPRO.size > 0;
            bulkControls.classList.toggle('hidden', !hasPLO && !hasPRO);
            convertBtn.classList.toggle('hidden', !hasPLO);
            releaseBtn.classList.toggle('hidden', !hasPRO);
            if(hasPLO) convertBtn.textContent = `Convert Selected PLO (${selectedPLO.size})`;
            if(hasPRO) releaseBtn.textContent = `Release Selected PRO (${selectedPRO.size})`;
        }

        function toggleSelectAll() {
            const selectAll = document.getElementById('select-all').checked;
            document.querySelectorAll('.bulk-select').forEach(cb => {
                cb.checked = selectAll;
                handleBulkSelect(cb);
            });
        }
        
        function clearAllSelections() {
            selectedPLO.clear();
            selectedPRO.clear();
            document.querySelectorAll('.bulk-select, #select-all').forEach(cb => cb.checked = false);
            updateBulkControls();
        }

        function bulkConvertPlannedOrders() {
            if (selectedPLO.size === 0) return alert('Tidak ada PLO yang dipilih.');

            if (!confirm(`Apakah Anda yakin ingin mengkonversi ${selectedPLO.size} Planned Order?`)) return;

            const loader = document.getElementById('global-loading');
            if (loader) loader.style.display = 'flex';

            const ploArray = Array.from(selectedPLO).map(itemStr => JSON.parse(itemStr));

            Promise.all(ploArray.map(item => {
                // MENYESUAIKAN DENGAN API PYTHON
                const url = '/create_prod_order'; // URL BENAR
                const requestData = {
                    PLANNED_ORDER: item.plnum,
                    AUART: item.auart
                };

                return fetch(url, {
                    method: 'POST', // METODE BENAR
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json' // HEADER PENTING
                    },
                    body: JSON.stringify(requestData) // DATA DIKIRIM DI BODY
                }).then(res => {
                    if (!res.ok) return Promise.reject(res.statusText);
                    return res.json();
                });
            }))
            .then(results => {
                const successCount = results.filter(r => r.success).length;
                alert(`${successCount} dari ${ploArray.length} Planned Order berhasil dikonversi.`);
                location.reload();
            })
            .catch(error => {
                console.error("Bulk convert error:", error);
                alert('Terjadi kesalahan saat konversi massal. Beberapa atau semua order mungkin gagal.');
            })
            .finally(() => {
                if (loader) loader.style.display = 'none';
            });
        }

        function bulkReleaseOrders() {
            if (selectedPRO.size === 0) return alert('No PRO selected.');
            if (confirm(`Are you sure you want to release ${selectedPRO.size} production orders?`)) {
                alert('Fungsi bulk release belum diimplementasikan di backend.');
            }
        }

        function formatDate(dateString) {
            if (!dateString || dateString === '0000-00-00') return '-';
            try {
                const date = new Date(dateString);
                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const year = date.getFullYear();
                return `${day}-${month}-${year}`;
            } catch (e) { return dateString; }
        }

        function ltrim(str, char) {
            if (!str) return '';
            const regex = new RegExp(`^${char}+`);
            return str.replace(regex, '');
        }

        function formatSapYmd(ymd){
            if(!ymd || String(ymd).length !== 8) return '-';
            const s = String(ymd);
            return `${s.slice(6,8)}-${s.slice(4,6)}-${s.slice(0,4)}`;
        }

        function renderTData2Table(key){
        const box = document.getElementById('tdata2-section');
        box.innerHTML = '';
        box.classList.add('hidden');

        const rows = allTData2[key] || [];
        if (!rows.length){
            box.innerHTML = `
            <div class="bg-white rounded-lg border p-4">
                <h4 class="text-md font-semibold mb-2">Outstanding Order</h4>
                <p class="text-gray-500">Tidak ada data T_DATA2 untuk item ini.</p>
            </div>`;
            box.classList.remove('hidden');
            return;
        }

        let html = `
            <div class="bg-white rounded-lg border p-4">
            <h4 class="text-md font-semibold mb-3">Outstanding Order</h4>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto text-sm text-left whitespace-nowrap border">
                <thead class="bg-blue-50">
                    <tr>
                    <th class="px-3 py-2 border text-blue-800 font-semibold">No.</th>
                    <th class="px-3 py-2 border text-blue-800 font-semibold">ORDER</th>
                    <th class="px-3 py-2 border text-blue-800 font-semibold">ITEM</th>
                    <th class="px-3 py-2 border text-blue-800 font-semibold">MATERIAL FG</th>
                    <th class="px-3 py-2 border text-blue-800 font-semibold">DESCRIPTION MATERIAL</th>
                    <th class="px-3 py-2 border text-blue-800 font-semibold">PO DATE</th>
                    <th class="px-3 py-2 border text-blue-800 font-semibold">TOTAL PLO</th>
                    <th class="px-3 py-2 border text-blue-800 font-semibold">PRO (CRTD)</th>
                    <th class="px-3 py-2 border text-blue-800 font-semibold">PRO (Released)</th>
                    </tr>
                </thead>
                <tbody>`;

        rows.forEach((r, i) => {
            const soKey = `${r.KDAUF || ''}-${r.KDPOS || ''}`;
            const t3 = allTData3[soKey] || allTData3[key] || [];

            let ploCount = 0, proCrt = 0, proRel = 0;
            t3.forEach(d3 => {
            if (d3.PLNUM && !d3.AUFNR) ploCount++;
            if (d3.AUFNR){
                if (d3.STATS === 'CRTD') proCrt++;
                else if (['PCNF','REL','CNF REL'].includes(d3.STATS)) proRel++;
            }
            });

            html += `
            <tr class="t2-row hover:bg-blue-50 cursor-pointer"
                data-key="${soKey}"
                data-index="${i}">
                <td class="px-3 py-2 border text-center">${i + 1}</td>
                <td class="px-3 py-2 border">${sanitize(r.KDAUF || '-')}</td>
                <td class="px-3 py-2 border">${(r.KDPOS || '').toString().replace(/^0+/, '')}</td>
                <td class="px-3 py-2 border ">${(r.MATFG || '').toString().replace(/^0+/, '') || '-'}</td>
                <td class="px-3 py-2 border">${sanitize(r.MAKFG || '-')}</td>
                <td class="px-3 py-2 border">${formatSapYmd(r.EDATU)}</td>
                <td class="px-3 py-2 border text-center">${ploCount}</td>
                <td class="px-3 py-2 border text-center">${proCrt}</td>
                <td class="px-3 py-2 border text-center">${proRel}</td>
            </tr>`;
        });

        html += `
                </tbody>
                </table>
            </div>
            <p class="mt-2 text-xs text-gray-500">Klik salah satu baris untuk melihat Order Overview (T_DATA3).</p>
            </div>`;

        box.innerHTML = html;
        box.classList.remove('hidden');

        // pasang handler klik per baris T_DATA2 → tampilkan / toggle T_DATA3
        box.querySelectorAll('.t2-row').forEach(tr => {
            tr.addEventListener('click', () => handleClickTData2Row(tr.dataset.key, tr));
        });
        }
        

            /** Saat user klik baris T_DATA2 → tampilkan T_DATA3 untuk key itu */
        function handleClickTData2Row(key, tr) {
            const idx = Number(tr.dataset.index);
            const t3Container = document.getElementById('tdata3-container');

            // Jika klik baris yang sama → toggle T_DATA3
            if (currentT2Selection &&
                currentT2Selection.key === key &&
                currentT2Selection.index === idx) {

                const isVisible = !t3Container.classList.contains('hidden');
                if (isVisible) {
                t3Container.classList.add('hidden');       // sembunyikan T_DATA3
                tr.classList.remove('bg-blue-100');        // lepas highlight baris T_DATA2
                currentT2Selection = null;                 // reset pilihan
                } else {
                // kalau sedang tersembunyi dan diklik lagi → tampilkan lagi
                showTData3ForKey(key);
                tr.classList.add('bg-blue-100');
                currentT2Selection = { key, index: idx };
                }
                return;
            }

            // Klik baris berbeda → tampilkan T_DATA3 untuk key
            const box = document.getElementById('tdata2-section');
            box.querySelectorAll('.t2-row').forEach(r => r.classList.remove('bg-blue-100'));
            tr.classList.add('bg-blue-100');
            currentT2Selection = { key, index: idx };

            showTData3ForKey(key);
        }

        function showTData3ForKey(key){
            const t3Container = document.getElementById('tdata3-container');
            const rows = allTData3[key] || [];
            allRowsData = rows;

            if (rows.length) {
                currentFilterName = 'all';
                filterByStatus('all');
                t3Container.classList.remove('hidden');
            } else {
                const tbody = document.getElementById('tdata3-body');
                tbody.innerHTML = `
                <tr><td colspan="14" class="px-3 py-2 text-center text-gray-500 border">
                    Tidak ada order overview (T_DATA3) untuk item ini.
                </td></tr>`;
                t3Container.classList.remove('hidden');
            }
        }
        
        function openSchedule(aufnrEnc) {
            const aufnr = decodeURIComponent(aufnrEnc);
            document.getElementById('scheduleAufnr').value = aufnr;
            document.getElementById('scheduleDate').value = '';
            document.getElementById('scheduleTime').value = '00.00.00';

            const m = document.getElementById('scheduleModal');
            const o = document.getElementById('scheduleOverlay');
            const p = document.getElementById('schedulePanel');
            m.classList.remove('hidden');
            requestAnimationFrame(() => {
            o.classList.remove('opacity-0');
            p.classList.remove('opacity-0','scale-95');
            });
        }

        function closeScheduleModal() {
            const m = document.getElementById('scheduleModal');
            const o = document.getElementById('scheduleOverlay');
            const p = document.getElementById('schedulePanel');
            o.classList.add('opacity-0');
            p.classList.add('opacity-0','scale-95');
            setTimeout(() => m.classList.add('hidden'), 150);
        } 

        function openChangeWcModal(aufnr, vornr, currentWC = '') {
            // simpan param (buat dipakai nanti saat submit)
            document.getElementById('changeWcAufnr').value = aufnr || '';
            document.getElementById('changeWcVornr').value = vornr || '';
            document.getElementById('changeWcInput').value = '';
            document.getElementById('changeWcInput').placeholder = `${currentWC}`;
            document.getElementById('changeWcCurrent').textContent =
            currentWC ? `Current WC: ${currentWC}` : '';

            // tampilkan modal
            const m = document.getElementById('changeWcModal');
            const o = document.getElementById('changeWcOverlay');
            const p = document.getElementById('changeWcPanel');
            m.classList.remove('hidden');
            requestAnimationFrame(() => {
            o.classList.remove('opacity-0');
            p.classList.remove('opacity-0','scale-95');
            });
        }

        function closeChangeWcModal() {
            const m = document.getElementById('changeWcModal');
            const o = document.getElementById('changeWcOverlay');
            const p = document.getElementById('changeWcPanel');
            o.classList.add('opacity-0');
            p.classList.add('opacity-0','scale-95');
            setTimeout(() => m.classList.add('hidden'), 150);
        }

        // Tutup saat klik overlay
        document.addEventListener('click', (e) => {
            if (e.target && e.target.id === 'changeWcOverlay') closeChangeWcModal();
        });

        // Tutup saat tekan ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeChangeWcModal();
        });

        // Alias sesuai nama lama di tombol (biar kompatibel)
        if (typeof window.openModalEditWC !== 'function') {
            window.openModalEditWC = (aufnr, vornr, currentWC='') => openChangeWcModal(aufnr, vornr, currentWC);
        }

        function openChangePvModal(aufnr, currentPV = '') {
            // simpan param (buat dipakai nanti saat submit)
            document.getElementById('changePvAufnr').value = aufnr || '';
            document.getElementById('changePvInput').value = '';
            document.getElementById('changePvInput').placeholder = `${currentPV}`;
            document.getElementById('changePvCurrent').textContent =
            currentPV ? `Current PV: ${currentPV}` : '';

            // tampilkan modal
            const m = document.getElementById('changePvModal');
            const o = document.getElementById('changePvOverlay');
            const p = document.getElementById('changePvPanel');
            m.classList.remove('hidden');
            requestAnimationFrame(() => {
            o.classList.remove('opacity-0');
            p.classList.remove('opacity-0', 'scale-95');
            });
        }

        function closeChangePvModal() {
            const m = document.getElementById('changePvModal');
            const o = document.getElementById('changePvOverlay');
            const p = document.getElementById('changePvPanel');
            o.classList.add('opacity-0');
            p.classList.add('opacity-0', 'scale-95');
            setTimeout(() => m.classList.add('hidden'), 150);
        }

        // Tutup saat klik overlay
        document.addEventListener('click', (e) => {
            if (e.target && e.target.id === 'changePvOverlay') closeChangePvModal();
        });

        // Tutup saat tekan ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeChangePvModal();
        });

        // Alias agar kompatibel dg tombol lama di tabel routing
        if (typeof window.openModalChangePV !== 'function') {
            window.openModalChangePV = (aufnr, currentPV='') => openChangePvModal(aufnr, currentPV);
        }

        function openScheduleModal(aufnr) {
            const modal  = document.getElementById('scheduleModal');
            const panel  = document.getElementById('schedulePanel');
            const overlay= document.getElementById('scheduleOverlay');

            document.getElementById('scheduleAufnr').value = aufnr;

            // Default tanggal & waktu sekarang (local)
            const now = new Date();
            const yyyy = now.getFullYear();
            const mm   = String(now.getMonth()+1).padStart(2,'0');
            const dd   = String(now.getDate()).padStart(2,'0');
            const HH   = String(now.getHours()).padStart(2,'0');
            const MM   = String(now.getMinutes()).padStart(2,'0');
            const SS   = String(now.getSeconds()).padStart(2,'0');

            document.getElementById('scheduleDate').value = `${yyyy}-${mm}-${dd}`;
            document.getElementById('scheduleTime').value = `${HH}.${MM}.${SS}`;

            modal.classList.remove('hidden');
            requestAnimationFrame(() => {
            overlay.classList.remove('opacity-0');
            panel.classList.remove('opacity-0','scale-95');
            });
        }

        function closeScheduleModal() {
            const modal  = document.getElementById('scheduleModal');
            const panel  = document.getElementById('schedulePanel');
            const overlay= document.getElementById('scheduleOverlay');

            overlay.classList.add('opacity-0');
            panel.classList.add('opacity-0','scale-95');
            setTimeout(() => modal.classList.add('hidden'), 150);
        }

        // Auto-format jam: izinkan "133000" → "13.30.00"; "13:3" → "13:30:00"
        (function() {
            const el = document.getElementById('scheduleTime');
            if (!el) return;

            el.addEventListener('input', (e) => {
            let v = e.target.value.replace(/[^\d]/g,''); // ambil digit saja
            if (v.length > 6) v = v.slice(0,6);
            if (v.length >= 5)       e.target.value = v.replace(/(\d{2})(\d{2})(\d{1,2})/, '$1.$2.$3');
            else if (v.length >= 3)  e.target.value = v.replace(/(\d{2})(\d{1,2})/, '$1.$2');
            else                     e.target.value = v;
        });

         // Pastikan saat submit format jadi HH.MM.SS sesuai pattern (controller akan ubah ke ":")
        const form = document.getElementById('scheduleForm');
        form.addEventListener('submit', () => {
        // trimming kecil
        el.value = el.value.trim();
        });
    })();

    async function submitChangePv() {
        const btn   = document.getElementById('changePvSubmitBtn');
        const aufnr = (document.getElementById('changePvAufnr').value || '').trim();
        let   pv    = (document.getElementById('changePvInput').value || '').trim();

        if (!aufnr)  { return (window.notify?.('AUFNR tidak ditemukan.', 'error') || alert('AUFNR tidak ditemukan.')); }
        if (!pv)     { return (window.notify?.('Isi Production Version (PV) dahulu.', 'error') || alert('Isi PV dahulu.')); }

        // normalisasi input PV
        const verid = pv.replace(/\s+/g, '').padStart(4, '0');              // <-- TRIM & pad

        const oldLabel = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'Menyimpan...';

        try {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
            const url  = "{{ route('change-pv') }}";

            const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',                                     // <-- tambah Accept
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({ AUFNR: aufnr, PROD_VERSION: verid })
            });

            const raw = await res.text();                                         // <-- robust parse JSON
            let data = {};
            try { data = raw ? JSON.parse(raw) : {}; }
            catch { throw new Error(`Non-JSON response: ${raw.slice(0,120)}...`); }

            if (!res.ok) {
            const msg = data?.error || data?.message || `HTTP ${res.status}`;
            throw new Error(msg);
            }

            const beforeV = (data.before_version ?? '').toString().trim();
            const afterV  = (data.after_version  ?? verid).toString().trim();

            // normalisasi: ambil digit saja, pad ke 4 digit
            const normalizeVerid = v => {
            const digits = String(v ?? '').replace(/[^\d]/g,''); // buang spasi/tanda lain
            return digits.padStart(4,'0').slice(-4);
            };
            const newVerid = normalizeVerid(afterV);

            // --- UPDATE STATE & RERENDER ROUTING ---
            // amankan key AUFNR (raw & padded) jaga-jaga kalau index beda format
            const keyRaw = String(aufnr);
            const keyPad = keyRaw.padStart(12,'0');

            [keyRaw, keyPad].forEach(k => {
            if (Array.isArray(tdata1ByAufnr?.[k])) {
                // update VERID di semua baris routing utk AUFNR tsb
                tdata1ByAufnr[k] = tdata1ByAufnr[k].map(t1 => ({ ...t1, VERID: newVerid }));
            }
            });

            // hapus blok lama (kalau ada) dan render ulang
            document.getElementById(`tdata1-${keyRaw}`)?.remove();
            document.getElementById(`tdata1-${keyPad}`)?.remove();
            showTData1ByAufnr(keyRaw); // panggil dengan yang sama seperti saat open modal

            // Notifikasi
            if (typeof Swal !== 'undefined') {
            await Swal.fire({
                icon: 'success',
                title: 'PV berhasil diubah',
                html: `<div class="text-left">
                        <div><b>AUFNR:</b> ${aufnr}</div>
                        <div><b>Sebelum:</b> ${beforeV || '-'}</div>
                        <div><b>Sesudah:</b> ${newVerid || '-'}</div>
                    </div>`,
                timer: 2200,
                showConfirmButton: false
            });
            } else {
            alert(`PV berhasil diubah.\nSebelum: ${beforeV || '-'}\nSesudah: ${newVerid || '-'}`);
            }

            // Tutup modal
            typeof closeChangePvModal === 'function' && closeChangePvModal();

        } catch (err) {
            if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: 'error', title: 'Gagal', text: err.message || String(err) });
            } else {
            alert(err.message || String(err));
            }
        } finally {
            btn.disabled = false;
            btn.textContent = oldLabel;
        }
        }


    </script>
    @endpush
</x-layouts.app>
