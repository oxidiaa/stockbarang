<?php
session_start();

if (!isset($_SESSION['log'])) {
    header('location:login.php');
    exit;
}

$role = $_SESSION['role'] ?? null;
$currentPage = basename($_SERVER['PHP_SELF']);

// Batasi akses untuk role 'user'
if ($role === 'user') {
    // Daftar halaman yang diizinkan untuk user
    $allowedPages = ['index.php', 'solar.php', 'logout.php'];
    
    // Jika halaman saat ini tidak diizinkan, redirect ke beranda
    if (!in_array($currentPage, $allowedPages)) {
        header('location:index.php');
        exit;
    }
}
?>
