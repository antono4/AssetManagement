<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>">
</head>
<body class="relative overflow-hidden">

    <!-- Animated aurora backdrop -->
    <div class="bg-aurora"></div>

    <!-- Particle canvas -->
    <canvas id="particles"></canvas>

    <!-- Login card -->
    <main class="relative z-10 min-h-screen flex items-center justify-center p-4">
        <div class="glass w-full max-w-md p-8 md:p-10">

            <!-- Brand -->
            <div class="flex items-center gap-3 mb-8">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-indigo-500 to-cyan-400 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg font-semibold text-white">Asset Management</h1>
                    <p class="text-xs text-slate-400">Manajemen Aset IT &amp; Umum</p>
                </div>
            </div>

            <h2 class="text-2xl font-semibold text-white mb-1">Selamat Datang</h2>
            <p class="text-sm text-slate-400 mb-6">Masuk untuk mengakses dashboard.</p>

            <!-- Error -->
            <?php if (session('error')): ?>
                <div class="glass-soft px-4 py-3 mb-5 text-sm text-red-300 border border-red-500/30">
                    <?= esc(session('error')) ?>
                </div>
            <?php endif; ?>
            <?php if (session('success')): ?>
                <div class="glass-soft px-4 py-3 mb-5 text-sm text-green-300 border border-green-500/30">
                    <?= esc(session('success')) ?>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form id="loginForm" class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1.5">Username</label>
                    <input type="text" name="username" id="username" required autofocus
                           value="<?= esc(old('username', $old['username'] ?? '')) ?>"
                           class="field" placeholder="masukkan username"
                           data-cursor>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1.5">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required
                               class="field pr-10" placeholder="••••••••" data-cursor>
                        <button type="button" id="togglePass" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white" data-cursor>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.46 12C3.73 7.94 7.52 5 12 5s8.27 2.94 9.54 7c-1.27 4.06-5.06 7-9.54 7s-8.27-2.94-9.54-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" id="btnLogin" class="btn-primary w-full flex items-center justify-center gap-2" data-cursor>
                    <span id="btnLoginText">Masuk</span>
                    <svg id="btnLoginSpinner" class="hidden w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4z"/>
                    </svg>
                </button>
            </form>

            <!-- Demo credentials -->
            <div class="mt-6 glass-soft p-3 text-xs text-slate-400">
                <p class="font-medium text-slate-300 mb-1">Akun Demo:</p>
                <p>Admin &rarr; <code class="text-cyan-300">admin / admin123</code></p>
                <p>Staff &rarr; <code class="text-cyan-300">staff / staff123</code></p>
                <p class="mt-1.5 text-slate-500">Jika login gagal, jalankan sekali: <a href="<?= site_url('/setup') ?>" class="text-indigo-300 underline">/setup</a></p>
            </div>

            <p class="mt-6 text-center text-xs text-slate-500">&copy; <?= date('Y') ?> Asset Management. CodeIgniter 4.</p>
        </div>
    </main>

    <script src="<?= base_url('js/particles.js') ?>"></script>
    <script src="<?= base_url('js/app.js') ?>"></script>
    <script>
        // Toggle password
        document.getElementById('togglePass').addEventListener('click', function () {
            const p = document.getElementById('password');
            p.type = p.type === 'password' ? 'text' : 'password';
        });

        // AJAX submit (SPA feel — no full reload)
        document.getElementById('loginForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const btn = document.getElementById('btnLogin');
            const txt = document.getElementById('btnLoginText');
            const sp  = document.getElementById('btnLoginSpinner');
            btn.disabled = true; txt.textContent = 'Memproses...'; sp.classList.remove('hidden');

            const formData = new FormData(this);
            const res = await api('<?= site_url('/login') ?>', { method: 'POST', body: formData });

            btn.disabled = false; txt.textContent = 'Masuk'; sp.classList.add('hidden');

            if (res.ok) {
                toast('Login berhasil! Mengalihkan...', 'success', 1500);
                setTimeout(() => window.location.href = res.data.redirect || '<?= site_url('/dashboard') ?>', 600);
            } else {
                // Jika error database (500), arahkan user ke /setup.
                const isDbError = res.status === 500 && (res.message || '').includes('Database belum siap');
                if (isDbError) {
                    toast(res.message + ' Klik di sini untuk setup otomatis.', 'error', 8000);
                    // Tampilkan banner setup.
                    let banner = document.getElementById('setup-banner');
                    if (!banner) {
                        banner = document.createElement('div');
                        banner.id = 'setup-banner';
                        banner.className = 'glass-soft px-4 py-3 mb-5 text-sm border border-amber-500/40 text-amber-200';
                        banner.innerHTML = '⚠ Database belum siap. <a href="<?= site_url('/setup') ?>" class="text-amber-300 underline font-medium">Jalankan setup otomatis →</a>';
                        document.querySelector('main .glass').prepend(banner);
                    }
                } else {
                    toast(res.message || 'Login gagal.', 'error');
                }
            }
        });
    </script>
</body>
</html>
