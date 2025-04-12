<?php
session_start();

if (!isset($_SESSION['log'])) {
    header('location:login.php');
    exit;
}

$role = $_SESSION['role'] ?? null;
$currentPage = basename($_SERVER['PHP_SELF']);

// Batasi akses untuk role 'user'
if ($role === 'user' && $currentPage !== 'solar.php') {
    // Cegah akses halaman selain solar.php
    echo "<script>alert('Akses ditolak!'); window.location='solar.php';</script>";
    exit;
}
?>
