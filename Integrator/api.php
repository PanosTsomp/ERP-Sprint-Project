<?php
// api.php - REST bridge between WordPress and the ERPNext pipeline

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/src/auth.php';
require_once __DIR__ . '/src/items.php';
require_once __DIR__ . '/src/customer.php';
require_once __DIR__ . '/src/stock.php';
require_once __DIR__ . '/src/sales_order.php';
require_once __DIR__ . '/src/delivery_note.php';

// GET ?action=catalogue
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';

    if ($action === 'catalogue') {
        try {
            $items = getItemCatalogue();
            echo json_encode(['status' => 'success', 'data' => $items]);
        } catch (RuntimeException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action']);
    exit;
}

// POST  - run the pipeline
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!is_array($input)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid JSON']);
        exit;
    }

    // Validate required fields
    $required = ['customer_name', 'customer_email', 'item_code', 'qty'];
    foreach ($required as $field) {
        if (empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => "Missing field: $field"]);
            exit;
        }
    }

    $customerName = trim($input['customer_name']);
    $customerEmail = trim($input['customer_email']);
    $itemCode = trim($input['item_code']);
    $qty = (int)$input['qty'];

    if ($qty < 1) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => "Quantity must be at least 1"]);
        exit;
    }

    try {
        // 1. Create customer
        $customer = createCustomer($customerName);

        // 2. Check stock
        $availableQty = checkStock($itemCode, WAREHOUSE);

        if ($availableQty < $qty) {
            throw new RuntimeException("Insufficient stock. Requested: $qty, Available: $availableQty");
        }

        // 3. Sales Order (fetch live rate from ERPNext)
        $itemData = erpRequest('GET', '/api/resource/Item/' . urlencode($itemCode));
        $rate = (float) ($itemData['standard_rate'] ?? 0);

        $so = createSalesOrder($customerName, $itemCode, $qty, $rate, WAREHOUSE);
        submitSalesOrder($so['name']);

        // 4. Create and submit delivery note
        $dn = createDeliveryNote(
            $customerName, 
            $so['name'], 
            $so['items'][0]['name'], // so_detail
            $itemCode, 
            $qty, 
            $rate
        );
        submitDeliveryNote($dn['name']);

        echo json_encode([
            'success' => true, 
            'customer' => $customer['name'],
            'order_id' => $so['name'],
            'delivery_id' => $dn['name'],
            'total' => $so['grand_total'],
            'currency' => 'EUR',
        ]);
    } catch (RuntimeException $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);