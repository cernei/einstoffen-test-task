<?php

class ShipmentMock
{
    public static function get(string $case): array
    {
        return match (strtolower($case)) {
            'ok' => [200, [
                'status'  => 'ok',
                'reference' => 'JJD' . mt_rand(1000000000, 9999999999),
                'message' => 'Shipment created.',
            ]],
            'error' => [500, [
                'status'  => 'error',
                'message' => 'Shipment creation failed.',
            ]],
            'timeout' => self::shipmentTimeoutResponse(),
            default => [400, [
                'error' => 'Unknown shipment mock case.',
            ]],
        };
    }

    public static function shipmentTimeoutResponse(): array
    {
        sleep(2);

        return [504, [
            'status'  => 'timeout',
            'message' => 'Shipment service timed out.',
        ]];
    }

}
function main(array $args) : array
{
    $response = ShipmentMock::get($args["case"] ?? "");

    return [
        "statusCode" => $response[0],
        "headers" => [
            "Content-Type" => "application/json"
        ],
        "body" => json_encode($response[1])
    ];
}
