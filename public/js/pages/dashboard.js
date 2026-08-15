/* Dashboard page renderer */
window.PAGE_RENDERERS.dashboard = async function () {
    const res = await api('/api/dashboard/stats');
    if (!res.ok) {
        document.getElementById('spaContent').innerHTML = '<p class="text-red-400 text-sm">'+escapeHtml(res.message)+'</p>';
        return;
    }
    const d = res.data.data;

    const cards = [
        { label: 'Total Aset',      value: d.total_assets,                 icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', color: 'from-indigo-500 to-blue-500' },
        { label: 'Tersedia',        value: d.tersedia,                     icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', color: 'from-emerald-500 to-green-500' },
        { label: 'Dipinjam',        value: d.dipinjam,                     icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', color: 'from-amber-500 to-yellow-500' },
        { label: 'Rusak',           value: d.rusak,                        icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', color: 'from-red-500 to-rose-500' },
        { label: 'Total User',      value: d.total_users,                  icon: 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-2a4 4 0 10-8 0 4 4 0 008 0zm6 0a4 4 0 11-8 0 4 4 0 018 0z', color: 'from-cyan-500 to-teal-500' },
        { label: 'Nilai Total Aset',value: formatRupiah(d.total_value),    icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1', color: 'from-violet-500 to-purple-500' },
    ];

    const cardsHtml = cards.map(c => `
        <div class="stat-card glass-soft p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-slate-400 mb-1">${c.label}</p>
                    <p class="text-2xl font-semibold text-white">${c.value}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br ${c.color} flex items-center justify-center shadow-lg">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="${c.icon}"/></svg>
                </div>
            </div>
        </div>`).join('');

    // Category chart data
    const catLabels = d.by_category.map(r => r.category || 'Tanpa Kategori');
    const catData   = d.by_category.map(r => r.total);
    const catJson   = JSON.stringify({ labels: catLabels, data: catData });
    const statusJson = JSON.stringify({
        labels: ['Tersedia', 'Dipinjam', 'Rusak'],
        data: [d.tersedia, d.dipinjam, d.rusak]
    });

    // Recent assets
    const recentHtml = d.recent_assets.map(a => `
        <tr>
            <td class="font-mono text-xs text-cyan-300">${escapeHtml(a.asset_code)}</td>
            <td>${escapeHtml(a.name)}</td>
            <td>${statusBadge(a.status)}</td>
            <td class="text-slate-400 text-xs">${formatDate(a.purchase_date)}</td>
        </tr>`).join('') || '<tr><td colspan="4" class="text-center text-slate-500 py-6">Belum ada data</td></tr>';

    document.getElementById('spaContent').innerHTML = `
        <div class="space-y-6">
            <!-- Stat cards -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">${cardsHtml}</div>

            <!-- Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="glass p-5">
                    <h3 class="text-sm font-semibold text-white mb-4">Distribusi Status Aset</h3>
                    <div class="relative h-64"><canvas id="statusChart"></canvas></div>
                </div>
                <div class="glass p-5">
                    <h3 class="text-sm font-semibold text-white mb-4">Aset per Kategori</h3>
                    <div class="relative h-64"><canvas id="categoryChart"></canvas></div>
                </div>
            </div>

            <!-- Recent assets -->
            <div class="glass p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-white">Aset Terbaru</h3>
                    <a class="nav-link !py-1.5 !px-3 text-xs" data-page="assets">Lihat semua →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="tbl">
                        <thead><tr><th>Kode</th><th>Nama</th><th>Status</th><th>Tgl. Beli</th></tr></thead>
                        <tbody>${recentHtml}</tbody>
                    </table>
                </div>
            </div>
        </div>
    `;

    // Re-bind nav clicks inside content
    bindNavLinks();

    // Render charts
    renderStatusChart(statusJson);
    renderCategoryChart(catJson);
};

function renderStatusChart(s) {
    const ctx = document.getElementById('statusChart');
    if (!ctx) return;
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: s.labels,
            datasets: [{
                data: s.data,
                backgroundColor: ['#22c55e', '#eab308', '#ef4444'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { color: '#cbd5e1', padding: 14, font: { size: 11 } } } }
        }
    });
}

function renderCategoryChart(c) {
    const ctx = document.getElementById('categoryChart');
    if (!ctx) return;
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: c.labels,
            datasets: [{
                label: 'Jumlah Aset',
                data: c.data,
                backgroundColor: 'rgba(99,102,241,0.7)',
                borderColor: 'rgba(34,211,238,0.9)',
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { color: '#94a3b8', font: { size: 10 } }, grid: { color: 'rgba(255,255,255,0.05)' } },
                y: { beginAtZero: true, ticks: { color: '#94a3b8', precision: 0 }, grid: { color: 'rgba(255,255,255,0.05)' } }
            }
        }
    });
}

// Helper: re-bind nav links rendered inside SPA content
function bindNavLinks() {
    document.querySelectorAll('#spaContent .nav-link').forEach(a => {
        a.addEventListener('click', (e) => {
            e.preventDefault();
            loadPage(a.dataset.page);
        });
    });
}
