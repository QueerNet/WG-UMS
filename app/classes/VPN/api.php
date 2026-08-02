<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/config.php';      // Load server configuration parameters
require_once __DIR__ . '/../../helpers/Format.php';     // Load format helper
require_once __DIR__ . '/Wg.php';                       // Load Wireguard scripts
require_once __DIR__ . '/../VPN.php';                   // Load VPN constructor

// Return template for methods called by this api:
    //$result = ['success' => BOOL, 'data' => DATA]
    //$result = ['success' => BOOL, 'error' => ERR] (If error)

$allowedMethods = [
    'wg_list_devices'  => ['userid'],
    'wg_check'         => ['allowedip', 'devid'],
    'wg_get_next_ip'   => ['userid', 'devname'],
    'wg_add_peer'      => ['iface', 'pubkey', 'psk', 'allowedIp'],
    'wg_rm_peer'       => ['iface', 'devid']
];

// Get which method was called
$method = $_POST['method'] ?? '';

// If method is not allowed, return an error and exit
if (!array_key_exists($method, $allowedMethods)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Unknown method: ' . $method]);
    exit;
}

// Pull the expected arguments, in order, from $_POST
$args = [];
foreach ($allowedMethods[$method] as $field) {
    if (!isset($_POST[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => "Missing field: $field"]);
        exit;
    }
    $args[] = $_POST[$field];
}

// Try to execute call to method
try {
    $vpn = new VPN();
    $result = call_user_func_array([$vpn, $method], $args);
} catch (\RuntimeException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}

// If result fail
if (isset($result['success']) && !$result['success']) {
    http_response_code(500);
}

// Return result
echo json_encode($result);