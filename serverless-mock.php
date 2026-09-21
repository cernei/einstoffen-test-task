<?php

class ShipmentMock
{
    public static function get(string $case): array
    {
        $validationErrors = ['Insufficient funds', 'Parcel is too heavy', 'Wrong shipping destination'];

        if ($case === 'ok-or-server-error') {
            $case = (rand(0, 1) === 1) ? 'ok' : 'server-error';
        }

        return match (strtolower($case)) {
            'ok' => [
                'code' => 200,
                'body' => json_encode([
                    'status' => 'ok',
                    'reference' => 'JJD' . mt_rand(1000000000, 9999999999),
                    'message' => 'Shipment created.',
                ]),
                'type' => 'json'
            ],
            'bad-json' => [
                'code' => 200,
                'body' => substr(json_encode([
                    'status' => 'ok',
                    'message' => 'Shipment created.',
                ]), 0, -5),
                'type' => 'json'
            ],
            'validation-error' => [
                'code' => 400,
                'body' => json_encode([
                    'status' => 'validation_error',
                    'message' => $validationErrors[array_rand($validationErrors)],
                ]),
                'type' => 'json'
            ],
            'bad-response' => [
                'code' => 403,
                'body' => 'Maintenance'
            ],
            'server-error' => [
                'code' => 500,
                'body' => json_encode([
                    'status' => 'server_error',
                    'message' => 'Shipment creation failed.',
                ]),
                'type' => 'json'
            ],
            'timeout' => self::shipmentTimeoutResponse(),
            default => [
                'code' => 400,
                'body' => json_encode([
                    'error' => 'Unknown shipment mock case.',
                ]),
                'type' => 'json'
            ],
        };
    }

    public static function shipmentTimeoutResponse(): array
    {
        sleep(4);

        return ['code' => 504, 'body' => json_encode([
            'status' => 'timeout',
            'message' => 'Shipment service timed out.',
        ]), 'type' => 'json'];
    }
}

function main(array $args): array
{
    $response = ShipmentMock::get($args["case"] ?? "");

    $output = [
        "statusCode" => $response['code']
    ];

    if ($response['type'] === 'json') {
        $output['headers'] = [
            "Content-Type" => "application/json"
        ];
    }
    $output['body'] = $response['body'];

    return $output;
}
