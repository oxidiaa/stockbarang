<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require 'function.php'; // koneksi database

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $cekdatabase = mysqli_query($conn, "SELECT * FROM login WHERE email='$email'");
    $data = mysqli_fetch_array($cekdatabase);
    $hitung = mysqli_num_rows($cekdatabase);

    if ($hitung > 0) {
        if ($password == $data['password']) {
            $_SESSION['log'] = true;
            $_SESSION['email'] = $data['email'];
            $_SESSION['role'] = $data['role'];

            if ($data['role'] == 'admin') {
                header('location:index.php');
            } else {
                header('location:solar.php');
            }
            exit;
        } else {
            echo "<script>alert('Password salah!'); window.location='login.php';</script>";
        }
    } else {
        echo "<script>alert('Email tidak ditemukan!'); window.location='login.php';</script>";
    }
}

if (isset($_SESSION['log'])) {
    if ($_SESSION['role'] == 'admin') {
        header('location:index.php');
    } else {
        header('location:solar.php');
    }
    exit;
}
?>

<!-- Form Login -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - MetalArt</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height:100vh;">
    <div class="card p-4" style="width: 400px;">
        <h3 class="text-center mb-4">Login</h3>
        <form method="post">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required/>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required/>
            </div>
            <button type="submit" name="login" class="btn btn-primary btn-block">Login</button>
        </form>
    </div>
</body>
</html>
