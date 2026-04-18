<?php
require_once __DIR__ . '/src/auth.php';

$item = erpRequest('GET', '/api/resource/Item/' . ITEM_CODE);
echo "Connected. Item found: " . $item['item_name'] . "\n";
echo "Standard Rate: " . $item['standard_rate'] . "\n";