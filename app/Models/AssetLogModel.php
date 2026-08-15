<?php

namespace App\Models;

use CodeIgniter\Model;

class AssetLogModel extends Model
{
    protected $table            = 'asset_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = ['asset_id', 'user_id', 'action', 'note'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // hanya created_at

    /**
     * Riwayat sebuah aset + nama user.
     */
    public function logsForAsset(int $assetId): array
    {
        return $this->builder()
            ->select('asset_logs.*, users.name AS user_name')
            ->join('users', 'users.id = asset_logs.user_id', 'left')
            ->where('asset_logs.asset_id', $assetId)
            ->orderBy('asset_logs.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }
}
