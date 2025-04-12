<?php
require 'function.php';
require 'cek.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Solar</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand" href="index.php">MetalArt Astra Indonesia</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle"><i class="fas fa-bars"></i></button>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Core</div>
                        <a class="nav-link" href="index.php"><div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>Beranda</a>
                        <a class="nav-link" href="solar.php"><div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>Solar</a>
                        <a class="nav-link" href="stock.php"><div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>Stock Barang</a>
                        <a class="nav-link" href="masuk.php"><div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>Barang Masuk</a>
                        <a class="nav-link" href="keluar.php"><div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>Barang Keluar</a>
                        <a class="nav-link" href="logout.php">Logout</a>
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
                <div class="container-fluid">
                    <h1 class="mt-4">Transaksi Solar</h1>
                    <div class="card mb-4">
                        <div class="card-header">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">Ambil Solar</button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Nomor Forklift</th>
                                            <th>Nama</th>
                                            <th>Sebelum (Ltr)</th>
                                            <th>Sesudah (Ltr)</th>
                                            <th>Total Pengeluaran (Ltr)</th>
                                            <?php if ($_SESSION['role'] === 'admin') { echo '<th>Aksi</th>'; } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $getsolar = mysqli_query($conn, "SELECT * FROM solar_keluar ORDER BY id DESC");
                                        while ($data = mysqli_fetch_array($getsolar)) {
                                            $id = $data['id'];
                                            $tanggal = date("Y-m-d H:i:s", strtotime($data['tanggal']));
                                            $nomor = $data['forklift'];
                                            $nama = $data['user'];
                                            $sebelum = $data['sebelum'];
                                            $sesudah = $data['sesudah'];
                                            $total = $data['total'];
                                        ?>
                                        <tr>
                                            <td><?= $tanggal; ?></td>
                                            <td><?= $nomor; ?></td>
                                            <td><?= $nama; ?></td>
                                            <td><?= $sebelum; ?></td>
                                            <td><?= $sesudah; ?></td>
                                            <td><?= $total; ?></td>
                                            <?php if ($_SESSION['role'] === 'admin') { ?>
                                            <td>
                                                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit<?= $id; ?>">Edit</button>
                                            </td>
                                            <?php } ?>
                                        </tr>

                                        <!-- Modal Edit -->
                                        <div class="modal fade" id="edit<?= $id; ?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Edit Data Solar</h4>
                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    </div>
                                                    <form method="post">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="id" value="<?= $id; ?>">
                                                            <input type="text" name="forklift" class="form-control" value="<?= $nomor; ?>" required><br>
                                                            <input type="text" name="user" class="form-control" value="<?= $nama; ?>" required><br>
                                                            <input type="text" name="sebelum" class="form-control" value="<?= $sebelum; ?>" required><br>
                                                            <input type="text" name="sesudah" class="form-control" value="<?= $sesudah; ?>" required><br>
                                                            <button type="submit" class="btn btn-primary" name="editsolar">Simpan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
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

<!-- Modal Input -->
<div class="modal fade" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Ambil Solar</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <?php
                    $last = mysqli_query($conn, "SELECT * FROM solar_keluar ORDER BY id DESC LIMIT 1");
                    $dataakhir = mysqli_fetch_array($last);
                    $sebelum = $dataakhir ? $dataakhir['sesudah'] : 0;
                    ?>
                    <select name="forklift" class="form-control" required>
                        <option value="">Pilih Nomor Forklift</option>
                        <?php for($i = 1; $i <= 8; $i++) { ?>
                            <option value="R<?= $i; ?>">R<?= $i; ?></option>
                        <?php } ?>
                    </select>
                    <br>
                    <input type="text" name="user" placeholder="Nama" class="form-control" required>
                    <br>
                    <input type="number" name="sebelum" class="form-control" value="<?= $sebelum; ?>" readonly>
                    <br>
                    <input type="text" name="sesudah" placeholder="Volume Sesudah (Liter)" class="form-control" required>
                    <br>
                    <button type="submit" class="btn btn-primary" name="addsolar">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="js/scripts.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
<script src="assets/demo/datatables-demo.js"></script>
</body>
</html>