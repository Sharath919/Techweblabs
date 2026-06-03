<?php
/**
 * Simple health check — same URL style as the rest of admin (no root .htaccess slug rules).
 * https://yoursite.com/admin/site-health.php
 */
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
echo json_encode(array('ok' => true, 'message' => 'site-health', 'path' => 'admin'));
