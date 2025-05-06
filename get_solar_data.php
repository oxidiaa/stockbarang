<?php
session_start();
require 'function.php';

// Get year and month from request
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$month = isset($_GET['month']) ? intval($_GET['month']) : date('n');

// Build the query with year and month filter
$query = "SELECT * FROM solar_keluar WHERE YEAR(tanggal) = ? AND MONTH(tanggal) = ? ORDER BY tanggal DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $year, $month);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Calculate total pengeluaran for the selected month and year
$queryTotal = "SELECT SUM(sesudah - sebelum) as total FROM solar_keluar WHERE YEAR(tanggal) = ? AND MONTH(tanggal) = ?";
$stmtTotal = mysqli_prepare($conn, $queryTotal);
mysqli_stmt_bind_param($stmtTotal, "ii", $year, $month);
mysqli_stmt_execute($stmtTotal);
$totalResult = mysqli_stmt_get_result($stmtTotal);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalPengeluaran = $totalRow['total'] ?: 0;

$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    // Format the date
    $row['tanggal'] = date('d-m-Y H:i:s', strtotime($row['tanggal']));
    $data[] = $row;
}

// Return JSON response with total pengeluaran
$response = array(
    'data' => $data,
    'total_pengeluaran' => number_format($totalPengeluaran, 2)
);

header('Content-Type: application/json');
echo json_encode($response);
?> 