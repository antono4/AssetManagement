<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>">
</head>
<body class="relative min-h-screen">

    <!-- Aurora backdrop -->
    <div class="bg-aurora"></div>

    <div class="relative z-10 flex min-h-screen">

        <!-- ============ Sidebar ============ -->
        <aside id="sidebar" class="glass-soft w-64 flex-shrink-0 flex flex-col p-4 border-r border-white/5 md:flex" style="display:none;">
            <!-- Brand -->
            <div class="flex items-center gap-3 px-2 py-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-cyan-400 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-sm font-semibold text-white leading-tight">Asset Mgmt</h1>
                    <p class="text-[10px] text-slate-400">IT &amp; Umum</p>
                </div>
            </div>

            <!-- Nav -->
            <nav class="flex-1 space-y-1">
                <a class="nav-link" data-page="dashboard" data-url="<?= site_url('/dashboard') ?>">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Dashboard</span>
                </a>
                <a class="nav-link" data-page="assets" data-url="<?= site_url('/asset') ?>">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <span>Data Aset</span>
                </a>
                <a class="nav-link" data-page="categories" data-url="<?= site_url('/category') ?>">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    <span>Kategori</span>
                </a>
                <?php if ($isAdmin): ?>
                <a class="nav-link" data-page="users" data-url="<?= site_url('/user') ?>">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-2a4 4 0 10-8 0 4 4 0 008 0zm6 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Manajemen User</span>
                </a>
                <?php endif; ?>
                <a class="nav-link" data-page="reports" data-url="<?= site_url('/report') ?>">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Laporan</span>
                </a>
            </nav>

            <!-- User -->
            <div class="glass-soft p-3 mt-4">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-cyan-400 flex items-center justify-center text-sm font-semibold text-white">
                        <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-white truncate"><?= esc($user['name'] ?? 'User') ?></p>
                        <p class="text-[10px] text-slate-400"><?= ucfirst($user['role'] ?? '') ?></p>
                    </div>
                    <a href="<?= site_url('/logout') ?>" title="Logout" class="text-slate-400 hover:text-red-400" data-cursor>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </a>
                </div>
            </div>
        </aside>

        <!-- ============ Main ============ -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Topbar -->
            <header class="glass-soft border-b border-white/5 px-5 py-3 flex items-center justify-between sticky top-0 z-20">
                <div class="flex items-center gap-3">
                    <button id="menuToggle" class="md:hidden text-slate-300" data-cursor>
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h2 id="pageTitle" class="text-base font-semibold text-white">Dashboard</h2>
                </div>
                <div class="flex items-center gap-3">
                    <span class="hidden sm:inline-flex badge <?= $isAdmin ? 'badge-admin' : 'badge-staff' ?>"><?= $isAdmin ? 'Admin' : 'Staff' ?></span>
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-cyan-400 flex items-center justify-center text-xs font-semibold text-white">
                        <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
                    </div>
                </div>
            </header>

            <!-- Content (SPA target) -->
            <main id="spaContent" class="flex-1 p-5 md:p-6 overflow-y-auto">
                <div class="text-center py-20 text-slate-400">
                    <div class="inline-block w-8 h-8 border-2 border-indigo-400 border-t-transparent rounded-full animate-spin mb-3"></div>
                    <p class="text-sm">Memuat...</p>
                </div>
            </main>
        </div>
    </div>

    <!-- Mobile sidebar overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-30 hidden" onclick="toggleSidebar()"></div>

    <script src="<?= base_url('js/app.js') ?>"></script>

    <!-- SPA controller: load halaman via fetch partial -->
    <script>
        window.APP_USER = {
            name: <?= json_encode($user['name'] ?? 'User') ?>,
            role: <?= json_encode($user['role'] ?? 'staff') ?>,
            isAdmin: <?= $isAdmin ? 'true' : 'false' ?>
        };

        // Page titles map
        const PAGE_TITLES = {
            dashboard: 'Dashboard',
            assets: 'Manajemen Aset',
            categories: 'Kategori Aset',
            users: 'Manajemen User',
            reports: 'Laporan Aset'
        };

        // Initial page from server-rendered var
        const INITIAL_PAGE = <?= json_encode($page) ?>;

        // Render functions defined per-page (loaded from separate JS)
        window.PAGE_RENDERERS = {};

        async function loadPage(page) {
            // Update active nav
            document.querySelectorAll('.nav-link').forEach(a => {
                a.classList.toggle('active', a.dataset.page === page);
            });
            document.getElementById('pageTitle').textContent = PAGE_TITLES[page] || '—';
            const content = document.getElementById('spaContent');
            content.innerHTML = '<div class="text-center py-20 text-slate-400"><div class="inline-block w-8 h-8 border-2 border-indigo-400 border-t-transparent rounded-full animate-spin mb-3"></div><p class="text-sm">Memuat '+ (PAGE_TITLES[page]||'') +'...</p></div>';

            // Each page renderer fetches its own data via API and injects HTML.
            if (window.PAGE_RENDERERS[page]) {
                try { await window.PAGE_RENDERERS[page](); }
                catch (e) { content.innerHTML = '<p class="text-red-400 text-sm">Gagal memuat: '+escapeHtml(e.message)+'</p>'; }
            } else {
                content.innerHTML = '<p class="text-slate-400 text-sm">Halaman tidak ditemukan.</p>';
            }

            // Update URL hash
            history.replaceState(null, '', '#' + page);
        }

        // Nav click handler (SPA navigation — no full reload)
        document.querySelectorAll('.nav-link').forEach(a => {
            a.addEventListener('click', (e) => {
                e.preventDefault();
                loadPage(a.dataset.page);
                if (window.innerWidth < 768) toggleSidebar(false);
            });
        });

        // Mobile sidebar toggle
        function toggleSidebar(show) {
            const sb = document.getElementById('sidebar');
            const ov = document.getElementById('sidebarOverlay');
            const visible = show === undefined ? (sb.style.display === 'none') : show;
            sb.style.display = visible ? 'flex' : 'none';
            ov.classList.toggle('hidden', !visible);
        }
        document.getElementById('menuToggle').addEventListener('click', () => toggleSidebar());

        // Show sidebar on desktop by default
        if (window.innerWidth >= 768) { document.getElementById('sidebar').style.display = 'flex'; }
    </script>

    <!-- Page modules JS (defines PAGE_RENDERERS) -->
    <script src="<?= base_url('js/pages/dashboard.js') ?>"></script>
    <script src="<?= base_url('js/pages/assets.js') ?>"></script>
    <script src="<?= base_url('js/pages/categories.js') ?>"></script>
    <?php if ($isAdmin): ?>
    <script src="<?= base_url('js/pages/users.js') ?>"></script>
    <?php endif; ?>
    <script src="<?= base_url('js/pages/reports.js') ?>"></script>

    <!-- Initial load (after page renderers are registered) -->
    <script>
        loadPage(INITIAL_PAGE);
    </script>
</body>
</html>
