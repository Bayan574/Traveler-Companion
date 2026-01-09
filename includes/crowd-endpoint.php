<?php
/**
 * Crowd Count Endpoint
 * Returns JSON crowd counts for specified places
 * Uses the getCrowdCounts() function from functions.php
 */

header('Content-Type: application/json');

require_once __DIR__ . '/functions.php';

$place_ids = isset($_GET['places']) ? explode(',', $_GET['places']) : [];

if (empty($place_ids)) {
    echo json_encode([]);
    exit();
}

$result = getCrowdCounts($place_ids);
echo json_encode($result);

