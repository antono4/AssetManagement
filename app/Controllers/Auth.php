<?php

namespace App\Controllers;

use App\Models\UserModel;

/**
 * Auth Controller
 *
 * Mengelola login, logout, dan setup password awal (one-time fixer).
 */
class Auth extends BaseController
{
    /**
     * Halaman login.
     */
    public function index()
    {
        // Jika sudah login, langsung ke dashboard.
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login', [
            'title'  => 'Login — Asset Management',
            'error'  => session('error'),
            'old'    => session('old'),
        ]);
    }

    /**
     * Proses login (menerima form POST biasa maupun AJAX JSON).
     */
    public function attempt()
    {
        $isJson = str_contains($this->request->getHeaderLine('Content-Type'), 'application/json');
        $body   = $isJson ? ($this->request->getJSON(true) ?? []) : $this->request->getPost();
        $username = $body['username'] ?? '';
        $password = $body['password'] ?? '';

        $isAjax = $this->request->isAJAX();

        try {
            $model = new UserModel();
            $user  = $model->findByUsername($username);
        } catch (\Throwable $e) {
            // Database belum disetup / koneksi gagal.
            $msg = 'Database belum siap. Jalankan import assets_app.sql lalu akses /setup '
                 . 'untuk menginisialisasi password. Detail: ' . $e->getMessage();

            if ($isAjax) {
                return $this->respond(['status' => 'error', 'message' => $msg], 500);
            }

            return redirect()->back()->withInput()->with('error', $msg);
        }

        // Validasi user & password.
        if (! $user || ! $user['is_active'] || ! password_verify($password, $user['password'])) {
            if ($isAjax) {
                return $this->respond(['status' => 'error', 'message' => 'Username atau password salah.'], 401);
            }

            return redirect()->back()->withInput()->with('error', 'Username atau password salah.');
        }

        // Set session.
        $this->session->set([
            'isLoggedIn'  => true,
            'userId'      => $user['id'],
            'userName'    => $user['name'],
            'userUsername'=> $user['username'],
            'userRole'    => $user['role'],
        ]);

        if ($isAjax) {
            return $this->respond([
                'status'  => 'success',
                'message' => 'Login berhasil.',
                'redirect'=> site_url('/dashboard'),
            ]);
        }

        return redirect()->to('/dashboard')->with('success', 'Selamat datang, ' . $user['name'] . '!');
    }

    /**
     * Logout.
     */
    public function logout()
    {
        $this->session->destroy();

        if ($this->request->isAJAX()) {
            return $this->respond(['status' => 'success', 'message' => 'Anda telah keluar.']);
        }

        return redirect()->to('/login')->with('success', 'Anda telah keluar.');
    }

    /**
     * Setup password awal.
     *
     * Karena hash BCrypt pada file SQL hanya placeholder, jalankan endpoint ini
     * satu kali untuk mengubah password admin/staff menjadi hash yang valid:
     *   admin  -> admin123
     *   staff  -> staff123
     *
     * Endpoint ini juga otomatis import assets_app.sql jika tabel users belum ada
     * (membantu user yang lupa import SQL manual).
     *
     * Set password benar, NONAKTIFKAN route setup di app/Config/Routes.php.
     */
    public function setupPassword()
    {
        $done  = [];
        $notes = [];

        try {
            $model = new UserModel();

            // Cek apakah tabel users sudah ada; jika belum, auto-import SQL.
            $users = $model->findAll();

            if (empty($users)) {
                // Tabel ada tapi kosong -> import SQL.
                $notes[] = 'Tabel users kosong. Mengimpor assets_app.sql ...';
                $this->importSqlFile(ROOTPATH . 'assets_app.sql');
                $this->importSqlFile(ROOTPATH . 'assets_sample_data.sql');
                $notes[] = 'Import selesai.';
            }

            foreach (['admin' => 'admin123', 'staff' => 'staff123'] as $username => $plain) {
                $user = $model->findByUsername($username);
                if ($user) {
                    $model->update($user['id'], ['password' => password_hash($plain, PASSWORD_BCRYPT)]);
                    $done[] = "$username => $plain";
                }
            }
        } catch (\Throwable $e) {
            // Tabel belum ada -> auto-import lalu retry.
            $notes[] = 'Tabel belum ditemukan. Mengimpor assets_app.sql ...';

            try {
                $this->importSqlFile(ROOTPATH . 'assets_app.sql');
                $this->importSqlFile(ROOTPATH . 'assets_sample_data.sql');
                $notes[] = 'Import selesai. Mengatur password...';

                $model = new UserModel();
                foreach (['admin' => 'admin123', 'staff' => 'staff123'] as $username => $plain) {
                    $user = $model->findByUsername($username);
                    if ($user) {
                        $model->update($user['id'], ['password' => password_hash($plain, PASSWORD_BCRYPT)]);
                        $done[] = "$username => $plain";
                    }
                }
            } catch (\Throwable $e2) {
                $msg = 'Gagal setup. Pastikan database & koneksi MySQL benar di .env, '
                     . 'lalu import manual: mysql asset_db < assets_app.sql. Error: ' . $e2->getMessage();

                if ($this->request->isAJAX() || strtolower($this->request->getMethod()) === 'post') {
                    return $this->respond(['status' => 'error', 'message' => $msg], 500);
                }

                return $this->renderSetupPage('Setup Gagal', $msg, true);
            }
        }

        $msg = 'Password telah direset. Login dengan: ' . implode(' | ', $done)
             . ' — SILAKAN HAPUS route setup di app/Config/Routes.php setelah ini.';

        if (! empty($notes)) {
            $msg = implode(' ', $notes) . ' ' . $msg;
        }

        if ($this->request->isAJAX() || strtolower($this->request->getMethod()) === 'post') {
            return $this->respond(['status' => 'success', 'message' => $msg]);
        }

        return $this->renderSetupPage('Setup Password Selesai', $msg);
    }

    /**
     * Import file SQL ke database aktif via mysqli multi_query.
     */
    private function importSqlFile(string $path): void
    {
        if (! is_file($path)) {
            return;
        }

        $sql = file_get_contents($path);

        // Hapus komentar baris (-- ...) dan baris kosong agar multi_query tidak error.
        $sql = preg_replace('/^\s*--.*$/m', '', $sql);
        $sql = preg_replace('/^\s*\/\*.*?\*\/\s*$/ms', '', $sql);

        /** @var \CodeIgniter\Database\MySQLi\Connection $db */
        $db   = \Config\Database::connect();
        $conn = $db->mysqli ?? null; // property public mysqli pada MySQLi\Connection

        if (! $conn instanceof \mysqli) {
            // Fallback: eksekusi per-statement via query().
            $this->importSqlFallback($sql);

            return;
        }

        // multi_query butuh titik koma di akhir.
        $sql = trim($sql);
        if (! str_ends_with($sql, ';')) {
            $sql .= ';';
        }

        $result = $conn->multi_query($sql);

        // Konsumsi semua resultset agar koneksi bersih untuk query berikutnya.
        while ($conn->more_results()) {
            $conn->next_result();
        }

        if ($result === false && $conn->errno) {
            throw new \RuntimeException('SQL import error: ' . $conn->error);
        }
    }

    /**
     * Fallback import: eksekusi statement satu per satu (kurang andal untuk
     * string yang mengandung titik koma, tapi cukup untuk skema sederhana).
     */
    private function importSqlFallback(string $sql): void
    {
        $db      = \Config\Database::connect();
        $queries = array_filter(array_map('trim', explode(';', $sql)));

        foreach ($queries as $query) {
            if ($query === '') {
                continue;
            }
            $db->query($query);
        }
    }

    /**
     * Render halaman setup sederhana.
     */
    private function renderSetupPage(string $title, string $msg, bool $isError = false): string
    {
        $color   = $isError ? '#fca5a5' : '#86efac';
        $icon    = $isError ? '⚠' : '✓';

        return '<!doctype html><meta charset="utf-8"><title>Setup</title>'
             . '<body style="font-family:Inter,sans-serif;padding:40px;background:#0b1020;color:#e5e7eb;min-height:100vh;display:flex;align-items:center;justify-content:center">'
             . '<div style="max-width:560px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.12);border-radius:16px;padding:32px">'
             . '<h2 style="margin:0 0 12px;color:#fff">' . $icon . ' ' . htmlspecialchars($title) . '</h2>'
             . '<p style="line-height:1.6;color:' . $color . '">' . htmlspecialchars($msg) . '</p>'
             . '<p style="margin-top:20px"><a href="/login" style="color:#60a5fa;text-decoration:none">Ke halaman login →</a></p>'
             . '</div></body>';
    }
}
