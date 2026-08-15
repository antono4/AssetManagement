/* Assets page renderer — CRUD via AJAX */
window.PAGE_RENDERERS.assets = async function () {
    const [assetRes, catRes] = await Promise.all([
        api('/api/assets'),
        api('/api/categories')
    ]);

    if (!assetRes.ok) {
        document.getElementById('spaContent').innerHTML = '<p class="text-red-400 text-sm">'+escapeHtml(assetRes.message)+'</p>';
        return;
    }

    const assets = assetRes.data.data;
    const cats   = catRes.ok ? catRes.data.data : [];
    window.__categories = cats; // cache for modal

    const isAdmin = window.APP_USER.isAdmin;
    const search  = '';

    const rows = assets.map(a => `
        <tr>
            <td class="font-mono text-xs text-cyan-300">${escapeHtml(a.asset_code)}</td>
            <td>
                <div class="font-medium text-white">${escapeHtml(a.name)}</div>
                <div class="text-xs text-slate-500">${escapeHtml(a.brand_spec || '')}</div>
            </td>
            <td><span class="text-xs text-slate-300">${escapeHtml(a.category_name || '-')}</span></td>
            <td class="text-xs text-slate-400">${escapeHtml(a.location || '-')}</td>
            <td>${statusBadge(a.status)}</td>
            <td class="text-xs text-slate-400">${formatDate(a.purchase_date)}</td>
            <td class="text-right text-xs text-slate-300">${formatRupiah(a.price)}</td>
            <td class="text-right whitespace-nowrap">
                <button class="btn-ghost !py-1 !px-2 text-xs mr-1" onclick="viewAsset(${a.id})" data-cursor>Detail</button>
                ${isAdmin ? `<button class="btn-ghost !py-1 !px-2 text-xs mr-1" onclick="editAsset(${a.id})" data-cursor>Edit</button>
                <button class="btn-danger !py-1 !px-2 text-xs" onclick="deleteAsset(${a.id}, '${escapeHtml(a.name)}')" data-cursor>Hapus</button>` : ''}
            </td>
        </tr>`).join('') || '<tr><td colspan="8" class="text-center text-slate-500 py-8">Belum ada data aset</td></tr>';

    document.getElementById('spaContent').innerHTML = `
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h3 class="text-lg font-semibold text-white">Daftar Aset</h3>
                    <p class="text-xs text-slate-400">Total ${assets.length} aset terdaftar</p>
                </div>
                <div class="flex items-center gap-2">
                    <input id="assetSearch" class="field !py-2 !px-3 text-sm w-48" placeholder="Cari aset..." oninput="filterAssets()" data-cursor>
                    ${isAdmin ? `<button class="btn-primary !py-2 text-sm" onclick="editAsset()" data-cursor>+ Tambah Aset</button>` : ''}
                </div>
            </div>

            <div class="glass p-1 overflow-x-auto">
                <table class="tbl" id="assetTable">
                    <thead>
                        <tr><th>Kode</th><th>Nama</th><th>Kategori</th><th>Lokasi</th><th>Status</th><th>Tgl. Beli</th><th class="text-right">Harga</th><th class="text-right">Aksi</th></tr>
                    </thead>
                    <tbody id="assetTbody">${rows}</tbody>
                </table>
            </div>
        </div>
    `;
};

function filterAssets() {
    const q = document.getElementById('assetSearch').value.toLowerCase();
    document.querySelectorAll('#assetTbody tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}

// ---------- Asset form modal (create/edit) ----------
async function editAsset(id) {
    let asset = null;
    if (id) {
        const res = await api(`/api/assets/${id}`);
        if (!res.ok) { toast(res.message, 'error'); return; }
        asset = res.data.data;
    }

    const cats = window.__categories || [];
    const catOpts = cats.map(c => `<option value="${c.id}" ${asset && asset.category_id == c.id ? 'selected' : ''}>${escapeHtml(c.name)}</option>`).join('');

    const statusOpts = ['tersedia', 'dipinjam', 'rusak']
        .map(s => `<option value="${s}" ${asset && asset.status === s ? 'selected' : ''}>${s.charAt(0).toUpperCase()+s.slice(1)}</option>`).join('');

    openModal(`
        <h3 class="text-lg font-semibold text-white mb-4">${asset ? 'Edit Aset' : 'Tambah Aset'}</h3>
        <form id="assetForm" class="space-y-3">
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs text-slate-300 mb-1">Kode Aset *</label><input name="asset_code" class="field text-sm" required value="${asset ? escapeHtml(asset.asset_code) : 'AST-'+String(Date.now()).slice(-4)}" data-cursor></div>
                <div><label class="block text-xs text-slate-300 mb-1">Nama *</label><input name="name" class="field text-sm" required value="${asset ? escapeHtml(asset.name) : ''}" data-cursor></div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs text-slate-300 mb-1">Kategori *</label><select name="category_id" class="field text-sm" required data-cursor><option value="">Pilih...</option>${catOpts}</select></div>
                <div><label class="block text-xs text-slate-300 mb-1">Status *</label><select name="status" class="field text-sm" required data-cursor>${statusOpts}</select></div>
            </div>
            <div><label class="block text-xs text-slate-300 mb-1">Merk / Spesifikasi</label><input name="brand_spec" class="field text-sm" value="${asset ? escapeHtml(asset.brand_spec||'') : ''}" data-cursor></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs text-slate-300 mb-1">Lokasi</label><input name="location" class="field text-sm" value="${asset ? escapeHtml(asset.location||'') : ''}" data-cursor></div>
                <div><label class="block text-xs text-slate-300 mb-1">Tanggal Pembelian</label><input type="date" name="purchase_date" class="field text-sm" value="${asset ? escapeHtml(asset.purchase_date||'') : ''}" data-cursor></div>
            </div>
            <div><label class="block text-xs text-slate-300 mb-1">Harga (Rp)</label><input type="number" name="price" class="field text-sm" value="${asset ? escapeHtml(asset.price||0) : 0}" data-cursor></div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" class="btn-ghost text-sm" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn-primary text-sm" data-cursor>${asset ? 'Simpan' : 'Tambah'}</button>
            </div>
        </form>
    `);

    document.getElementById('assetForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const fd = new FormData(e.target);
        const url = asset ? `/api/assets/${id}` : '/api/assets';
        const res = await api(url, { method: 'POST', body: fd });
        if (res.ok) {
            toast(res.data.message, 'success');
            closeModal();
            loadPage('assets');
        } else {
            const errs = res.data.errors ? '<ul class="mt-1 text-xs list-disc list-inside">'+Object.values(res.data.errors).map(m=>`<li>${escapeHtml(m)}</li>`).join('')+'</ul>' : '';
            toast((res.message||'Gagal') + errs, 'error', 5000);
        }
    });
}

// ---------- View asset + logs ----------
async function viewAsset(id) {
    const [aRes, lRes] = await Promise.all([api(`/api/assets/${id}`), api(`/api/assets/${id}/logs`)]);
    if (!aRes.ok) { toast(aRes.message, 'error'); return; }
    const a = aRes.data.data;
    const logs = lRes.ok ? lRes.data.data : [];

    const logsHtml = logs.map(l => `
        <tr>
            <td class="text-xs text-slate-400">${formatDate(l.created_at)}</td>
            <td><span class="badge badge-staff">${escapeHtml(l.action)}</span></td>
            <td class="text-xs text-slate-300">${escapeHtml(l.note||'')}</td>
            <td class="text-xs text-slate-400">${escapeHtml(l.user_name||'-')}</td>
        </tr>`).join('') || '<tr><td colspan="4" class="text-center text-slate-500 py-4">Belum ada riwayat</td></tr>';

    openModal(`
        <div class="flex items-start justify-between mb-4">
            <div>
                <h3 class="text-lg font-semibold text-white">${escapeHtml(a.name)}</h3>
                <p class="text-xs font-mono text-cyan-300">${escapeHtml(a.asset_code)}</p>
            </div>
            ${statusBadge(a.status)}
        </div>
        <div class="grid grid-cols-2 gap-3 text-sm mb-5">
            <div><p class="text-xs text-slate-400">Kategori</p><p>${escapeHtml(a.category_name||'-')}</p></div>
            <div><p class="text-xs text-slate-400">Lokasi</p><p>${escapeHtml(a.location||'-')}</p></div>
            <div class="col-span-2"><p class="text-xs text-slate-400">Merk / Spesifikasi</p><p>${escapeHtml(a.brand_spec||'-')}</p></div>
            <div><p class="text-xs text-slate-400">Tanggal Pembelian</p><p>${formatDate(a.purchase_date)}</p></div>
            <div><p class="text-xs text-slate-400">Harga</p><p>${formatRupiah(a.price)}</p></div>
        </div>
        <h4 class="text-sm font-semibold text-white mb-2">Riwayat Aktivitas</h4>
        <div class="overflow-x-auto max-h-48 overflow-y-auto">
            <table class="tbl"><thead><tr><th>Tanggal</th><th>Aksi</th><th>Catatan</th><th>Oleh</th></tr></thead><tbody>${logsHtml}</tbody></table>
        </div>
        <div class="flex justify-end pt-4"><button class="btn-ghost text-sm" onclick="closeModal()">Tutup</button></div>
    `);
}

// ---------- Delete asset ----------
async function deleteAsset(id, name) {
    if (!confirm(`Hapus aset "${name}"? Tindakan ini tidak dapat dibatalkan.`)) return;
    const res = await api(`/api/assets/${id}/delete`, { method: 'POST' });
    if (res.ok) { toast(res.data.message, 'success'); loadPage('assets'); }
    else toast(res.message, 'error');
}
