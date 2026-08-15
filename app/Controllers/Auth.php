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

        $model   = new UserModel();
        $user    = $model->findByUsername($username);
        $isAjax  = $this->request->isAJAX();

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
     * Set password benar, NONAKTIFKAN route setup di app/Config/Routes.php.
     */
    public function setupPassword()
    {
        $model = new UserModel();
        $done  = [];

        foreach (['admin' => 'admin123', 'staff' => 'staff123'] as $username => $plain) {
            $user = $model->findByUsername($username);
            if ($user) {
                $model->update($user['id'], ['password' => password_hash($plain, PASSWORD_BCRYPT)]);
                $done[] = "$username => $plain";
            }
        }

        $msg = 'Password telah direset. Login dengan: ' . implode(' | ', $done)
             . ' — SILAKAN HAPUS route setup di app/Config/Routes.php setelah ini.';

        if ($this->request->isAJAX() || strtolower($this->request->getMethod()) === 'post') {
            return $this->respond(['status' => 'success', 'message' => $msg]);
        }

        return '<!doctype html><meta charset="utf-8"><title>Setup</title>'
             . '<body style="font-family:sans-serif;padding:40px;background:#0b1020;color:#e5e7eb">'
             . '<h2>Setup Password Selesai</h2><p>' . htmlspecialchars($msg) . '</p>'
             . '<p><a href="/login" style="color:#60a5fa">Ke halaman login →</a></p></body>';
    }
}
