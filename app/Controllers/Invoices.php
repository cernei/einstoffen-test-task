<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class Invoices extends BaseController
{
    public function store(): ResponseInterface
    {
        $payload = $this->request->getJSON(true);

        $rules = [
            'event'          => 'required',
            'invoice_id'     => 'required|is_unique[invoices.invoice_id]',
            'customer'       => 'required',
            'amount'         => 'required|decimal',
            'currency'       => 'required|in_list[USD,EUR,CHF]',
            'status'         => 'required',
            'due_date'       => 'required|valid_date[Y-m-d]',
            'created_at'     => 'required',
            'customer.id'    => 'required',
            'customer.name'  => 'required',
            'customer.email' => 'required|valid_email',
        ];

        $validation = service('validation');
        $validation->setRules($rules);

        if (! $validation->run($payload)) {
            return $this->response->setStatusCode(422)->setJSON([
                'error'  => 'Validation failed.',
                'errors' => $validation->getErrors(),
            ]);
        }

        $db = db_connect();
        $db->table('event_log')->insert([
            'name'    => 'invoice.created',
            'payload' => json_encode($payload),
        ]);
        $db->table('invoices')->insert([
            'invoice_id'       => $payload['invoice_id'],
            'customer_id'      => $payload['customer']['id'],
            'customer_name'    => $payload['customer']['name'],
            'customer_email'   => $payload['customer']['email'],
            'amount'           => $payload['amount'],
            'currency'         => $payload['currency'],
            'status'           => $payload['status'],
            'due_date'         => $payload['due_date'],
        ]);

        if ($db->error()['code'] !== 0) {

            return $this->response->setStatusCode(500)->setJSON([
                'error' => 'Unable to store invoice event.',
            ]);
        }

        return $this->response->setStatusCode(201)->setJSON([
            'message' => 'Invoice event stored.',
            'id'      => $db->insertID(),
        ]);
    }

}