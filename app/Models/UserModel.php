<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = ['name', 'username', 'email', 'password', 'role', 'is_active'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'name'     => 'required|min_length[3]|max_length[100]',
        'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username,id,{id}]',
        'email'    => 'permit_empty|valid_email|max_length[120]',
        'password' => 'required|min_length[6]',
        'role'     => 'required|in_list[admin,staff]',
    ];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    /**
     * Ambil user berdasarkan username (untuk login).
     */
    public function findByUsername(string $username): ?array
    {
        return $this->where('username', $username)->first();
    }

    /**
     * Statistik ringan: total user per role.
     */
    public function countByRole(): array
    {
        $result = $this->select('role, COUNT(*) AS total')
            ->groupBy('role')
            ->get()
            ->getResultArray();

        $out = ['admin' => 0, 'staff' => 0];
        foreach ($result as $row) {
            $out[$row['role']] = (int) $row['total'];
        }

        return $out;
    }
}
