<?php
// Chunk Upload Debug Test
// Bu dosya 502 hatası neden oluyor test eder

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-CSRF-TOKEN');

// OPTIONS request için
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// POST request test
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $debug = [
        'timestamp' => date('Y-m-d H:i:s'),
        'method' => $_SERVER['REQUEST_METHOD'],
        'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'not set',
        'content_length' => $_SERVER['CONTENT_LENGTH'] ?? 0,
        'has_files' => !empty($_FILES),
        'post_data' => array_keys($_POST),
        'files_data' => array_keys($_FILES),
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
        'php_version' => PHP_VERSION,
        'max_upload' => ini_get('upload_max_filesize'),
        'max_post' => ini_get('post_max_size'),
        'memory_limit' => ini_get('memory_limit'),
        'max_execution_time' => ini_get('max_execution_time')
    ];
    
    // Chunk file var mı?
    if (isset($_FILES['chunk'])) {
        $debug['chunk_info'] = [
            'name' => $_FILES['chunk']['name'],
            'type' => $_FILES['chunk']['type'],
            'size' => $_FILES['chunk']['size'],
            'tmp_name' => $_FILES['chunk']['tmp_name'],
            'error' => $_FILES['chunk']['error'],
            'error_message' => getFileErrorMessage($_FILES['chunk']['error'])
        ];
        
        // Chunk dosyası okunabiliyor mu?
        if ($_FILES['chunk']['error'] === 0) {
            $tmpFile = $_FILES['chunk']['tmp_name'];
            if (file_exists($tmpFile)) {
                $content = file_get_contents($tmpFile);
                $debug['chunk_readable'] = $content !== false;
                $debug['chunk_actual_size'] = strlen($content);
                $debug['chunk_md5'] = md5($content);
            } else {
                $debug['chunk_readable'] = false;
                $debug['error'] = 'Temp file not found';
            }
        }
    } else {
        $debug['error'] = 'No chunk file uploaded';
    }
    
    // Session kontrolü
    session_start();
    $debug['session_id'] = session_id();
    $debug['session_active'] = session_status() === PHP_SESSION_ACTIVE;
    
    echo json_encode([
        'success' => true,
        'message' => 'Test successful',
        'debug' => $debug
    ], JSON_PRETTY_PRINT);
    
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Only POST requests allowed',
        'method' => $_SERVER['REQUEST_METHOD']
    ]);
}

function getFileErrorMessage($errorCode) {
    $errors = [
        UPLOAD_ERR_OK => 'No error',
        UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
        UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
        UPLOAD_ERR_PARTIAL => 'File partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'No temp directory',
        UPLOAD_ERR_CANT_WRITE => 'Cannot write to disk',
        UPLOAD_ERR_EXTENSION => 'PHP extension stopped upload'
    ];
    
    return $errors[$errorCode] ?? 'Unknown error';
}

