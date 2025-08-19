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
                        
                        {{-- Tabel Utama (T_DATA2) --}}
                        <div class="overflow-x-auto">
                             <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold">Outstanding Order (Sales Order)</h3>
                                <form method="GET" class="flex items-center w-full md:w-1/3">
                                    <div class="relative flex-grow">
                                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                                               class="w-full pl-10 pr-4 py-2 border rounded-lg text-gray-700 focus:outline-none focus:border-blue-500" id="searchInput">
                                        <div class="absolute top-0 left-0 inline-flex items-center p-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-400" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M21.71,20.29,18,16.61A9,9,0,1,0,16.61,18l3.68,3.68a1,1,0,0,0,1.42,0A1,1,0,0,0,21.71,20.29ZM11,18a7,7,0,1,1,7-7A7,7,0,0,1,11,18Z"/>
                                            </svg>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <table class="min-w-full table-auto text-sm text-left whitespace-nowrap border">
                                <thead class="bg-gray-100 ">
                                    <tr>
                                        <th class="px-3 py-2 border bg-blue-50 text-blue-800 font-semibold">No.</th>
                                        <th class="px-3 py-2 border bg-blue-50 text-blue-800 font-semibold">ORDER</th>
                                        <th class="px-3 py-2 border bg-blue-50 text-blue-800 font-semibold">ITEM</th>
                                        <th class="px-3 py-2 border bg-blue-50 text-blue-800 font-semibold">MATERIAL FG</th>
                                        <th class="px-3 py-2 border bg-blue-50 text-blue-800 font-semibold">DESCRIPTION MATERIAL</th>
                                        <th class="px-3 py-2 border bg-blue-50 text-blue-800 font-semibold">PO DATE</th>
                                        <th class="px-3 py-2 border bg-blue-50 text-blue-800 font-semibold">TOTAL PLO</th>
                                        <th class="px-3 py-2 border bg-blue-50 text-blue-800 font-semibold">PRO (CRTD)</th>
                                        <th class="px-3 py-2 border bg-blue-50 text-blue-800 font-semibold">PRO (Released)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($details as $index => $item)
                                        @php
                                            $key = $item->KDAUF . '-' . $item->KDPOS;
                                            $tData3Items = $allTData3[$key] ?? [];
                                            
                                            $proCountCRTD = 0;
                                            $proCountReleased = 0;
                                            $ploCount = 0;

                                            foreach ($tData3Items as $tdata3) {
                                                if (!empty($tdata3['AUFNR'])) {
                                                    if ($tdata3['STATS'] === 'CRTD') {
                                                        $proCountCRTD++;
                                                    } elseif (in_array($tdata3['STATS'], ['PCNF', 'REL', 'CNF REL'])) {
                                                        $proCountReleased++;
                                                    }
                                                }
                                                if (!empty($tdata3['PLNUM'])) $ploCount++;
                                            }
                                        @endphp
                                        <tr class="hover:bg-blue-50 cursor-pointer" onclick="showTData3('{{ $key }}', this)">
                                            <td class="px-2 py-1 border text-center">{{ $details->firstItem() + $index }}</td>
                                            <td class="px-2 py-1 border">{{ $item->KDAUF }}</td>
                                            <td class="px-2 py-1 border">{{ ltrim($item->KDPOS, '0') }}</td>
                                            <td class="px-2 py-1 border">{{ ltrim($item->MATFG, '0') }}</td>
                                            <td class="px-2 py-1 border">{{ $item->MAKFG }}</td>
                                            <td class="px-2 py-1 border">{{ $item->EDATU ? \Carbon\Carbon::parse($item->EDATU)->format('d-m-Y') : '-' }}</td>
                                            <td class="px-2 py-1 border text-center">{{ $ploCount }}</td>
                                            <td class="px-2 py-1 border text-center">{{ $proCountCRTD }}</td>
                                            <td class="px-2 py-1 border text-center">{{ $proCountReleased }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="px-3 py-2 text-center text-gray-500 border">Tidak ada data ditemukan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        {{-- Pagination Links --}}
                        <div class="mt-4">
                            {{ $details->appends(['search' => $search])->links() }}
                        </div>
                    </div>

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

        // Mengambil data dari controller dan menyimpannya di JavaScript
        const allTData3 = @json($allTData3, JSON_HEX_TAG);
        const allTData1 = @json($allTData1, JSON_HEX_TAG);
        const allTData4ByAufnr = @json($allTData4ByAufnr, JSON_HEX_TAG);
        const allTData4ByPlnum = @json($allTData4ByPlnum, JSON_HEX_TAG);

        function showTData3(key, clickedRow) {
            const t3Container = document.getElementById('tdata3-container');
            const t2Container = document.getElementById('outstanding-order-container');
            
            if (currentSelectedRow === clickedRow) {
                hideAllDetails();
                return;
            }

            hideAllDetails();
            currentSelectedRow = clickedRow;
            
            const allMainRows = document.querySelectorAll('#outstanding-order-container tbody tr');
            allMainRows.forEach(row => {
                if (row !== clickedRow) {
                    row.classList.add('hidden');
                }
            });
            t2Container.querySelector('.flex.justify-between.items-center.mb-4').classList.add('hidden'); // Row of title and search
            t2Container.querySelector('.mt-4').classList.add('hidden'); // Pagination

            if (allTData3[key] && allTData3[key].length > 0) {
                allRowsData = allTData3[key];
                filterByStatus('all');
                t3Container.classList.remove('hidden');
            } else {
                alert('Tidak ada detail order overview ditemukan.');
                hideAllDetails();
            }
        }
        
        function hideAllDetails() {
            const t2Container = document.getElementById('outstanding-order-container');
            document.getElementById('tdata3-container').classList.add('hidden');
            document.getElementById('additional-data-container').innerHTML = '';
            
            const allMainRows = document.querySelectorAll('#outstanding-order-container tbody tr');
            allMainRows.forEach(row => row.classList.remove('hidden'));
            t2Container.querySelector('.flex.justify-between.items-center.mb-4').classList.remove('hidden');
            t2Container.querySelector('.mt-4').classList.remove('hidden');

            if (currentSelectedRow) {
                currentSelectedRow.classList.remove('bg-blue-100');
            }
            currentSelectedRow = null;
            currentActiveKey = null;
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
                <td class="px-2 py-1 border text-center">${canSelect ? `<input type="checkbox" class="bulk-select" data-type="${canSelectForPLO ? 'PLO' : 'PRO'}" data-id="${canSelectForPLO ? d3.PLNUM : d3.AUFNR}" data-auart="${d3.AUART || ''}" onchange="handleBulkSelect(this)">` : ''}</td>
                <td class="px-2 py-1 border text-center">${index}</td>
                <td class="px-2 py-1 border">${d3.PLNUM || '-'} <button class="bg-blue-600 text-white px-2 py-1 rounded text-xs hover:bg-blue-700" onclick="showTData4ByPlnum('${d3.PLNUM}')">Component</button></td>
                <td class="px-2 py-1 border">${d3.AUFNR || '-'}</td>
                <td class="px-2 py-1 border text-center"><span class="px-2 py-1 rounded-full text-xs font-medium ${statusClass}">${statusDisplay}</span></td>
                <td class="px-2 py-1 border"><div class="flex gap-1">${d3.PLNUM ? `<button class="bg-red-600 text-white px-2 py-1 rounded text-xs hover:bg-red-700" onclick='convertPlannedOrder(${JSON.stringify(d3)})'>Convert</button>` : ''}${d3.AUFNR ? `<button class="bg-indigo-600 text-white px-2 py-1 rounded text-xs hover:bg-indigo-700" onclick="showTData1('${d3.ORDERX}', '${d3.VORNR}')">Route</button>` : ''}${d3.AUFNR ? `<button class="bg-blue-600 text-white px-2 py-1 rounded text-xs hover:bg-blue-700" onclick="showTData4ByAufnr('${d3.AUFNR}')">Comp</button>` : ''}</div></td>
                <td class="px-2 py-1 border">${d3.DISPO || '-'}</td>
                <td class="px-2 py-1 border">${d3.MATNR ? ltrim(d3.MATNR, '0') : '-'}</td>
                <td class="px-2 py-1 border">${d3.MAKTX || '-'}</td>
                <td class="px-2 py-1 border text-right">${d3.PSMNG || '-'}</td>
                <td class="px-2 py-1 border text-right">${d3.WEMNG || '-'}</td>
                <td class="px-2 py-1 border text-right">${d3.MENG2 || '-'}</td>
                <td class="px-2 py-1 border">${formatDate(d3.SSAVD)}</td>
                <td class="px-2 py-1 border">${formatDate(d3.SSSLD)}</td>
            `;
            return row;
        }

        function convertPlannedOrder(d3) {
            console.log('tombol telah ditekan');
            const plnum = d3.PLNUM;
            const auart = d3.AUART;

            if (!plnum || !auart) {
                return alert('PLNUM atau Order Type (AUART) tidak ditemukan.');
            }

            if (!confirm(`Konversi Planned Order ${plnum} (Tipe: ${auart})?`)) return;

            const loader = document.getElementById('global-loading');
            if (loader) loader.style.display = 'flex';

            // 1. URL diubah sesuai dengan route di Python
            const url = '/api/create_prod_order';

            // 2. Data disiapkan dalam bentuk objek
            const requestData = {
                PLANNED_ORDER: plnum,
                AUART: auart
            };

            fetch(url, {
                // 3. Metode diubah menjadi 'POST'
                method: 'POST', 
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    // 4. Header Content-Type ditambahkan (sangat penting untuk POST)
                    'Content-Type': 'application/json'
                },
                // 5. Data dikirim melalui 'body' dalam format string JSON
                body: JSON.stringify(requestData)
            })
            .then(response => {
                if (!response.ok) {
                    // Coba baca error dari JSON jika ada, jika tidak, gunakan status teks
                    return response.json().then(err => { throw new Error(err.error || response.statusText); });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert(`Order ${data.order_number} berhasil dibuat.`);
                    location.reload();
                } else {
                    // Menampilkan pesan error spesifik dari SAP/Python
                    const returnMsg = data.return || {};
                    throw new Error(returnMsg.MESSAGE || 'Terjadi kesalahan dari server saat konversi.');
                }
            })
            .catch(error => {
                alert(error.message);
            })
            .finally(() => {
                if (loader) loader.style.display = 'none';
            });
        }

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
    </script>
    @endpush
</x-layouts.app>
