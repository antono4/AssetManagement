/* Reports page renderer — filter + print */
window.PAGE_RENDERERS.reports = async function () {
    // Initial load without filters -> fetch all
    const res = await api('/api/reports/assets');
    renderReport(res);
};

function renderReport(res) {
    if (!res.ok) {
        document.getElementById('spaContent').innerHTML = '<p class="text-red-400 text-sm">'+escapeHtml(res.message)+'</p>';
        return;
    }
    const d = res.data;
    const cats = d.categories || [];
    const catOpts = cats.map(c => `<option value="${c.id}">${escapeHtml(c.name)}</option>`).join('');

    const rows = d.data.map((a, i) => `
        <tr>
            <td class="text-center text-xs text-slate-500">${i+1}</td>
            <td class="font-mono text-xs text-cyan-300">${escapeHtml(a.asset_code)}</td>
            <td>${escapeHtml(a.name)}</td>
            <td class="text-xs">${escapeHtml(a.category_name||'-')}</td>
            <td class="text-xs text-slate-400">${escapeHtml(a.location||'-')}</td>
            <td>${statusBadge(a.status)}</td>
            <td class="text-xs">${formatDate(a.purchase_date)}</td>
            <td class="text-right text-xs">${formatRupiah(a.price)}</td>
        </tr>`).join('') || '<tr><td colspan="8" class="text-center text-slate-500 py-8">Tidak ada data untuk filter ini</td></tr>';

    const s = d.summary;
    document.getElementById('spaContent').innerHTML = `
        <div class="space-y-4">
            <!-- Filter bar -->
            <div class="glass p-4 no-print">
                <h3 class="text-sm font-semibold text-white mb-3">Filter Laporan</h3>
                <form id="reportFilter" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                    <div><label class="block text-xs text-slate-300 mb-1">Dari Tanggal</label><input type="date" name="start" class="field text-sm" value="${escapeHtml(d.filters.start||'')}" data-cursor></div>
                    <div><label class="block text-xs text-slate-300 mb-1">Sampai Tanggal</label><input type="date" name="end" class="field text-sm" value="${escapeHtml(d.filters.end||'')}" data-cursor></div>
                    <div><label class="block text-xs text-slate-300 mb-1">Kategori</label><select name="category_id" class="field text-sm" data-cursor><option value="">Semua</option>${catOpts}</select></div>
                    <div class="flex gap-2">
                        <button type="submit" class="btn-primary !py-2 text-sm flex-1" data-cursor>Terapkan</button>
                        <button type="button" class="btn-ghost !py-2 text-sm" onclick="printReport()" data-cursor>Print</button>
                    </div>
                </form>
            </div>

            <!-- Print area -->
            <div class="glass p-5 print-area">
                <div class="flex items-start justify-between mb-4 no-print">
                    <div><h3 class="text-lg font-semibold text-white">Laporan Aset</h3><p class="text-xs text-slate-400">${d.filters.start||'Awal'} s/d ${d.filters.end||'Sekarang'}</p></div>
                </div>

                <!-- Print-only header -->
                <div class="hidden print:block mb-4">
                    <h2 style="font-size:18px;font-weight:700">Laporan Manajemen Aset</h2>
                    <p>Periode: ${d.filters.start||'Awal'} s/d ${d.filters.end||'Sekarang'}</p>
                    <p>Dicetak: ${new Date().toLocaleString('id-ID')}</p>
                    <hr style="margin:8px 0">
                </div>

                <!-- Summary -->
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-4">
                    <div class="glass-soft p-3"><p class="text-[10px] text-slate-400">Total Record</p><p class="text-base font-semibold text-white">${s.total_records}</p></div>
                    <div class="glass-soft p-3"><p class="text-[10px] text-slate-400">Nilai Total</p><p class="text-base font-semibold text-white">${formatRupiah(s.total_value)}</p></div>
                    <div class="glass-soft p-3"><p class="text-[10px] text-slate-400">Tersedia</p><p class="text-base font-semibold text-green-400">${s.tersedia}</p></div>
                    <div class="glass-soft p-3"><p class="text-[10px] text-slate-400">Dipinjam</p><p class="text-base font-semibold text-yellow-400">${s.dipinjam}</p></div>
                    <div class="glass-soft p-3"><p class="text-[10px] text-slate-400">Rusak</p><p class="text-base font-semibold text-red-400">${s.rusak}</p></div>
                </div>

                <div class="overflow-x-auto">
                    <table class="tbl">
                        <thead><tr><th class="text-center">#</th><th>Kode</th><th>Nama</th><th>Kategori</th><th>Lokasi</th><th>Status</th><th>Tgl. Beli</th><th class="text-right">Harga</th></tr></thead>
                        <tbody>${rows}</tbody>
                        <tfoot><tr><td colspan="7" class="text-right font-semibold text-white">Total Nilai:</td><td class="text-right font-semibold text-white">${formatRupiah(s.total_value)}</td></tr></tfoot>
                    </table>
                </div>
            </div>
        </div>
    `;

    // Bind filter form
    document.getElementById('reportFilter').addEventListener('submit', async (e) => {
        e.preventDefault();
        const fd = new FormData(e.target);
        const params = new URLSearchParams();
        for (const [k,v] of fd.entries()) if (v) params.append(k, v);
        const r = await api('/api/reports/assets?'+params.toString());
        renderReport(r);
    });
}

function printReport() {
    window.print();
}
