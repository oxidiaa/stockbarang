<?php
session_start();
require 'function.php';

// Query untuk mengambil data solar
$query = "SELECT id, tanggal, forklift, user, sebelum, sesudah FROM solar_keluar ORDER BY tanggal DESC";
$result = mysqli_query($conn, $query);

$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    // Format tanggal
    $row['tanggal'] = date('d/m/Y H:i', strtotime($row['tanggal']));
    
    // Konversi nilai numerik
    $row['sebelum'] = floatval($row['sebelum']);
    $row['sesudah'] = floatval($row['sesudah']);
    
    $data[] = $row;
}

// Return data sebagai JSON
header('Content-Type: application/json');
echo json_encode($data);
?> 