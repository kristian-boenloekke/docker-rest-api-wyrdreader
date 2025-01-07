<?php
function allowedMethods(array $methods): void
{
    $method = $_SERVER['REQUEST_METHOD'];
    $allowed = array_map('strtoupper', $methods);

    // Handle OPTIONS preflight requests
    if ($method === 'OPTIONS') {
        header('Allow: ' . implode(', ', $allowed));
        http_response_code(200);
        exit;
    }

    // Check if the current method is allowed
    if (!in_array($method, $allowed)) {
        header('Allow: ' . implode(', ', $allowed));
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed. Allowed methods: " . implode(', ', $allowed)]);
        exit;
    }
}
