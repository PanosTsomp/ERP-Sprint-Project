<?php
// Fetches the live item catalogue from ERPNext


require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/auth.php';

function getItemCatalogue(): array {
    $query = http_build_query([
        'fields' => json_encode(['item_code', 'item_name', 'standard_rate', 'description']),
        'filters' => json_encode([
            ['disabled', '=', 0],
            ['is_stock_item', '=', 1],
        ]),
        'limit_page_length' => 50,
    ]);

    $result = erpRequest('GET', '/api/resource/Item?' . $query);

    // ERPNext returns a list - normalise to a clean array
    return array_map(fn($item) => [
        'item_code' => $item['item_code'],
        'item_name' => $item['item_name'],
        'standard_rate' => $item['standard_rate'],
        'description' => $item['description'] ?? '',
    ], $result);
}