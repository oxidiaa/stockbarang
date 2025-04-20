<?php
session_start();
require 'function.php';

// Get the last 'sesudah' value
$query = "SELECT sesudah FROM solar_keluar ORDER BY id DESC LIMIT 1";
$result = mysqli_query($conn, $query);

$response = array();
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $response['sesudah'] = floatval($row['sesudah']);
} else {
    $response['sesudah'] = 0;
}

header('Content-Type: application/json');
echo json_encode($response);
?> 