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
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-900">
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
                                        <th class="px-3 py-2 border bg-blue-50 text-blue-800 font-semibold">PLO</th>
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

    {{-- Other modals here --}}

    @if(session('success'))
        <div id="modal-success" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
            <div class="bg-white p-6 rounded shadow-xl w-full max-w-md text-center">
                <h2 class="text-lg font-semibold text-green-700 mb-4">Sukses</h2>
                <p class="text-gray-700 mb-6">{{ session('success') }}</p>
                <button onclick="document.getElementById('modal-success').remove()"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    OK
                </button>
            </div>
        </div>
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

        // Mengambil data dari controller dan menyimpannya di JavaScript
        const allTData2 = @json($allTData2, JSON_HEX_TAG);
        const allTData3 = @json($allTData3, JSON_HEX_TAG);
        const allTData1 = @json($allTData1, JSON_HEX_TAG);
        const allTData4ByAufnr = @json($allTData4ByAufnr, JSON_HEX_TAG);
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
            
            // PERBAIKAN UTAMA: Simpan data di dataset element, bukan di onclick langsung
            row.innerHTML = `
                <td class="px-2 py-1 border text-center">${canSelect ? `<input type="checkbox" class="bulk-select" data-type="${canSelectForPLO ? 'PLO' : 'PRO'}" data-id="${canSelectForPLO ? d3.PLNUM : d3.AUFNR}"  data-auart="${d3.AUART || ''}" onchange="handleBulkSelect(this)">` : ''}</td>
                <td class="px-2 py-1 border text-center">${index}</td>
                <td class="px-2 py-1 border">
                ${d3.PLNUM ? 
                    `${d3.PLNUM} <button class="bg-blue-600 text-white px-2 py-1 rounded text-xs hover:bg-blue-700" onclick="showTData4ByPlnum('${d3.PLNUM}')">Component</button>` : 
                    '-'
                }
                </td>
                <td class="px-2 py-1 border">
                    ${d3.AUFNR || '-'}
                </td>
                <td class="px-2 py-1 border text-center">
                    <span class="px-2 py-1 rounded-full text-xs font-medium ${statusClass}">${statusDisplay}</span>
                </td>
                <td class="px-2 py-1 border">
                    <div class="flex gap-1">${d3.PLNUM ? `<button class="convert-btn bg-red-600 text-white px-2 py-1 rounded text-xs hover:bg-red-700" data-row-index="${index}">Convert</button>` : ''}${d3.AUFNR ? `<button class="bg-indigo-600 text-white px-2 py-1 rounded text-xs hover:bg-indigo-700" onclick="showTData1('${d3.ORDERX}', '${d3.VORNR}')">Route</button>` : ''}${d3.AUFNR ? `<button class="bg-blue-600 text-white px-2 py-1 rounded text-xs hover:bg-blue-700" onclick="showTData4ByAufnr('${d3.AUFNR}')">Comp</button>` : ''}</div>
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
            
            // PERBAIKAN: Simpan data d3 sebagai dataset pada row
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
        function showTData1(orderx, vornr) {
            const key = `${orderx}-${vornr}`;
            toggleAdditionalData(`tdata1-${key}`, () => {
                const data = allTData1[key];
                if (!data || data.length === 0) return '<p class="text-center text-gray-500">Tidak ada data routing.</p>';
                let tableRows = data.map((t1, index) => `<tr class="border-t"><td class="p-2 border">${index + 1}</td><td class="p-2 border">${t1.VORNR}</td><td class="p-2 border">${t1.KTEXT}</td><td class="p-2 border">${t1.ARBPL}</td></tr>`).join('');
                return `<h4 class="text-md font-semibold mb-2">Routing Overview (Order: ${orderx})</h4><table class="w-full text-sm border"><thead class="bg-blue-50"><tr><th class="p-2 border text-blue-800 font-semibold">No.</th><th class="p-2 border text-blue-800 font-semibold">Activity</th><th class="p-2 border text-blue-800 font-semibold">Description</th><th class="p-2 border text-blue-800 font-semibold">Work Center</th></tr></thead><tbody>${tableRows}</tbody></table>`;
            });
        }

        function showTData4ByAufnr(aufnr) {
            toggleAdditionalData(`tdata4-aufnr-${aufnr}`, () => createComponentTableHtml(allTData4ByAufnr[aufnr], `Komponen (PRO: ${aufnr})`));
        }

        function showTData4ByPlnum(plnum) {
            toggleAdditionalData(`tdata4-plnum-${plnum}`, () => createComponentTableHtml(allTData4ByPlnum[plnum], `Komponen (PLO: ${plnum})`));
        }
        
        function createComponentTableHtml(data, title) {
            // Menangani jika array data tidak ada atau kosong
            if (!data || data.length === 0) {
                return `<p class="text-center text-gray-500">Tidak ada data komponen.</p>`;
            }

            // Placeholder untuk data kosong dengan gaya yang diminta
            const emptyPlaceholder = `<span class="text-gray-500 opacity-50">Tidak ada data</span>`;

            // Membuat baris tabel (<tr>)
            const tableRows = data.map((item, index) => {
                // Menggunakan Nullish Coalescing Operator (??) untuk menangani nilai null/undefined
                const matnr = (item.MATNR ?? '').toString().replace(/^0+/, '') || emptyPlaceholder;
                const maktx = item.MAKTX || emptyPlaceholder;
                const ltext = item.LTEXT || emptyPlaceholder;
                const meins = item.MEINS || '';

                // Menangani kolom numerik, nilai 0 tetap ditampilkan
                const bdmng = item.BDMNG != null ? `${item.BDMNG} ${meins}`.trim() : emptyPlaceholder;
                const labst = item.LABST != null ? `${item.LABST} ${meins}`.trim() : emptyPlaceholder;

                return `
                    <tr class="border-t">
                        <td class="p-2 border">${index + 1}</td>
                        <td class="p-2 border">${matnr}</td>
                        <td class="p-2 border">${maktx}</td>
                        <td class="p-2 border text-right">${bdmng}</td>
                        <td class="p-2 border text-right">${labst}</td>
                        <td class="p-2 border">${ltext}</td>
                    </tr>
                `;
            }).join('');

            // Menggabungkan semua bagian menjadi HTML tabel yang utuh
            return `
                <h4 class="text-md font-semibold mb-2">${title}</h4>
                
                <table class="w-full text-sm border">
                    <thead class="bg-blue-50">
                        <tr>
                            <th class="p-2 border text-blue-800 font-semibold">No.</th>
                            <th class="p-2 border text-blue-800 font-semibold">Material</th>
                            <th class="p-2 border text-blue-800 font-semibold">Description</th>
                            <th class="p-2 border text-blue-800 font-semibold">Req. Qty</th>
                            <th class="p-2 border text-blue-800 font-semibold">Stock</th>
                            <th class="p-2 border text-blue-800 font-semibold">Spec. Requirement</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${tableRows}
                    </tbody>
                </table>
            `;
        }

        function toggleAdditionalData(key, contentGenerator) {
            const container = document.getElementById('additional-data-container');
            if (currentActiveKey === key) {
                container.innerHTML = '';
                currentActiveKey = null;
            } else {
                currentActiveKey = key;
                container.innerHTML = `<div class="bg-gray-50  p-4 rounded-lg shadow-inner">${contentGenerator()}</div>`;
            }
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
                <td class="px-3 py-2 border font-mono">${(r.MATFG || '').toString().replace(/^0+/, '') || '-'}</td>
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

        // function openSalesItem(tr){
        //     const key = tr.dataset.key;              // "KDAUF-KDPOS"
        //     // toggle: klik baris yang sama = tutup, baris lain = buka
        //     if (window.currentSelectedRow === tr) { 
        //     hideAllDetails(); 
        //     return; 
        //     }
        //     showTData3(key, tr);                     // fungsi yang kamu sudah punya
        // }

    </script>
    @endpush
</x-layouts.app>
