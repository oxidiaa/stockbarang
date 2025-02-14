<?php
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
                            <a class="nav-link" href="index.php">
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

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 mb-4">
                <a href="masuk.php" class="link-card">Barang Masuk</a>
            </div>
            <div class="col-md-6 mb-4">
                <a href="keluar.php" class="link-card">Barang Keluar</a>
            </div>
        </div>
    </div>
</body>
</html>
