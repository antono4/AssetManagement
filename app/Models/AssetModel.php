<?php

namespace App\Models;

use CodeIgniter\Model;

class AssetModel extends Model
{
    protected $table            = 'assets';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'asset_code', 'name', 'category_id', 'brand_spec',
        'location', 'status', 'purchase_date', 'price',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'asset_code'    => 'required|min_length[3]|max_length[30]|is_unique[assets.asset_code,id,{id}]',
        'name'          => 'required|min_length[2]|max_length[120]',
        'category_id'   => 'required|integer',
        'brand_spec'    => 'permit_empty|max_length[180]',
        'location'      => 'permit_empty|max_length[120]',
        'status'        => 'required|in_list[tersedia,dipinjam,rusak]',
        'purchase_date' => 'permit_empty|valid_date[Y-m-d]',
        'price'         => 'permit_empty|numeric',
    ];

    /**
     * Ambil semua aset + nama kategori (JOIN).
     */
    public function listWithCategory(): array
    {
        return $this->builder()
            ->select('assets.*, categories.name AS category_name')
            ->join('categories', 'categories.id = assets.category_id', 'left')
            ->orderBy('assets.id', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Ambil satu aset + nama kategori.
     */
    public function findWithCategory(int $id): ?array
    {
        return $this->builder()
            ->select('assets.*, categories.name AS category_name')
            ->join('categories', 'categories.id = assets.category_id', 'left')
            ->where('assets.id', $id)
            ->get()
            ->getRowArray();
    }

    /**
     * Statistik status untuk dashboard.
     */
    public function countByStatus(): array
    {
        $rows = $this->select('status, COUNT(*) AS total')
            ->groupBy('status')
            ->get()
            ->getResultArray();

        $out = ['tersedia' => 0, 'dipinjam' => 0, 'rusak' => 0];
        foreach ($rows as $row) {
            $out[$row['status']] = (int) $row['total'];
        }

        return $out;
    }

    /**
     * Distribusi aset per kategori (untuk chart dashboard).
     */
    public function countByCategory(): array
    {
        return $this->builder()
            ->select('categories.name AS category, COUNT(assets.id) AS total')
            ->join('categories', 'categories.id = assets.category_id', 'left')
            ->groupBy('categories.id')
            ->orderBy('total', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Total nilai aset.
     */
    public function totalValue(): float
    {
        $row = $this->select('COALESCE(SUM(price),0) AS total')->get()->getRowArray();

        return (float) ($row['total'] ?? 0);
    }

    /**
     * Filter aset berdasarkan tanggal & kategori (untuk laporan).
     */
    public function filterReport(?string $start, ?string $end, ?int $categoryId): array
    {
        $builder = $this->builder();
        $builder->select('assets.*, categories.name AS category_name')
                ->join('categories', 'categories.id = assets.category_id', 'left');

        if (! empty($start)) {
            $builder->where('assets.purchase_date >=', $start);
        }
        if (! empty($end)) {
            $builder->where('assets.purchase_date <=', $end);
        }
        if (! empty($categoryId)) {
            $builder->where('assets.category_id', (int) $categoryId);
        }

        $builder->orderBy('assets.purchase_date', 'DESC');

        return $builder->get()->getResultArray();
    }
}
