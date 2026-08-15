<?php

namespace App\Controllers;

use App\Models\AssetModel;
use App\Models\CategoryModel;

/**
 * Report Controller
 *
 * Menyajikan halaman laporan + endpoint JSON untuk filter & print.
 */
class Report extends BaseController
{
    /**
     * Render halaman laporan (SPA shell).
     */
    public function index()
    {
        return view('layouts/app', [
            'title'   => 'Laporan — Asset Management',
            'page'    => 'reports',
            'user'    => $this->currentUser,
            'isAdmin' => $this->isAdmin(),
        ]);
    }

    /**
     * API: data laporan aset (filter berdasarkan rentang tanggal & kategori).
     *
     * Query: GET /api/reports/assets?start=2023-01-01&end=2023-12-31&category_id=2
     */
    public function assetsReport()
    {
        $start      = $this->request->getGet('start');
        $end        = $this->request->getGet('end');
        $categoryId = $this->request->getGet('category_id');

        $assetModel = new AssetModel();
        $rows       = $assetModel->filterReport($start, $end, $categoryId ? (int) $categoryId : null);

        // Ringkasan.
        $summary = [
            'total_records'  => count($rows),
            'total_value'    => array_sum(array_column($rows, 'price')),
            'tersedia'       => count(array_filter($rows, fn ($r) => $r['status'] === 'tersedia')),
            'dipinjam'       => count(array_filter($rows, fn ($r) => $r['status'] === 'dipinjam')),
            'rusak'          => count(array_filter($rows, fn ($r) => $r['status'] === 'rusak')),
        ];

        $catModel = new CategoryModel();

        return $this->respond([
            'status'     => 'success',
            'filters'    => ['start' => $start, 'end' => $end, 'category_id' => $categoryId],
            'summary'    => $summary,
            'categories' => $catModel->orderBy('name', 'ASC')->findAll(),
            'data'       => $rows,
        ]);
    }
}
