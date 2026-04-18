<?php
require_once __DIR__ . '/src/stock.php';

$qty = checkStock(ITEM_CODE, WAREHOUSE);
echo "Projected quantity for item '" . ITEM_CODE . "' in warehouse '" . WAREHOUSE . "': " . $qty . "\n";
echo "Stock check completed.\n";