<?php

namespace App\Controllers;

use App\Models\AssetModel;
use App\Models\CategoryModel;
use App\Models\UserModel;

/**
 * Dashboard Controller
 *
 * Menampilkan halaman utama (SPA shell) & endpoint JSON statistik.
 */
class Dashboard extends BaseController
{
    /**
     * Render halaman dashboard (SPA shell).
     */
    public function index()
    {
        return view('layouts/app', [
            'title'      => 'Dashboard — Asset Management',
            'page'       => 'dashboard',
            'user'       => $this->currentUser,
            'isAdmin'    => $this->isAdmin(),
        ]);
    }

    /**
     * API: statistik ringkas untuk dashboard.
     */
    public function stats()
    {
        $assetModel    = new AssetModel();
        $categoryModel = new CategoryModel();
        $userModel     = new UserModel();

        $statusCount = $assetModel->countByStatus();
        $total       = array_sum($statusCount);

        return $this->respond([
            'status' => 'success',
            'data'   => [
                'total_assets'    => $total,
                'tersedia'        => $statusCount['tersedia'],
                'dipinjam'        => $statusCount['dipinjam'],
                'rusak'           => $statusCount['rusak'],
                'total_value'     => $assetModel->totalValue(),
                'total_users'     => $userModel->countAll(),
                'users_by_role'   => $userModel->countByRole(),
                'by_category'     => $assetModel->countByCategory(),
                'recent_assets'   => $assetModel->orderBy('id', 'DESC')->findAll(5),
            ],
        ]);
    }
}
