<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class JobTest extends BaseController
{
    public function shipment(): ResponseInterface
    {
        command('queue:work shipments -max-jobs 1 --stop-when-empty');

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Processed pending jobs in the shipments queue.'
        ]);
    }
}