<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * AuthFilter
 *
 * Memastikan user sudah login sebelum mengakses route terproteksi.
 * Mendukung JSON request (API) maupun request halaman biasa.
 */
class AuthFilter implements FilterInterface
{
    /**
     * Sebelum request diproses.
     *
     * @param array|null $arguments
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (! $session->get('isLoggedIn')) {
            if ($request->isAJAX() || str_contains((string) service('uri')->getPath(), 'api/')) {
                return service('response')
                    ->setStatusCode(401)
                    ->setJSON(['status' => 'error', 'message' => 'Sesi berakhir. Silakan login kembali.']);
            }

            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }
    }

    /**
     * Setelah request diproses.
     *
     * @param array|null $arguments
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada post-processing.
    }
}
