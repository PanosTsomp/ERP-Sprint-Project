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

    return erpRequest('POST', '/api/resource/Customer', $payload);
}