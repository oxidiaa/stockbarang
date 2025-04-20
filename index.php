<?php
session_start();
require 'function.php';

// Cek apakah user sudah login
if (!isset($_SESSION['log'])) {
    header('location:login.php');
    exit();
}

// Cek session timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_unset();
    session_destroy();
    header('location:login.php?message=timeout');
    exit();
}
$_SESSION['last_activity'] = time();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="MetalArt Astra Indonesia - Sistem Manajemen Stock" />
    <meta name="author" content="MetalArt" />
    <title>Stock Barang - MetalArt</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet" />
    <style>
        body {
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .card {
            transition: transform 0.2s;
            margin-bottom: 20px;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .card img {
            object-fit: cover;
            max-height: 200px;
        }
        .navbar-brand {
            font-weight: bold;
        }
        .sb-sidenav-dark {
            background-color: #212529;
        }
        .sb-sidenav-footer {
            background-color: #343a40;
        }
    </style>
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand" href="index.php">MetalArt Astra Indonesia</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="ml-auto">
            <span class="text-light mr-3">Welcome, <?php echo htmlspecialchars($_SESSION['email']); ?></span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </nav>

    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Core</div>
                        <a class="nav-link active" href="index.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>
                            Beranda
                        </a>
                        
                        <?php if ($_SESSION['role'] == 'admin') { ?>
                        <a class="nav-link" href="solar.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-gas-pump"></i></div>
                            Solar
                        </a>
                        <a class="nav-link" href="stock_solar.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-warehouse"></i></div>
                            Stock Solar
                        </a>
                        <a class="nav-link" href="stock.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-boxes"></i></div>
                            Stock Barang
                        </a>
                        <a class="nav-link" href="masuk.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-download"></i></div>
                            Barang Masuk
                        </a>
                        <a class="nav-link" href="keluar.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-upload"></i></div>
                            Barang Keluar
                        </a>
                        <?php } else { ?>
                        <a class="nav-link" href="solar.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-gas-pump"></i></div>
                            Solar
                        </a>
                        <a class="nav-link" href="stock_solar.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-warehouse"></i></div>
                            Stock Solar
                        </a>
                        <?php } ?>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">MetalArt Astra Indonesia</div>
                    Ryan Pratama | 240040
                </div>
            </nav>
        </div>

        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Dashboard</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>

                    <div class="row">
                        <?php if ($_SESSION['role'] == 'admin') { ?>
                        <div class="col-12 mb-4">
                            <div class="card h-100">
                                <div class="card-body d-flex align-items-center">
                                    <img src="https://thumbs.dreamstime.com/z/gas-cylinders-tanks-set-gas-cylinders-tanks-realistic-set-flammable-gas-symbols-isolated-vector-illustration-288988149.jpg" 
                                        alt="Gas Area" class="img-fluid me-4" style="max-width: 200px;">
                                    <div>
                                        <h4 class="card-title">Gas Area</h4>
                                        <p class="card-text">Akses untuk pengambilan item di Gas Area.</p>
                                        <a href="keluar.php" class="btn btn-primary">
                                            <i class="fas fa-arrow-right"></i> Gas Area
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>

                        <div class="col-12">
                            <div class="card h-100">
                                <div class="card-body d-flex align-items-center">
                                    <img src="https://onesolution.pertamina.com/uploads/insight/20230425030316ind_warna%20solar.png" 
                                        alt="Solar Area" class="img-fluid me-4" style="max-width: 200px;">
                                    <div>
                                        <h4 class="card-title">Solar</h4>
                                        <p class="card-text">Akses untuk pengambilan item di Area Solar.</p>
                                        <a href="solar.php" class="btn btn-primary">
                                            <i class="fas fa-arrow-right"></i> Solar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>
