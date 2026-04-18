<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/auth.php';

function createDeliveryNote(string $customerName, string $soName, string $soDetail, string $itemCode, int $qty, string $warehouse): array {
    $dn = erpRequest('POST', '/api/resource/Delivery%20Note', [
        'customer'     => $customerName,
        'posting_date' => date('Y-m-d'),
        'items'        => [
            [
                'item_code'           => $itemCode,
                'qty'                 => $qty,
                'warehouse'           => $warehouse,
                'against_sales_order' => $soName,
                'so_detail'           => $soDetail,
            ],
        ],
    ]);

    echo "Delivery Note created: " . $dn['name'] . "\n";
    echo "  Customer: " . $dn['customer'] . "\n";
    echo "  Status:   " . $dn['status'] . "\n";

    return $dn;
}

function submitDeliveryNote(string $dnName): void {
    erpRequest('PUT', '/api/resource/Delivery%20Note/' . urlencode($dnName), [
        'docstatus' => 1,
    ]);

    echo "Delivery Note submitted: $dnName\n";
    echo "Stock deducted from warehouse.\n";
}