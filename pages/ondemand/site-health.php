<?php
/**
 * Simple health check — same folder pattern as other on-demand pages.
 * https://yoursite.com/pages/ondemand/site-health.php
 */
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
echo json_encode(array('ok' => true, 'message' => 'site-health', 'path' => 'pages/ondemand'));
