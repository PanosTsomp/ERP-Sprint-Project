<?php
require_once __DIR__ . '/src/customer.php';

$customer = createCustomer('Test Customer');

print("Customer created: " . $customer['customer_name'] . " (ID: " . $customer['name'] . ")\n");