<script>
const routeUrl = "{{ route('cohv.convert') }}"; // pastikan route ini mengarah ke convertPlannedOrder

// --- Element References ---
const modalEl     = document.getElementById('modalHasil');
const overlayEl   = document.getElementById('modalOverlay');
const panelEl     = document.getElementById('modalPanel');
const btnClose    = document.getElementById('btn-close');
const btnClose2   = document.getElementById('btn-close-2');
const btnConvert  = document.getElementById('btn-convert');
const btnCopy     = document.getElementById('btn-copy');
const tblBody     = document.getElementById('tblBody');
const summaryEl   = document.getElementById('summary');
const modalAlert  = document.getElementById('modal-alert');
const inlineAlert = document.getElementById('inline-alert');
const inpPlanned  = document.getElementById('inp-planned');
const inpAuart    = document.getElementById('inp-auart');
const inpPlant    = document.getElementById('inp-plant');

let lastRows = []; // Simpan hasil untuk tombol copy

// --- Event Listeners ---
btnConvert.addEventListener('click', submitConvert);
btnClose.addEventListener('click', closeModal);
btnClose2.addEventListener('click', closeModal);
overlayEl.addEventListener('click', closeModal);
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
btnCopy.addEventListener('click', copyPRO);

// --- Core Functions ---

async function submitConvert() {
  const PLANNED_ORDER = inpPlanned.value.trim();
  const AUART         = inpAuart.value.trim();
  const PLANT         = inpPlant.value.trim();

  // Validasi sederhana sebelum kirim
  if (!PLANNED_ORDER || !AUART || !PLANT) {
      showInlineError('Input tidak lengkap.', 'Harap isi semua kolom yang diperlukan.');
      return;
  }

  const payload = { PLANNED_ORDER, AUART, PLANT };
  resetInlineAlert();
  setLoading(true); // <-- PERUBAHAN: Aktifkan loading state

  try {
    const res = await fetch(routeUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify(payload)
    });

    const ct = res.headers.get('content-type') || '';
    const text = await res.text();
    if (!ct.includes('application/json')) {
      showInlineError(`Server Error (status ${res.status})`, `Respons bukan JSON. Periksa log server atau detail di bawah ini: <br><pre class="mt-2 text-xs bg-gray-100 p-2 rounded">${sanitize(text)}</pre>`);
      return;
    }

    const data = JSON.parse(text);
    openModal();
    renderResult(data);

  } catch (e) {
    showInlineError('Gagal memanggil API.', String(e));
  } finally {
    setLoading(false); // <-- PERUBAHAN: Matikan loading state, baik sukses maupun gagal
  }
}

function renderResult(data) {
  // Logika ini sudah sangat baik, tidak perlu diubah.
  // Normalisasi data untuk handle single object atau array of results
  const rows = Array.isArray(data.results) ? data.results : [{
    planned_order: data.planned_order || data.PLANNED_ORDER,
    plant: data.plant || data.PLANT,
    production_orders: data.production_orders || (data.order_number ? [data.order_number] : []),
    success: data.success,
    messages: data.messages || (data.return ? [data.return] : [])
  }];

  lastRows = rows; // simpan untuk tombol copy

  tblBody.innerHTML = '';
  let ok = 0, fail = 0;

  rows.forEach((r, i) => {
    const pros = (r.production_orders || []).filter(Boolean);
    const msgObj = (r.messages && r.messages[0]) || {};
    const msg = msgObj.message || msgObj.MESSAGE || '';
    const statusOk = !!r.success;
    statusOk ? ok++ : fail++;

    const tr = document.createElement('tr');
    tr.className = "hover:bg-gray-50";
    tr.innerHTML = `
      <td class="px-4 py-2 text-gray-700">${i+1}</td>
      <td class="px-4 py-2 font-mono">${sanitize(r.planned_order) || '-'}</td>
      <td class="px-4 py-2">${sanitize(r.plant) || '-'}</td>
      <td class="px-4 py-2 space-x-1">
        ${pros.length ? pros.map(p=>`<span class="inline-flex rounded bg-gray-100 px-2 py-0.5 font-mono text-xs">${sanitize(p)}</span>`).join('') : '-'}
      </td>
      <td class="px-4 py-2">
        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs ${statusOk ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">
          ${statusOk ? 'SUCCESS' : 'FAILED'}
        </span>
      </td>
      <td class="px-4 py-2 text-gray-600">${sanitize(msg)}</td>
    `;
    tblBody.appendChild(tr);
  });

  summaryEl.textContent = `Total: ${rows.length} • Sukses: ${ok} • Gagal: ${fail}`;

  // Tampilkan alert di modal
  if (fail && !ok) {
    showModalAlert('Semua konversi gagal. Periksa pesan error pada kolom Message.', 'danger');
  } else if (fail) {
    showModalAlert('Beberapa item gagal dikonversi. Lihat kolom Message.', 'warning');
  } else {
    hideModalAlert();
  }
}

function copyPRO() {
  const allPRO = lastRows.flatMap(r => (r.production_orders || [])).filter(Boolean);
  if (!allPRO.length) {
    showModalAlert('Tidak ada PRO untuk disalin.', 'warning');
    return;
  }
  navigator.clipboard.writeText(allPRO.join('\n'))
    .then(() => showModalAlert(`Berhasil menyalin ${allPRO.length} PRO ke clipboard.`, 'success'))
    .catch(() => showModalAlert('Gagal menyalin ke clipboard.', 'danger'));
}

// --- Helper Functions ---

function setLoading(isLoading) {
    if (isLoading) {
        btnConvert.disabled = true;
        btnConvert.innerHTML = `
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Processing...
        `;
    } else {
        btnConvert.disabled = false;
        btnConvert.innerHTML = 'Convert PLO → PRO';
    }
}

/* Modal helpers */
function openModal() {
  modalEl.classList.remove('hidden');
  requestAnimationFrame(() => {
    overlayEl.classList.remove('opacity-0');
    panelEl.classList.remove('opacity-0', 'scale-95');
  });
}
function closeModal() {
  overlayEl.classList.add('opacity-0');
  panelEl.classList.add('opacity-0', 'scale-95');
  setTimeout(() => modalEl.classList.add('hidden'), 150);
}
function showModalAlert(text, type='warning') {
  modalAlert.className = 'mb-3 rounded-xl border px-4 py-3 text-sm'; // Reset classes
  if (type === 'danger')  modalAlert.classList.add('border-red-300','bg-red-50','text-red-700');
  if (type === 'warning') modalAlert.classList.add('border-yellow-300','bg-yellow-50','text-yellow-700');
  if (type === 'success') modalAlert.classList.add('border-green-300','bg-green-50','text-green-700');
  modalAlert.textContent = text;
}
function hideModalAlert(){ modalAlert.classList.add('hidden'); }

/* Inline alert */
function showInlineError(title, detail='') {
  inlineAlert.classList.remove('hidden');
  inlineAlert.innerHTML = `<strong>${sanitize(title)}</strong>${detail ? `<br><span class="text-xs">${sanitize(detail)}</span>` : ''}`;
}
function resetInlineAlert(){ inlineAlert.classList.add('hidden'); inlineAlert.innerHTML=''; }

/* Utils */
function sanitize(str){ const d=document.createElement('div'); d.textContent=String(str||''); return d.innerHTML; }
</script>
