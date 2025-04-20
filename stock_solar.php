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

// Proses form tambah stok solar
if (isset($_POST['tambahstok']) && $_SESSION['role'] == 'admin') {
    $tanggal = $_POST['tanggal_kedatangan'];
    $jumlah = floatval($_POST['jumlah']);
    
    if ($jumlah <= 0) {
        $_SESSION['error'] = "Jumlah solar harus lebih dari 0";
    } else {
        // Get current stock
        $queryStok = mysqli_query($conn, "SELECT stock FROM stock_solar WHERE id = 1");
        $currentStock = 0;
        if ($row = mysqli_fetch_assoc($queryStok)) {
            $currentStock = $row['stock'];
        }
        
        // Update stock
        $newStock = $currentStock + $jumlah;
        $updateQuery = mysqli_query($conn, "UPDATE stock_solar SET stock = $newStock, last_update = '$tanggal' WHERE id = 1");
        
        if ($updateQuery) {
            // Log the stock addition
            $logQuery = mysqli_query($conn, "INSERT INTO stock_solar_log (tanggal, jumlah, tipe, stock_sebelum, stock_sesudah) 
                                           VALUES ('$tanggal', $jumlah, 'masuk', $currentStock, $newStock)");
            
            if ($logQuery) {
                $_SESSION['success'] = "Stok solar berhasil ditambahkan";
            } else {
                $_SESSION['error'] = "Gagal mencatat log penambahan stok";
            }
        } else {
            $_SESSION['error'] = "Gagal menambahkan stok solar";
        }
    }
    
    header('location:stock_solar.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="MetalArt Astra Indonesia - Sistem Manajemen Stock Solar" />
    <meta name="author" content="MetalArt" />
    <title>Stock Solar - MetalArt</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet" />
    <style>
        .stock-info {
            font-size: 1.2em;
            margin-bottom: 20px;
        }
        .stock-warning {
            color: #dc3545;
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
            <span class="text-light mr-3">Welcome, <?php echo htmlspecialchars(str_replace('@gmail.com', '', $_SESSION['email'])); ?></span>
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
                        <a class="nav-link" href="index.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>
                            Beranda
                        </a>
                        <a class="nav-link" href="solar.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-gas-pump"></i></div>
                            Solar
                        </a>
                        <?php if ($_SESSION['role'] == 'admin') { ?>
                        <a class="nav-link active" href="stock_solar.php">
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
                    <h1 class="mt-4">Stock Solar</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Stock Solar</li>
                    </ol>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php 
                            echo $_SESSION['success'];
                            unset($_SESSION['success']);
                            ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php 
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                            ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <!-- Informasi Stock Solar -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-info-circle me-1"></i>
                            Informasi Stock Solar
                            <?php if ($_SESSION['role'] == 'admin') { ?>
                            <button type="button" class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#tambahStokModal">
                                <i class="fas fa-plus"></i> Tambah Stock Solar
                            </button>
                            <?php } ?>
                        </div>
                        <div class="card-body">
                            <?php
                            // Get current stock
                            $queryStok = mysqli_query($conn, "SELECT * FROM stock_solar WHERE id = 1");
                            $stokSolar = 0;
                            $lastUpdate = '';
                            if ($row = mysqli_fetch_assoc($queryStok)) {
                                $stokSolar = $row['stock'];
                                $lastUpdate = $row['last_update'];
                            }

                            // Get total pengeluaran
                            $queryPengeluaran = mysqli_query($conn, "SELECT SUM(jumlah) as total FROM stock_solar_log WHERE tipe = 'keluar'");
                            $totalPengeluaran = 0;
                            if ($row = mysqli_fetch_assoc($queryPengeluaran)) {
                                $totalPengeluaran = $row['total'] ?: 0;
                            }
                            ?>
                            <div class="stock-info">
                                <p><strong>Stock Solar:</strong> <?php echo number_format($stokSolar, 2); ?> liter</p>
                                <p><strong>Total Pengeluaran:</strong> <?php echo number_format($totalPengeluaran, 2); ?> liter</p>
                                <p><strong>Terakhir Update:</strong> <?php echo $lastUpdate ? date('d/m/Y H:i', strtotime($lastUpdate)) : '-'; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Riwayat Stock Solar -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-history me-1"></i>
                            Riwayat Stock Solar
                        </div>
                        <div class="card-body">
                            <table id="datatablesSimple" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Tipe</th>
                                        <th>Jumlah (Ltr)</th>
                                        <th>Stock Sebelum (Ltr)</th>
                                        <th>Stock Sesudah (Ltr)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = mysqli_query($conn, "SELECT * FROM stock_solar_log ORDER BY tanggal DESC");
                                    $i = 1;
                                    while ($row = mysqli_fetch_assoc($query)) {
                                    ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($row['tanggal'])); ?></td>
                                        <td><?php echo ucfirst($row['tipe']); ?></td>
                                        <td><?php echo number_format($row['jumlah'], 2); ?></td>
                                        <td><?php echo number_format($row['stock_sebelum'], 2); ?></td>
                                        <td><?php echo number_format($row['stock_sesudah'], 2); ?></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>

            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; MetalArt 2025</div>
                        <div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Modal Tambah Stock Solar -->
    <div class="modal fade" id="tambahStokModal" tabindex="-1" role="dialog" aria-labelledby="tambahStokModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahStokModalLabel">Tambah Stock Solar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Tanggal Kedatangan</label>
                            <input type="datetime-local" name="tanggal_kedatangan" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Jumlah Solar (Liter)</label>
                            <input type="number" step="0.01" name="jumlah" placeholder="Jumlah Solar (Liter)" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" name="tambahstok" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#datatablesSimple').DataTable({
                order: [[1, 'desc']], // Sort by date column
                pageLength: 10,
                responsive: true
            });
        });
    </script>
</body>
</html>