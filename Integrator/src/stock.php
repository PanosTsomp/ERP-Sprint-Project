<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config.php';

function checkStock(string $itemCode, string $warehouse): float {
    $filters = json_encode([
        ['item_code', '=', $itemCode],
        ['warehouse', '=', $warehouse],
    ]);

    $fields = json_encode(['actual_qty', 'reserved_qty', 'projected_qty']);

    $query = http_build_query([
        'filters' => $filters,
        'fields' => $fields,
    ]);

    $response = erpRequest('GET', '/api/resource/Bin?' . $query);

    if (empty($response)) {
        throw new RuntimeException("No stock information found for item '$itemCode' in warehouse '$warehouse'.");
    }

    $bin = $response[0];
    echo "Stock for item '$itemCode' in warehouse '$warehouse':\n";
    echo "  Actual Qty: " . $bin['actual_qty'] . "\n";
    echo "  Reserved Qty: " . $bin['reserved_qty'] . "\n";
    echo "  Projected Qty: " . $bin['projected_qty'] . "\n";

    if ((float)$bin['projected_qty'] < 1) {
        echo "Warning: Projected quantity is negative. Stock may be insufficient.\n";
    }

    return $bin['projected_qty'];
}