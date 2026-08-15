<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table            = 'categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = ['name', 'description'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // tabel categories hanya punya created_at

    protected $validationRules = [
        'name'        => 'required|min_length[2]|max_length[80]|is_unique[categories.name,id,{id}]',
        'description' => 'permit_empty|max_length[255]',
    ];

    /**
     * Ambil semua kategori + jumlah aset per kategori.
     */
    public function listWithCounts(): array
    {
        $builder = $this->builder();
        $builder->select('categories.id, categories.name, categories.description, COUNT(assets.id) AS asset_count')
                ->join('assets', 'assets.category_id = categories.id', 'left')
                ->groupBy('categories.id')
                ->orderBy('categories.name', 'ASC');

        return $builder->get()->getResultArray();
    }
}
