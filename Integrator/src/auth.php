<?php
require_once __DIR__ . '/../config.php';

function erpRequest(string $method, string $endpoint, array $payload = []): array {
    $url = BASE_URL . $endpoint;
    $ch = curl_init($url);

    $headers = [
        'Content-Type: application/json',
        'Authorization: token ' . API_TOKEN,
    ];

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers,
    ]);

    if (!empty($payload)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    }

    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false) {
        throw new RuntimeException("cURL error: could not reach $url");
    }

    $body = json_decode($response, true);

    if (isset($body['exc_type']) || isset($body['exc'])) {
        $msg = $body['exc_type'] ?? 'ERPNext error';
        throw new RuntimeException("$msg - $response");
    }

    return $body['data'] ?? $body;
}