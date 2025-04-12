<?php
session_start();
require 'function.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Stock Barang</title>
        <link href="css/styles.css" rel="stylesheet" />
        <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
        <style>
        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f8f9fa;
        }
        .link-card {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200px;  
            border: 2px solid #007bff;
            border-radius: 10px;
            font-size: 24px;
            font-weight: bold;
            text-decoration: none;
            color: #007bff;
            transition: background-color 0.2s ease;
        }
        .link-card:hover {
            background-color: #007bff;
            color: white;
        }
        .container {
            width: 60%;
        }
    </style>
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <a class="navbar-brand" href="index.php">MetalArt Astra Indonesia</a>
            <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#"><i class="fas fa-bars"></i></button>
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                    <div class="nav">   
    <div class="sb-sidenav-menu-heading">Core</div>

    <?php if ($_SESSION['role'] == 'admin') { ?>
        <a class="nav-link" href="index.php">
            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
            Beranda 
        </a>
        <a class="nav-link" href="solar.php">
            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
            Solar
        </a>
        <a class="nav-link" href="stock.php">
            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
            Stock Barang
        </a>
        <a class="nav-link" href="masuk.php">
            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
            Barang Masuk
        </a>
        <a class="nav-link" href="keluar.php">
            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
            Barang Keluar
        </a>
    <?php } ?>

    <a class="nav-link" href="solar.php">
        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
        Solar
    </a>
    <a class="nav-link" href="logout.php">
        Logout
    </a>
</div>

                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">MetalArt Astra Indonesia</div>
                        Ryan Pratama | 240040
                    </div>
                </nav>
            </div>
<div class="container">
            <div class="row mt-4"> <!-- Tambahkan ROW agar elemen tersusun secara vertikal -->
    <div class="col-12 mb-4"> <!-- Card 1 -->
        <div class="card d-flex flex-row p-4 align-items-center w-100">
            <img src="https://thumbs.dreamstime.com/z/gas-cylinders-tanks-set-gas-cylinders-tanks-realistic-set-flammable-gas-symbols-isolated-vector-illustration-288988149.jpg" 
                alt="image" class="img-fluid" width="300" height="300">
            <div class="ms-4 flex-grow-1">
                <h4 class="mt-3">Gas Area</h4>
                <p class="card-subtitle mt-2 mb-3">Klik bawah untuk pengambilan item di Gas Area.</p>
                <a href="keluar.php" class="btn btn-primary">Gas Area</a>
            </div>
        </div>
    </div>

    <div class="col-12"> <!-- Card 2 -->
        <div class="card d-flex flex-row p-4 align-items-center w-100">
            <img src="https://onesolution.pertamina.com/uploads/insight/20230425030316ind_warna%20solar.png" 
                alt="image" class="img-fluid" width="300" height="300">
            <div class="ms-4 flex-grow-1">
                <h4 class="mt-3">Solar</h4>
                <p class="card-subtitle mt-2 mb-3">Klik bawah untuk pengambilan item di Area Solar.</p>
                <a href="solar.php" class="btn btn-primary">Solar</a>
            </div>
        </div>
    </div>
</div>



  </div>
</div>

        </div>

</body>
</html>
