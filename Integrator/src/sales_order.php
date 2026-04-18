<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/auth.php';

function customerNameToId(string $customerName): string {
    $query = http_build_query([
        'filters' => json_encode([['customer_name', '=', $customerName]]),
        'fields'  => json_encode(['name']),
    ]);

    $response = erpRequest('GET', '/api/resource/Customer?' . $query);

    if (empty($response)) {
        throw new RuntimeException("Customer '$customerName' not found.");
    }

    return $response[0]['name'];
}

function createSalesOrder(string $customerName, string $itemCode, int $qty, float $rate, string $warehouse): array {
    $customerId = customerNameToId($customerName);

    $so = erpRequest('POST', '/api/resource/Sales%20Order', [
        'customer'         => $customerId,
        'transaction_date' => date('Y-m-d'),
        'delivery_date'    => date('Y-m-d', strtotime('+7 days')),
        'order_type'       => 'Sales',
        'currency'         => 'EUR',
        'items'            => [
            [
                'item_code' => $itemCode,
                'qty'       => $qty,
                'rate'      => $rate,
                'warehouse' => $warehouse,
            ],
        ],
    ]);

    echo "Sales Order created: " . $so['name'] . "\n";
    echo "  Customer:    " . $so['customer'] . "\n";
    echo "  Grand total: " . $so['grand_total'] . "\n";
    echo "  Status:      " . $so['status'] . "\n";
    echo "  SO detail:   " . $so['items'][0]['name'] . "\n";

    return $so;
}

function submitSalesOrder(string $soName): void {
    erpRequest('PUT', '/api/resource/Sales%20Order/' . urlencode($soName), [
        'docstatus' => 1,
    ]);

    echo "Sales Order submitted: $soName\n";
}