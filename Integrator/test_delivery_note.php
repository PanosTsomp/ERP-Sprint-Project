<?php
require_once __DIR__ . '/src/delivery_note.php';

$soName = 'SAL-ORD-2026-00001';
$soDetail = "aon06negcu";

$dn = createDeliveryNote(CUSTOMER, $soName, $soDetail, ITEM_CODE, 10, WAREHOUSE);
echo "\nDelivery Note created: " . $dn['name'] . "\n";
submitDeliveryNote($dn['name']);
echo "Delivery Note submitted: " . $dn['name'] . "\n";