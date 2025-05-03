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
    if (tambahStokSolar($_POST)) {
        header('location:solar.php');
        exit();
    }
}

// Proses form tambah data solar keluar
if (isset($_POST['tambah'])) {
    header('Content-Type: application/json');
    $response = array();
    
    try {
        // Validasi input
        if (empty($_POST['forklift'])) {
            throw new Exception("Forklift harus dipilih");
        }
        if (empty($_POST['user'])) {
            throw new Exception("Nama harus diisi");
        }
        if (!isset($_POST['sebelum'])) {
            throw new Exception("Volume sebelum harus diisi");
        }
        if (!isset($_POST['sesudah'])) {
            throw new Exception("Volume sesudah harus diisi");
        }
        
        $sebelum = floatval($_POST['sebelum']);
        $sesudah = floatval($_POST['sesudah']);
        $jumlahDiambil = $sesudah - $sebelum;
        
        if ($sesudah <= $sebelum) {
            throw new Exception("Volume sesudah harus lebih besar dari volume sebelum");
        }

        // Get current stock
        $queryStok = mysqli_query($conn, "SELECT stock FROM stock_solar WHERE id = 1");
        $currentStock = 0;
        if ($row = mysqli_fetch_assoc($queryStok)) {
            $currentStock = $row['stock'];
        }

        // Check if enough stock
        if ($jumlahDiambil > $currentStock) {
            throw new Exception("Stok solar tidak mencukupi");
        }

        // Begin transaction
        mysqli_begin_transaction($conn);

        // Update stock_solar
        $newStock = $currentStock - $jumlahDiambil;
        $updateStok = mysqli_query($conn, "UPDATE stock_solar SET stock = $newStock, last_update = NOW() WHERE id = 1");
        
        if (!$updateStok) {
            throw new Exception("Gagal mengupdate stok solar");
        }

        // Log the stock reduction
        $logQuery = mysqli_query($conn, "INSERT INTO stock_solar_log (tanggal, jumlah, tipe, stock_sebelum, stock_sesudah) 
                                       VALUES (NOW(), $jumlahDiambil, 'keluar', $currentStock, $newStock)");
        
        if (!$logQuery) {
            throw new Exception("Gagal mencatat log pengambilan solar");
        }

        // Insert into solar_keluar
        if (tambahSolarKeluar($_POST)) {
            mysqli_commit($conn);
            $response['status'] = 'success';
            $response['message'] = 'Data berhasil ditambahkan';
        } else {
            throw new Exception("Gagal menambahkan data pengambilan solar");
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $response['status'] = 'error';
        $response['message'] = $e->getMessage();
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Proses form edit data solar keluar
if (isset($_POST['edit']) && $_SESSION['role'] == 'admin') {
    if (editSolarKeluar($_POST)) {
        header('location:solar.php');
        exit();
    }
}

// Proses form hapus data solar keluar
if (isset($_POST['hapus']) && $_SESSION['role'] == 'admin') {
    if (hapusSolarKeluar($_POST['id'])) {
        header('location:solar.php');
        exit();
    }
}

// Ambil data stok solar
$query = "SELECT stock FROM stock WHERE namabarang = 'Solar'";
$result = mysqli_query($conn, $query);
$stokSolar = 0;
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $stokSolar = $row['stock'];
}

// Hitung total pengeluaran
$query = "SELECT SUM(total) as total FROM solar_keluar";
$result = mysqli_query($conn, $query);
$totalPengeluaran = 0;
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $totalPengeluaran = $row['total'] ?? 0;
}

// Hitung stok tersisa
$stokTersisa = $stokSolar - $totalPengeluaran;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="MetalArt Astra Indonesia - Sistem Manajemen Solar" />
    <meta name="author" content="MetalArt" />
    <title>Solar - MetalArt</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet" />
    <style>
        .card {
            margin-bottom: 20px;
        }
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
                        <a class="nav-link active" href="solar.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-gas-pump"></i></div>
                            Solar
                        </a>
                        <?php if ($_SESSION['role'] == 'admin') { ?>
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
                    <h1 class="mt-4">Solar</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Solar</li>
                    </ol>
                    
                    <!-- Tabel Data Solar -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Data Pengambilan Solar
                            <button type="button" class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#tambahModal">
                                <i class="fas fa-plus"></i> Ambil Solar
                            </button>
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
                            $queryPengeluaran = mysqli_query($conn, "SELECT SUM(jumlah) as total FROM stock_solar_log WHERE tipe = 'keluar' and month(tanggal) = ".date('n'));
                            $totalPengeluaran = 0;
                            if ($row = mysqli_fetch_assoc($queryPengeluaran)) {
                                $totalPengeluaran = $row['total'] ?: 0;
                            }
                            ?>
                            <div class="stock-info">
                                <p><strong>Stock Solar:</strong> <?php echo number_format($stokSolar, 2); ?> liter</p>
                                <p><strong>Total Pengeluaran:</strong> <?php echo number_format($totalPengeluaran, 2); ?> liter</p>
                                
                                <!-- DROP DOWN -->
                                 
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownbulan" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Dropdown Bulan
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownbulan">
                                        <a class="dropdown-item" href="#">Action</a>
                                        <a class="dropdown-item" href="#">Another action</a>
                                        <a class="dropdown-item" href="#">Something else here</a>
                                    </div>
                                </div>

                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdowntahun" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Dropdown Tahun
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdowntahun">
                                        <a class="dropdown-item" href="#">Action</a>
                                        <a class="dropdown-item" href="#">Another action</a>
                                        <a class="dropdown-item" href="#">Something else here</a>
                                    </div>
                                </div>

                            </div>
                        </div>
                        </div>
                        <div class="card-body">
                            <?php
                            // Get total stok solar
                            $queryStok = mysqli_query($conn, "SELECT stock FROM stock WHERE namabarang = 'Solar'");
                            $stokSolar = 0;
                            if ($row = mysqli_fetch_assoc($queryStok)) {
                                $stokSolar = $row['stock'];
                            }

                            // Get total pengeluaran
                            $queryPengeluaran = mysqli_query($conn, "SELECT SUM(sesudah) as total FROM solar_keluar");
                            $totalPengeluaran = 0;
                            if ($row = mysqli_fetch_assoc($queryPengeluaran)) {
                                $totalPengeluaran = $row['total'] ?: 0;
                            }

                            // Hitung stok tersisa
                            $stokTersisa = $stokSolar - $totalPengeluaran;
                            ?>

                            <table id="datatablesSimple" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Forklift</th>
                                        <th>Nama</th>
                                        <th>Sebelum (Ltr)</th>
                                        <th>Sesudah (Ltr)</th>
                                        <th>Jumlah solar yang diambil (Ltr)</th>
                                        <?php if ($_SESSION['role'] == 'admin') { ?>
                                        <th>Aksi</th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
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

    <!-- Modal Tambah Stok Solar (Admin Only) -->
    <div class="modal fade" id="tambahStokModal" tabindex="-1" role="dialog" aria-labelledby="tambahStokModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahStokModalLabel">Tambah Stok Solar</h5>
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
                            <label>Tambah Solar (Liter)</label>
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

    <!-- Modal Tambah Data -->
    <div class="modal fade" id="tambahModal" tabindex="-1" role="dialog" aria-labelledby="tambahModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahModalLabel">Ambil Solar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formAmbilSolar">
                    <div class="modal-body">
                        <?php
                        // Get the last 'sesudah' value from solar_keluar
                        $last = mysqli_query($conn, "SELECT sesudah FROM solar_keluar ORDER BY id DESC LIMIT 1");
                        $sebelum = 0; // Default value is 0
                        if ($last && mysqli_num_rows($last) > 0) {
                            $dataakhir = mysqli_fetch_array($last);
                            $sebelum = $dataakhir['sesudah'];
                        }
                        ?>
                        <div class="form-group">
                            <label>Nomor Forklift</label>
                            <select name="forklift" class="form-control" required>
                                <option value="">Pilih Nomor Forklift</option>
                                <?php for($i = 1; $i <= 8; $i++) { ?>
                                    <option value="R<?= $i; ?>">R<?= $i; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" name="user" placeholder="Nama" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Sebelum (Liter)</label>
                            <input type="number" step="0.01" name="sebelum" class="form-control" value="<?= $sebelum; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Sesudah (Liter)</label>
                            <input type="number" step="0.01" name="sesudah" placeholder="Volume Sesudah (Liter)" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <input type="hidden" name="tambah" value="1">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit dan Hapus (Admin Only) -->
    <?php
    if ($_SESSION['role'] == 'admin') {
        $query = "SELECT * FROM solar_keluar ORDER BY tanggal DESC";
        $result = mysqli_query($conn, $query);
        while ($row = mysqli_fetch_assoc($result)) {
    ?>
    <!-- Modal Edit -->
    <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Data Solar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formEditSolar">
                    <div class="modal-body">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <div class="form-group">
                            <label>Nomor Forklift</label>
                            <select name="forklift" class="form-control" required>
                                <option value="">Pilih Nomor Forklift</option>
                                <?php 
                                for($i = 1; $i <= 8; $i++) { 
                                    $selected = ($row['forklift'] == "R$i") ? 'selected' : '';
                                ?>
                                    <option value="R<?= $i; ?>" <?= $selected; ?>>R<?= $i; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" name="user" class="form-control" value="<?php echo htmlspecialchars($row['user']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Sebelum (Liter)</label>
                            <input type="number" step="0.01" name="sebelum" class="form-control" value="<?php echo $row['sebelum']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Sesudah (Liter)</label>
                            <input type="number" step="0.01" name="sesudah" class="form-control" value="<?php echo $row['sesudah']; ?>" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" name="edit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Hapus -->
    <div class="modal fade" id="hapusModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="hapusModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="hapusModalLabel">Hapus Data Solar Keluar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus data ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <form action="" method="post" style="display: inline;">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="hapus" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php
        }
    }
    ?>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#datatablesSimple').DataTable({
                order: [[1, 'desc']], // Sort by date column
                orderClasses: false,
                processing: true,
                serverSide: false,
                ajax: {
                    url: 'get_solar_data.php',
                    dataSrc: ''
                },
                columns: [
                    { data: null, render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }},
                    { data: 'tanggal' },
                    { data: 'forklift' },
                    { data: 'user' },
                    { data: 'sebelum', render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }},
                    { data: 'sesudah', render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }},
                    { data: null, render: function(data) {
                        return (parseFloat(data.sesudah) - parseFloat(data.sebelum)).toFixed(2);
                    }}
                    <?php if ($_SESSION['role'] == 'admin') { ?>,
                    { data: null, render: function(data) {
                        return `
                            <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal${data.id}">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#hapusModal${data.id}">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        `;
                    }}
                    <?php } ?>
                ]
            });

            // Handle form submission for adding new data
            $('#formAmbilSolar').on('submit', function(e) {
                e.preventDefault();
                
                var formData = $(this).serialize();
                
                $.ajax({
                    type: 'POST',
                    url: 'solar.php',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#tambahModal').modal('hide');
                            table.ajax.reload();
                            alert('Data berhasil ditambahkan');
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Terjadi kesalahan: ' + error);
                    }
                });
            });

            // Handle edit form submission
            $(document).on('submit', '[id^=formEdit]', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                var modalId = $(this).closest('.modal').attr('id');
                
                $.ajax({
                    type: 'POST',
                    url: 'solar.php',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $(`#${modalId}`).modal('hide');
                            table.ajax.reload();
                            alert('Data berhasil diupdate');
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Terjadi kesalahan: ' + error);
                    }
                });
            });

            // Handle delete confirmation
            $(document).on('click', '[id^=btnDelete]', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                var modalId = $(this).closest('.modal').attr('id');
                
                $.ajax({
                    type: 'POST',
                    url: 'solar.php',
                    data: {
                        hapus: true,
                        id: id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $(`#${modalId}`).modal('hide');
                            table.ajax.reload();
                            alert('Data berhasil dihapus');
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Terjadi kesalahan: ' + error);
                    }
                });
            });

            // Validation for sesudah value
            $('input[name="sesudah"]').on('change', function() {
                var sebelum = parseFloat($('input[name="sebelum"]').val());
                var sesudah = parseFloat($(this).val());
                
                if (sesudah <= sebelum) {
                    alert('Volume sesudah harus lebih besar dari volume sebelum');
                    $(this).val('');
                }
            });
        });

        // Function to format date
        function formatDate(date) {
            var d = new Date(date);
            return d.toLocaleString('id-ID');
        }
    </script>
</body>
</html>