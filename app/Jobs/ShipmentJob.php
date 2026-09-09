<?php

namespace App\Jobs;

class ShipmentJob
{

    public function __construct(private readonly string $case)
    {
    }

    public function handle(): void
    {

        $db = db_connect();

        $invoice = $db->table('invoices')
            ->where('shipping_status', '')
            ->get()
            ->getFirstRow('array');

        if ($invoice === null) {
            return;
        }
        $db->table('event_log')
            ->insert([
                'name' => 'shipping.job_started',
                'payload' => 'Invoice: ' . $invoice['invoice_id']
            ]);

        $result = service('curlrequest')->get(getenv('SHIPMENT_ENDPOINT') . '?case='. $this->case, [
            'http_errors' => false,
            'json'        => [
                'invoice_id' => $invoice['invoice_id'],
                'amount'     => $invoice['amount'],
                'name'       => $invoice['customer_name'],
                'email'      => $invoice['customer_email'],
            ],
        ]);
        $db->table('event_log')
            ->insert([
                'name' => 'shipping.job_ended',
                'payload' => 'Invoice: ' . $invoice['invoice_id']. "\n" . $result->getBody()
            ]);

        $payload = json_decode($result->getBody(), true);
        $serviceStatus = is_array($payload) ? strtolower((string) ($payload['status'] ?? '')) : '';

        $shippingStatus = match ($serviceStatus) {
            'ok'      => 'shipped',
            'error', 'timeout' => 'error',
            default   => $result->getStatusCode() >= 200 && $result->getStatusCode() < 300
                ? 'shipped'
                : 'error',
        };

        $shippingReference = is_array($payload)
            ? ($payload['reference'] ?? "")
            : null;

        $db->table('invoices')
            ->where('id', $invoice['id'])
            ->update([
                'shipping_status'            => $shippingStatus,
                'shipping_reference'         => $shippingReference,
                'shipping_status_updated_at' => date('Y-m-d H:i:s'),
            ]);
    }
}
