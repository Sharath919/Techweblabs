<?php
/**
 * Root health check — single word filename (no hyphen) avoids slug confusion.
 * https://yoursite.com/sitestatus.php
 */
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
echo json_encode(array(
    'ok' => true,
    'message' => 'sitestatus',
    'path' => 'document_root',
    'time' => gmdate('c'),
));
