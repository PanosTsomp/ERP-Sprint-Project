<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/auth.php';

function createCustomer(string $name): array {
    $query = http_build_query([
        'filters' => json_encode([['customer_name', '=', $name]]),
        'fields'  => json_encode(['name']),
    ]);

    $existing = erpRequest('GET', '/api/resource/Customer?' . $query);

    if (!empty($existing)) {
        echo "Customer already exists: " . $existing[0]['name'] . "\n";
        return $existing[0];
    }

    $customer = erpRequest('POST', '/api/resource/Customer', [
        'customer_name'  => $name,
        'customer_type'  => 'Company',
        'customer_group' => 'Commercial',
        'territory'      => 'All Territories',
    ]);

    echo "Customer created: " . $customer['name'] . "\n";
    return $customer;
}