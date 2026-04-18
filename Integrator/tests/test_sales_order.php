<?php
require_once __DIR__ . '/src/sales_order.php';

$so = createSalesOrder(CUSTOMER, ITEM_CODE, 10, 49.99, WAREHOUSE);
echo "\nSubmitting...\n";
submitSalesOrder($so['name']);
echo "\nSO name for Step 4:   " . $so['name'] . "\n";
echo "SO detail for Step 4: " . $so['items'][0]['name'] . "\n";