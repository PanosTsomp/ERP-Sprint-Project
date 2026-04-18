<?php
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../config.php';

function createCustomer(string $customerName): array {
    $payload = erpRequest('POST', '/api/resource/Customer', [
        'customer_name' => $customerName,
        'customer_type' => 'Company',
        'customer_group' => 'Commercial',
        'territory' => 'All Territories',
    ]);

    echo "Customer '$customerName' created with ID: " . $payload['name'] . "\n";
    return $payload;
}