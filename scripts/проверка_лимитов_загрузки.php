<?php
require_once '../config/config.php';

function checkUploadLimits() {
    $limits = array(
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size'),
        'memory_limit' => ini_get('memory_limit')
    );
    return $limits;
}

$result = checkUploadLimits();

header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

echo json_encode($result, JSON_PRETTY_PRINT);
?>
