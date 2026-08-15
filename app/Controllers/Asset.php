<?php

namespace App\Controllers;

use App\Models\AssetLogModel;
use App\Models\AssetModel;
use App\Models\CategoryModel;

/**
 * Asset Controller
 *
 * Halaman modul aset + endpoint JSON CRUD (AJAX/SPA).
 */
class Asset extends BaseController
{
    /**
     * Render halaman modul aset (SPA shell).
     */
    public function index()
    {
        return view('layouts/app', [
            'title'   => 'Manajemen Aset — Asset Management',
            'page'    => 'assets',
            'user'    => $this->currentUser,
            'isAdmin' => $this->isAdmin(),
        ]);
    }

    /**
     * API: daftar semua aset (dengan nama kategori).
     */
    public function list()
    {
        $model = new AssetModel();

        return $this->respond([
            'status' => 'success',
            'data'   => $model->listWithCategory(),
        ]);
    }

    /**
     * API: detail satu aset.
     */
    public function show(int $id)
    {
        $model = new AssetModel();
        $asset = $model->findWithCategory($id);

        if (! $asset) {
            return $this->respond(['status' => 'error', 'message' => 'Aset tidak ditemukan.'], 404);
        }

        return $this->respond(['status' => 'success', 'data' => $asset]);
    }

    /**
     * API: tambah aset (Admin only).
     */
    public function create()
    {
        if ($guard = $this->denyUnlessAdmin()) {
            return $guard;
        }

        $model = new AssetModel();
        $data  = $this->collectInput();

        if (! $model->insert($data)) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Validasi gagal.',
                'errors'  => $model->errors(),
            ], 422);
        }

        // Catat log pembuatan.
        $this->logAction($model->getInsertID(), 'created', 'Aset ditambahkan.');

        return $this->respond([
            'status'  => 'success',
            'message' => 'Aset berhasil ditambahkan.',
            'id'      => $model->getInsertID(),
        ], 201);
    }

    /**
     * API: update aset (Admin only).
     */
    public function update(int $id)
    {
        if ($guard = $this->denyUnlessAdmin()) {
            return $guard;
        }

        $model = new AssetModel();
        $asset = $model->find($id);

        if (! $asset) {
            return $this->respond(['status' => 'error', 'message' => 'Aset tidak ditemukan.'], 404);
        }

        $data = $this->collectInput();

        if (! $model->update($id, $data)) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Validasi gagal.',
                'errors'  => $model->errors(),
            ], 422);
        }

        // Catat log perubahan status bila berubah.
        if (isset($data['status']) && $data['status'] !== $asset['status']) {
            $this->logAction($id, 'status_update', "Status: {$asset['status']} → {$data['status']}");
        } else {
            $this->logAction($id, 'updated', 'Data aset diperbarui.');
        }

        return $this->respond(['status' => 'success', 'message' => 'Aset berhasil diperbarui.']);
    }

    /**
     * API: hapus aset (Admin only).
     */
    public function delete(int $id)
    {
        if ($guard = $this->denyUnlessAdmin()) {
            return $guard;
        }

        $model = new AssetModel();

        if (! $model->find($id)) {
            return $this->respond(['status' => 'error', 'message' => 'Aset tidak ditemukan.'], 404);
        }

        $this->logAction($id, 'deleted', 'Aset dihapus.');
        $model->delete($id);

        return $this->respond(['status' => 'success', 'message' => 'Aset berhasil dihapus.']);
    }

    /**
     * API: riwayat log sebuah aset.
     */
    public function logs(int $id)
    {
        $logModel = new AssetLogModel();

        return $this->respond([
            'status' => 'success',
            'data'   => $logModel->logsForAsset($id),
        ]);
    }

    // ------------------------------------------------------------------

    /**
     * Kumpulkan input dari form-POST atau JSON body.
     */
    private function collectInput(): array
    {
        $isJson = str_contains($this->request->getHeaderLine('Content-Type'), 'application/json');
        if ($isJson) {
            $json = $this->request->getJSON(true) ?? [];
            $fields = ['asset_code', 'name', 'category_id', 'brand_spec', 'location', 'status', 'purchase_date', 'price'];
            $data   = [];
            foreach ($fields as $f) {
                if (array_key_exists($f, $json)) {
                    $data[$f] = $json[$f];
                }
            }

            return $data;
        }

        return $this->request->getPost(['asset_code', 'name', 'category_id', 'brand_spec', 'location', 'status', 'purchase_date', 'price']);
    }

    /**
     * Catat log aksi aset.
     */
    private function logAction(int $assetId, string $action, string $note): void
    {
        $logModel = new AssetLogModel();
        $logModel->insert([
            'asset_id' => $assetId,
            'user_id'  => $this->currentUser['id'] ?? null,
            'action'   => $action,
            'note'     => $note,
        ]);
    }
}
