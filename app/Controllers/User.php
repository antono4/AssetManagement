<?php

namespace App\Controllers;

use App\Models\UserModel;

/**
 * User Controller
 *
 * Halaman modul user + endpoint JSON CRUD. Seluruh modul khusus Admin.
 */
class User extends BaseController
{
    /**
     * Render halaman modul user (Admin only).
     */
    public function index()
    {
        if ($guard = $this->denyUnlessAdmin()) {
            return $guard;
        }

        return view('layouts/app', [
            'title'   => 'Manajemen User — Asset Management',
            'page'    => 'users',
            'user'    => $this->currentUser,
            'isAdmin' => $this->isAdmin(),
        ]);
    }

    /**
     * API: daftar user.
     */
    public function list()
    {
        if ($guard = $this->denyUnlessAdmin()) {
            return $guard;
        }

        $model = new UserModel();
        $users = $model->orderBy('id', 'ASC')->findAll();

        // Jangan kembalikan password.
        foreach ($users as &$u) {
            unset($u['password']);
        }

        return $this->respond(['status' => 'success', 'data' => $users]);
    }

    /**
     * API: tambah user.
     */
    public function create()
    {
        if ($guard = $this->denyUnlessAdmin()) {
            return $guard;
        }

        $model = new UserModel();
        $data  = $this->collectInput();

        if (! $model->insert($data)) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Validasi gagal.',
                'errors'  => $model->errors(),
            ], 422);
        }

        return $this->respond([
            'status'  => 'success',
            'message' => 'User berhasil ditambahkan.',
            'id'      => $model->getInsertID(),
        ], 201);
    }

    /**
     * API: update user.
     */
    public function update(int $id)
    {
        if ($guard = $this->denyUnlessAdmin()) {
            return $guard;
        }

        $model = new UserModel();

        if (! $model->find($id)) {
            return $this->respond(['status' => 'error', 'message' => 'User tidak ditemukan.'], 404);
        }

        $data = $this->collectInput();

        // Password opsional saat update.
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        if (! $model->update($id, $data)) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Validasi gagal.',
                'errors'  => $model->errors(),
            ], 422);
        }

        return $this->respond(['status' => 'success', 'message' => 'User berhasil diperbarui.']);
    }

    /**
     * API: hapus user.
     */
    public function delete(int $id)
    {
        if ($guard = $this->denyUnlessAdmin()) {
            return $guard;
        }

        // Cegah admin menghapus dirinya sendiri.
        if ($id === (int) $this->currentUser['id']) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Anda tidak dapat menghapus akun yang sedang digunakan.',
            ], 409);
        }

        $model = new UserModel();

        if (! $model->find($id)) {
            return $this->respond(['status' => 'error', 'message' => 'User tidak ditemukan.'], 404);
        }

        $model->delete($id);

        return $this->respond(['status' => 'success', 'message' => 'User berhasil dihapus.']);
    }

    // ------------------------------------------------------------------

    private function collectInput(): array
    {
        $isJson = str_contains($this->request->getHeaderLine('Content-Type'), 'application/json');
        $fields = ['name', 'username', 'email', 'password', 'role', 'is_active'];

        if ($isJson) {
            $json = $this->request->getJSON(true) ?? [];
            $data = [];
            foreach ($fields as $f) {
                if (array_key_exists($f, $json)) {
                    $data[$f] = $json[$f];
                }
            }

            // Hash password baru bila ada.
            if (! empty($data['password'])) {
                $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
            }

            return $data;
        }

        $data = $this->request->getPost($fields);

        if (! empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        return $data;
    }
}
