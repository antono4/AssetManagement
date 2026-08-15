<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController
 *
 * Menyediakan helper & service umum yang dipakai semua controller aplikasi.
 */
abstract class BaseController extends Controller
{
    /**
     * Helper yang dimuat otomatis.
     */
    protected $helpers = ['form', 'url', 'text'];

    /**
     * Session instance.
     */
    protected $session;

    /**
     * Data user yang sedang login (dari session).
     */
    protected array $currentUser = [];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);

        $this->session = session();

        if ($this->session->get('isLoggedIn')) {
            $this->currentUser = [
                'id'       => $this->session->get('userId'),
                'name'     => $this->session->get('userName'),
                'username' => $this->session->get('userUsername'),
                'role'     => $this->session->get('userRole'),
            ];
        }
    }

    /**
     * Apakah user saat ini admin?
     */
    protected function isAdmin(): bool
    {
        return ($this->currentUser['role'] ?? '') === 'admin';
    }

    /**
     * Tolak akses non-admin dengan JSON 403 (API) atau redirect (web).
     */
    protected function denyUnlessAdmin()
    {
        if ($this->isAdmin()) {
            return null;
        }

        if (str_contains((string) service('uri')->getPath(), 'api/')) {
            return service('response')
                ->setStatusCode(403)
                ->setJSON(['status' => 'error', 'message' => 'Akses ditolak. Halaman ini khusus Admin.']);
        }

        return redirect()->to('/dashboard')->with('error', 'Akses ditolak. Halaman ini khusus Admin.');
    }

    /**
     * Kirim response JSON standar.
     */
    protected function respond(array $data, int $status = 200)
    {
        return $this->response->setStatusCode($status)->setJSON($data);
    }
}
