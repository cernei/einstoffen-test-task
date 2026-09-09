<?php

namespace App\Controllers;

use App\Jobs\ShipmentJob;
use CodeIgniter\HTTP\ResponseInterface;

class JobTest extends BaseController
{
    public function shipment($case): ResponseInterface
    {
        $job = new ShipmentJob($case);
        $job->handle();

        return $this->response->setStatusCode(201)->setJSON([
            'message' => 'Job ran successfully.',
        ]);
    }

}