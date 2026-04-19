<?php

require_once 'Integrator/config.php';
require_once 'Integrator/src/auth.php';

$items = [
    [
        'item_code' => 'WIDGET-002',
        'item_name' => 'Widget Pro',
        'item_group' => 'Products',
        'stock_uom' => 'Nos',
        'is_stock_item' => 1,
        'standard_rate' => 79.99,
        'opening_stock' => 50,
        'valuation_rate' => 40.00,
        'default_warehouse' => 'Stores - A',
        'description' => 'Upgraded widget with extra features',
    ],
    [
        'item_code' => 'GADGET-001',
        'item_name' => 'Gadget Basic',
        'item_group' => 'Products',
        'stock_uom' => 'Nos',
        'is_stock_item' => 1,
        'standard_rate' => 29.99,
        'opening_stock' => 200,
        'valuation_rate' => 15.00,
        'default_warehouse' => 'Stores - A',
        'description' => 'Basic gadget for everyday use',
    ],
    [
        'item_code' => 'PART-001',
        'item_name' => 'Replacement Part',
        'item_group' => 'Products',
        'stock_uom' => 'Nos',
        'is_stock_item' => 1,
        'standard_rate' => 9.99,
        'opening_stock' => 500,
        'valuation_rate' => 5.00,
        'default_warehouse' => 'Stores - A',
        'description' => 'Replacement part for damaged items',
    ],
];

foreach ($items as $item) {
    try {
        $result = erpRequest('POST', '/api/resource/Item', $item);
        echo "[OK] Created: " . $result['name'] . " - €" . $item['standard_rate'] . "\n";
    } catch (RuntimeException $e) {
        // Item may already exist - try to fetch it to confirm
        if (str_contains($e->getMessage(), 'DuplicateEntryError')) {
            echo "[SKIP] Already exists: " . $item['item_code'] . "\n";
        } else {
            echo "[ERROR] " . $item['item_code'] . " - " . $e->getMessage() . "\n";
        }
    }
}