<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/src/customer.php';
require_once __DIR__ . '/src/stock.php';
require_once __DIR__ . '/src/sales_order.php';
require_once __DIR__ . '/src/delivery_note.php';

echo "=================================\n";
echo " ERP Sprint — Quote-to-Cash Run \n";
echo "=================================\n\n";

try {
    // Step 1 — Create customer
    echo "[ Step 1 ] Creating customer...\n";
    $customer = createCustomer(CUSTOMER);
    echo "Done.\n\n";

    // Step 2 — Stock check
    echo "[ Step 2 ] Checking stock...\n";
    $qty = checkStock(ITEM_CODE, WAREHOUSE);
    echo "Done. $qty units available.\n\n";

    // Step 3 — Sales order
    echo "[ Step 3 ] Creating Sales Order...\n";
    $so = createSalesOrder(CUSTOMER, ITEM_CODE, 10, 49.99, WAREHOUSE);
    submitSalesOrder($so['name']);
    echo "Done.\n\n";

    // Step 4 — Delivery note
    echo "[ Step 4 ] Creating Delivery Note...\n";
    $dn = createDeliveryNote(
        CUSTOMER,
        $so['name'],
        $so['items'][0]['name'],
        ITEM_CODE,
        10,
        WAREHOUSE
    );
    submitDeliveryNote($dn['name']);
    echo "Done.\n\n";

    echo "=================================\n";
    echo " Pipeline completed successfully \n";
    echo "=================================\n";
    echo "Customer:      " . $customer['name'] . "\n";
    echo "Sales Order:   " . $so['name'] . "\n";
    echo "Delivery Note: " . $dn['name'] . "\n";
    echo "Units sold:    10\n";
    echo "Revenue:       EUR " . $so['grand_total'] . "\n";

} catch (RuntimeException $e) {
    echo "\n[PIPELINE ERROR] " . $e->getMessage() . "\n";
    exit(1);
}
