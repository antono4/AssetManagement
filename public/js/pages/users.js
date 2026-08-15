/* Users page renderer — CRUD via AJAX (admin only) */
window.PAGE_RENDERERS.users = async function () {
    const res = await api('/api/users');
    if (!res.ok) {
        document.getElementById('spaContent').innerHTML = '<p class="text-red-400 text-sm">'+escapeHtml(res.message)+'</p>';
        return;
    }
    const users = res.data.data;
    const currentId = window.APP_USER;

    const rows = users.map(u => `
        <tr>
            <td><div class="font-medium text-white">${escapeHtml(u.name)}</div><div class="text-xs text-slate-500">${escapeHtml(u.email||'-')}</div></td>
            <td class="text-sm text-slate-300">${escapeHtml(u.username)}</td>
            <td>${roleBadge(u.role)}</td>
            <td>${u.is_active ? '<span class="badge badge-tersedia">Aktif</span>' : '<span class="badge badge-rusak">Nonaktif</span>'}</td>
            <td class="text-xs text-slate-400">${formatDate(u.created_at)}</td>
            <td class="text-right whitespace-nowrap">
                <button class="btn-ghost !py-1 !px-2 text-xs mr-1" onclick="editUser(${u.id})" data-cursor>Edit</button>
                <button class="btn-danger !py-1 !px-2 text-xs" onclick="deleteUser(${u.id}, '${escapeHtml(u.name)}')" data-cursor>Hapus</button>
            </td>
        </tr>`).join('') || '<tr><td colspan="6" class="text-center text-slate-500 py-8">Belum ada user</td></tr>';

    document.getElementById('spaContent').innerHTML = `
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div><h3 class="text-lg font-semibold text-white">Manajemen User</h3><p class="text-xs text-slate-400">Total ${users.length} user</p></div>
                <button class="btn-primary !py-2 text-sm" onclick="editUser()" data-cursor>+ Tambah User</button>
            </div>
            <div class="glass p-1 overflow-x-auto">
                <table class="tbl"><thead><tr><th>Nama</th><th>Username</th><th>Role</th><th>Status</th><th>Dibuat</th><th class="text-right">Aksi</th></tr></thead><tbody>${rows}</tbody></table>
            </div>
        </div>
    `;
};

async function editUser(id) {
    let user = null;
    if (id) {
        const users = (await api('/api/users')).data?.data || [];
        user = users.find(u => u.id == id);
        if (!user) { toast('User tidak ditemukan', 'error'); return; }
    }
    const roleOpts = ['admin','staff'].map(r => `<option value="${r}" ${user && user.role===r ? 'selected':''}>${r.charAt(0).toUpperCase()+r.slice(1)}</option>`).join('');
    openModal(`
        <h3 class="text-lg font-semibold text-white mb-4">${user ? 'Edit User' : 'Tambah User'}</h3>
        <form id="userForm" class="space-y-3">
            <div><label class="block text-xs text-slate-300 mb-1">Nama *</label><input name="name" class="field text-sm" required value="${user ? escapeHtml(user.name) : ''}" data-cursor></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs text-slate-300 mb-1">Username *</label><input name="username" class="field text-sm" required value="${user ? escapeHtml(user.username) : ''}" data-cursor></div>
                <div><label class="block text-xs text-slate-300 mb-1">Email</label><input type="email" name="email" class="field text-sm" value="${user ? escapeHtml(user.email||'') : ''}" data-cursor></div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs text-slate-300 mb-1">Role *</label><select name="role" class="field text-sm" required data-cursor>${roleOpts}</select></div>
                <div><label class="block text-xs text-slate-300 mb-1">Status</label><select name="is_active" class="field text-sm" data-cursor><option value="1" ${user && user.is_active ? 'selected':''}>Aktif</option><option value="0" ${user && !user.is_active ? 'selected':''}>Nonaktif</option></select></div>
            </div>
            <div><label class="block text-xs text-slate-300 mb-1">Password ${user ? '(kosongkan jika tidak diubah)' : '*'}</label><input type="password" name="password" class="field text-sm" ${user ? '' : 'required'} data-cursor></div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" class="btn-ghost text-sm" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn-primary text-sm" data-cursor>${user ? 'Simpan' : 'Tambah'}</button>
            </div>
        </form>
    `);
    document.getElementById('userForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const fd = new FormData(e.target);
        const url = user ? `/api/users/${id}` : '/api/users';
        const res = await api(url, { method: 'POST', body: fd });
        if (res.ok) { toast(res.data.message, 'success'); closeModal(); loadPage('users'); }
        else {
            const errs = res.data.errors ? '<ul class="mt-1 text-xs list-disc list-inside">'+Object.values(res.data.errors).map(m=>`<li>${escapeHtml(m)}</li>`).join('')+'</ul>' : '';
            toast((res.message||'Gagal')+errs, 'error', 5000);
        }
    });
}

async function deleteUser(id, name) {
    if (!confirm(`Hapus user "${name}"?`)) return;
    const res = await api(`/api/users/${id}/delete`, { method: 'POST' });
    if (res.ok) { toast(res.data.message, 'success'); loadPage('users'); }
    else toast(res.message, 'error', 5000);
}
