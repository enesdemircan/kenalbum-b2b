<?php
// Basit POST test - Laravel bypass
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postData = $_POST;
    $fileData = $_FILES;
    
    echo json_encode([
        'success' => true,
        'message' => 'POST request received!',
        'post_data' => $postData,
        'files' => array_map(function($file) {
            return [
                'name' => $file['name'],
                'size' => $file['size'],
                'error' => $file['error']
            ];
        }, $fileData),
        'content_length' => $_SERVER['CONTENT_LENGTH'] ?? 0,
        'request_method' => $_SERVER['REQUEST_METHOD']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Use POST method'
    ]);
}
?>

