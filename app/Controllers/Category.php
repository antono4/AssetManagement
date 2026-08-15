<?php

namespace App\Controllers;

use App\Models\CategoryModel;

/**
 * Category Controller
 *
 * Halaman modul kategori + endpoint JSON CRUD (Admin only untuk tulis).
 */
class Category extends BaseController
{
    /**
     * Render halaman modul kategori (SPA shell).
     */
    public function index()
    {
        return view('layouts/app', [
            'title'   => 'Kategori — Asset Management',
            'page'    => 'categories',
            'user'    => $this->currentUser,
            'isAdmin' => $this->isAdmin(),
        ]);
    }

    /**
     * API: daftar kategori (+ jumlah aset).
     */
    public function list()
    {
        $model = new CategoryModel();

        return $this->respond([
            'status' => 'success',
            'data'   => $model->listWithCounts(),
        ]);
    }

    /**
     * API: tambah kategori (Admin only).
     */
    public function create()
    {
        if ($guard = $this->denyUnlessAdmin()) {
            return $guard;
        }

        $model = new CategoryModel();
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
            'message' => 'Kategori berhasil ditambahkan.',
            'id'      => $model->getInsertID(),
        ], 201);
    }

    /**
     * API: update kategori (Admin only).
     */
    public function update(int $id)
    {
        if ($guard = $this->denyUnlessAdmin()) {
            return $guard;
        }

        $model = new CategoryModel();

        if (! $model->find($id)) {
            return $this->respond(['status' => 'error', 'message' => 'Kategori tidak ditemukan.'], 404);
        }

        $data = $this->collectInput();

        if (! $model->update($id, $data)) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Validasi gagal.',
                'errors'  => $model->errors(),
            ], 422);
        }

        return $this->respond(['status' => 'success', 'message' => 'Kategori berhasil diperbarui.']);
    }

    /**
     * API: hapus kategori (Admin only).
     */
    public function delete(int $id)
    {
        if ($guard = $this->denyUnlessAdmin()) {
            return $guard;
        }

        $model = new CategoryModel();
        $asset = model('AssetModel');

        // Cegah hapus kategori yang masih dipakai aset.
        if ($asset->where('category_id', $id)->countAllResults() > 0) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Kategori tidak bisa dihapus karena masih digunakan oleh aset.',
            ], 409);
        }

        if (! $model->find($id)) {
            return $this->respond(['status' => 'error', 'message' => 'Kategori tidak ditemukan.'], 404);
        }

        $model->delete($id);

        return $this->respond(['status' => 'success', 'message' => 'Kategori berhasil dihapus.']);
    }

    // ------------------------------------------------------------------

    private function collectInput(): array
    {
        $isJson = str_contains($this->request->getHeaderLine('Content-Type'), 'application/json');
        if ($isJson) {
            $json = $this->request->getJSON(true) ?? [];
            $data = [];
            if (array_key_exists('name', $json)) {
                $data['name'] = $json['name'];
            }
            if (array_key_exists('description', $json)) {
                $data['description'] = $json['description'];
            }

            return $data;
        }

        return $this->request->getPost(['name', 'description']);
    }
}
