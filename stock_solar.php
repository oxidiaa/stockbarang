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

                            <!-- Informasi Stok Solar (Admin Only) -->
                            <?php if ($_SESSION['role'] == 'admin') { ?>
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Informasi Stok Solar
                                </div>
                                <div class="card-body">
                                    <div class="stock-info">
                                        <p><strong>Stok Solar:</strong> <?php echo number_format($stokSolar, 2); ?> liter</p>
                                        <p><strong>Total Pengeluaran:</strong> <?php echo number_format($totalPengeluaran, 2); ?> liter</p>
                                        <p><strong>Stok Tersisa:</strong> <span class="<?php echo $stokTersisa < 0 ? 'text-danger' : ''; ?>"><?php echo number_format($stokTersisa, 2); ?> liter</span></p>
                                    </div>
                                    
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tambahStokModal">
                                        <i class="fas fa-plus"></i> Tambah Stok Solar
                                    </button>
                                </div>
                            </div>
                            <?php } ?>

                            <table id="datatablesSimple" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Forklift</th>
                                        <th>Nama</th>
                                        <th>Sebelum (Ltr)</th>
                                        <th>Sesudah (Ltr)</th>
                                        <th>Total (Ltr)</th>
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
                    { data: 'id', render: function(data) {
                        return `
                            <button type="button" class="btn btn-warning btn-sm" onclick="editData(${data})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteData(${data})">
                                <i class="fas fa-trash"></i>
                            </button>
                        `;
                    }}
                    <?php } ?>
                ]
            });

            // Handle form submission
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
                            alert(response.message);
                            location.reload(); // Reload page to update stock info
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Terjadi kesalahan: ' + error);
                    }
                });
            });
            
            // Handle edit button click
            $('.editBtn').on('click', function() {
                var id = $(this).data('id');
                var forklift = $(this).data('forklift');
                var user = $(this).data('user');
                var sebelum = $(this).data('sebelum');
                var sesudah = $(this).data('sesudah');
                
                $('#id_edit').val(id);
                $('#forklift_edit').val(forklift);
                $('#user_edit').val(user);
                $('#sebelum_edit').val(sebelum);
                $('#sesudah_edit').val(sesudah);
                
                $('#editModal').modal('show');
            });

            // Handle edit form submission
            $('#formEditSolar').on('submit', function(e) {
                e.preventDefault();
                
                var formData = $(this).serialize();
                
                $.ajax({
                    type: 'POST',
                    url: 'solar.php',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#editModal').modal('hide');
                            alert(response.message);
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Terjadi kesalahan: ' + error);
                    }
                });
            });

            // Handle delete button click
            $('.deleteBtn').on('click', function() {
                if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                    var id = $(this).data('id');
                    
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
                                alert(response.message);
                                location.reload();
                            } else {
                                alert(response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('Terjadi kesalahan: ' + error);
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>