<?php

namespace App\Controllers;

use App\Jobs\ShipmentJob;
use App\Models\Invoice;
use CodeIgniter\HTTP\ResponseInterface;

class Invoices extends BaseController
{
    public function store(): ResponseInterface
    {
        $payload = $this->request->getJSON(true);

        $rules = [
            'event' => 'required|in_list[invoice.created]',
            'invoice_id' => 'required',
            'customer' => 'required',
            'amount' => 'required|decimal',
            'currency' => 'required|in_list[USD,EUR,CHF]',
            'status' => 'required',
            'due_date' => 'required|valid_date[Y-m-d]',
            'created_at' => 'required',
            'customer.id' => 'required',
            'customer.name' => 'required',
            'customer.email' => 'required|valid_email',
            'comment' => 'required',
        ];

        $validation = service('validation');
        $validation->setRules($rules);

        if (!$validation->run($payload)) {
            return $this->response->setStatusCode(422)->setJSON([
                'error' => 'Validation failed.',
                'errors' => $validation->getErrors(),
            ]);
        }

        $db = db_connect();
        $invoiceModel = new Invoice();

        $existing = $invoiceModel->where('invoice_id', $payload['invoice_id'])->first();

        if ($existing) {
            return $this->response->setStatusCode(200)->setJSON([
                'status' => 'success',
                'message' => 'Invoice event already processed (duplicate webhook).'
            ]);
        }

        try {
            $db->transStart();

            $invoiceId = $invoiceModel->insert([
                'invoice_id' => $payload['invoice_id'],
                'customer_id' => $payload['customer']['id'],
                'customer_name' => $payload['customer']['name'],
                'customer_email' => $payload['customer']['email'],
                'amount' => $payload['amount'],
                'currency' => $payload['currency'],
                'comment' => $payload['comment'],
                'status' => $payload['status'],
                'due_date' => $payload['due_date'],
                'shipping_status' => ShipmentJob::PENDING,
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Database transaction failed.');
            }
        } catch (\Throwable $exception) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'An internal server error occurred.'
            ]);
        }

        service('queue')->push('shipments', 'shipment', [
            'invoice_id' => $payload['invoice_id'],
        ]);

        return $this->response->setStatusCode(200)->setJSON([
            'status' => 'success',
            'invoice_id' => $invoiceId
        ]);
    }
}