<?php

namespace App\Jobs;

use App\Models\EventLog;
use App\Models\Invoice;
use CodeIgniter\Queue\BaseJob;
use Config\Services;
use App\Exceptions\ShippingValidationException;
use RuntimeException;

class ShipmentJob extends BaseJob
{
    protected int $tries = 3;
    protected int $retryAfter = 60;

    public const PENDING = 'PENDING';
    public const VALIDATION_ERROR = 'VALIDATION_ERROR';
    public const TECHNICAL_ERROR = 'TECHNICAL_ERROR';
    public const SUCCESS = 'SUCCESS';

    public function process(): void
    {
        $invoiceModel = new Invoice();
        $eventLogModel = new EventLog();

        $invoice = $invoiceModel
            ->where('invoice_id', $this->data['invoice_id'])
            ->first();

        if ($invoice === null) {
            return;
        }

        try {
            $url = getenv('SHIPMENT_ENDPOINT') . '?case=' . $invoice['comment'];
            $idempotencyKey = 'shipment:' . $invoice['invoice_id'];

            $client = Services::curlRequest();

            $result = $client->post($url, [
                'timeout' => 4,
                'http_errors' => false,
                'headers' => [
                    'Idempotency-Key' => $idempotencyKey,
                ],
                'json' => [
                    'invoice_id' => $invoice['invoice_id'],
                    'amount' => $invoice['amount'],
                    'name' => $invoice['customer_name'],
                    'email' => $invoice['customer_email'],
                ],
            ]);

            $payload = json_decode($result->getBody(), true, 512, JSON_THROW_ON_ERROR);
            $serviceMessage = is_array($payload) ? strtolower((string)($payload['message'] ?? '')) : '';
            $code = $result->getStatusCode();

            if ($code >= 400 && $code < 500) {
                throw new ShippingValidationException($serviceMessage);
            }
            if ($code >= 500) {
                throw new \Exception("Server error");
            }
            if (!isset($payload['reference'])) {
                throw new ShippingValidationException("No reference number");
            }

            $db = db_connect();
            $db->transStart();

            $invoiceModel->update($invoice['id'], [
                'shipping_status' => ShipmentJob::SUCCESS,
                'shipping_info' => $payload['reference'],
            ]);
            $eventLogModel->insert([
                'name' => 'shipping_attempt',
                'entity_id' => $invoice['id'],
                'payload' => json_encode([
                    'invoice_id' => $invoice['invoice_id'],
                    'status' => ShipmentJob::SUCCESS,
                    'message' => 'Shipping info: ' . $payload['reference']
                ])
            ]);
            $db->transComplete();
        } catch (ShippingValidationException $exception) {
            $db = db_connect();
            $db->transStart();

            $invoiceModel->update($invoice['id'], [
                'shipping_status' => ShipmentJob::VALIDATION_ERROR,
                'shipping_info' => $exception->getMessage(),
            ]);
            $eventLogModel->insert([
                'name' => 'shipping_attempt',
                'entity_id' => $invoice['id'],
                'payload' => json_encode([
                    'invoice_id' => $invoice['invoice_id'],
                    'status' => ShipmentJob::VALIDATION_ERROR,
                    'message' => $exception->getMessage()
                ])
            ]);

            $db->transComplete();

        } catch (\Throwable $exception) {
            $db = db_connect();
            $db->transStart();

            $invoiceModel->update($invoice['id'], [
                'shipping_status' => ShipmentJob::TECHNICAL_ERROR,
            ]);
            $eventLogModel->insert([
                'name' => 'shipping_attempt',
                'entity_id' => $invoice['id'],
                'payload' => json_encode([
                    'invoice_id' => $invoice['invoice_id'],
                    'status' => ShipmentJob::TECHNICAL_ERROR,
                    'message' => $exception->getMessage()
                ])
            ]);

            $db->transComplete();

            throw new RuntimeException($exception->getMessage());
        }
    }
}
