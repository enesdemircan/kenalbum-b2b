<?php
// Chunk Upload Test Script
header('Content-Type: application/json');

// Timeout ayarları
set_time_limit(300);
ini_set('max_execution_time', '600');
ini_set('default_socket_timeout', '600');

echo json_encode([
    'success' => true,
    'message' => 'Chunk upload endpoint reachable',
    'server_info' => [
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size'),
        'max_execution_time' => ini_get('max_execution_time'),
        'default_socket_timeout' => ini_get('default_socket_timeout'),
        'memory_limit' => ini_get('memory_limit'),
    ],
    'request_info' => [
        'method' => $_SERVER['REQUEST_METHOD'],
        'content_length' => $_SERVER['CONTENT_LENGTH'] ?? 0,
    ]
]);
?>

