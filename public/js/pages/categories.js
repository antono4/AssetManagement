/* Categories page renderer — CRUD via AJAX (admin writes) */
window.PAGE_RENDERERS.categories = async function () {
    const res = await api('/api/categories');
    if (!res.ok) {
        document.getElementById('spaContent').innerHTML = '<p class="text-red-400 text-sm">'+escapeHtml(res.message)+'</p>';
        return;
    }
    const cats = res.data.data;
    const isAdmin = window.APP_USER.isAdmin;

    const rows = cats.map(c => `
        <tr>
            <td><span class="font-medium text-white">${escapeHtml(c.name)}</span></td>
            <td class="text-xs text-slate-400">${escapeHtml(c.description||'-')}</td>
            <td class="text-center"><span class="badge badge-staff">${c.asset_count} aset</span></td>
            <td class="text-right whitespace-nowrap">
                ${isAdmin ? `<button class="btn-ghost !py-1 !px-2 text-xs mr-1" onclick="editCategory(${c.id})" data-cursor>Edit</button>
                <button class="btn-danger !py-1 !px-2 text-xs" onclick="deleteCategory(${c.id}, '${escapeHtml(c.name)}')" data-cursor>Hapus</button>` : '<span class="text-xs text-slate-500">read-only</span>'}
            </td>
        </tr>`).join('') || '<tr><td colspan="4" class="text-center text-slate-500 py-8">Belum ada kategori</td></tr>';

    document.getElementById('spaContent').innerHTML = `
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div><h3 class="text-lg font-semibold text-white">Kategori Aset</h3><p class="text-xs text-slate-400">Total ${cats.length} kategori</p></div>
                ${isAdmin ? `<button class="btn-primary !py-2 text-sm" onclick="editCategory()" data-cursor>+ Tambah Kategori</button>` : ''}
            </div>
            <div class="glass p-1 overflow-x-auto">
                <table class="tbl"><thead><tr><th>Nama</th><th>Deskripsi</th><th class="text-center">Jumlah Aset</th><th class="text-right">Aksi</th></tr></thead><tbody>${rows}</tbody></table>
            </div>
        </div>
    `;
};

async function editCategory(id) {
    let cat = null;
    if (id) {
        const cats = (await api('/api/categories')).data?.data || [];
        cat = cats.find(c => c.id == id);
        if (!cat) { toast('Kategori tidak ditemukan', 'error'); return; }
    }
    openModal(`
        <h3 class="text-lg font-semibold text-white mb-4">${cat ? 'Edit Kategori' : 'Tambah Kategori'}</h3>
        <form id="catForm" class="space-y-3">
            <div><label class="block text-xs text-slate-300 mb-1">Nama *</label><input name="name" class="field text-sm" required value="${cat ? escapeHtml(cat.name) : ''}" data-cursor></div>
            <div><label class="block text-xs text-slate-300 mb-1">Deskripsi</label><input name="description" class="field text-sm" value="${cat ? escapeHtml(cat.description||'') : ''}" data-cursor></div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" class="btn-ghost text-sm" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn-primary text-sm" data-cursor>${cat ? 'Simpan' : 'Tambah'}</button>
            </div>
        </form>
    `);
    document.getElementById('catForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const fd = new FormData(e.target);
        const url = cat ? `/api/categories/${id}` : '/api/categories';
        const res = await api(url, { method: 'POST', body: fd });
        if (res.ok) { toast(res.data.message, 'success'); closeModal(); loadPage('categories'); }
        else {
            const errs = res.data.errors ? '<ul class="mt-1 text-xs list-disc list-inside">'+Object.values(res.data.errors).map(m=>`<li>${escapeHtml(m)}</li>`).join('')+'</ul>' : '';
            toast((res.message||'Gagal')+errs, 'error', 5000);
        }
    });
}

async function deleteCategory(id, name) {
    if (!confirm(`Hapus kategori "${name}"?`)) return;
    const res = await api(`/api/categories/${id}/delete`, { method: 'POST' });
    if (res.ok) { toast(res.data.message, 'success'); loadPage('categories'); }
    else toast(res.message, 'error', 5000);
}
