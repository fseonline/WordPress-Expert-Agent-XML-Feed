<?php
// if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$response = array('message' => 'Connecting', 'done' => false);
$fileHash = md5($_POST['file_id']);

if (file_exists($fileHash)) {
    $response = json_decode(file_get_contents($fileHash));
    if ((bool) $response->done === true) {
        // Clean the file
        unlink($fileHash);
    }
}

header('Content-Type: application/json');
echo json_encode($response);
