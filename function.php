<?php
date_default_timezone_set('Asia/Jakarta');

// Fungsi untuk membersihkan input
function cleanInput($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}

// Fungsi untuk menampilkan pesan error
function showError($message) {
    $_SESSION['error'] = $message;
}

// Fungsi untuk menampilkan pesan sukses
function showSuccess($message) {
    $_SESSION['success'] = $message;
}

// Membuat koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "stockbarang");
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Menambah barang baru
if(isset($_POST['addnewbarang'])) {
    $namabarang = cleanInput($_POST['namabarang']);
    $deskripsi = cleanInput($_POST['deskripsi']);
    $stock = (int)$_POST['stock'];

    if(empty($namabarang) || $stock < 0) {
        showError("Data tidak valid");
        header('location:index.php');
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO stock (namabarang, deskripsi, stock) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $namabarang, $deskripsi, $stock);
    
    if($stmt->execute()) {
        showSuccess("Barang berhasil ditambahkan");
    } else {
        showError("Gagal menambahkan barang");
    }
    $stmt->close();
    header('location:index.php');
    exit();
}

// Menambah barang masuk
if(isset($_POST['barangmasuk'])) {
    $barangnya = (int)$_POST['barangnya'];
    $keterangan = cleanInput($_POST['keterangan']);
    $qty = (int)$_POST['qty'];

    if($qty <= 0) {
        showError("Quantity harus lebih dari 0");
        header('location:masuk.php');
        exit();
    }

    // Menggunakan transaction untuk memastikan data konsisten
    $conn->begin_transaction();
    try {
        // Cek stok sekarang
        $stmt = $conn->prepare("SELECT stock FROM stock WHERE idbarang = ? FOR UPDATE");
        $stmt->bind_param("i", $barangnya);
        $stmt->execute();
        $result = $stmt->get_result();
        $stockData = $result->fetch_assoc();
        
        if(!$stockData) {
            throw new Exception("Barang tidak ditemukan");
        }

        $stocksekarang = $stockData['stock'];
        $tambahkanstocksekarangdenganquantity = $stocksekarang + $qty;

        // Insert ke tabel masuk
        $stmt = $conn->prepare("INSERT INTO masuk (idbarang, keterangan, qty) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $barangnya, $keterangan, $qty);
        $stmt->execute();

        // Update stok
        $stmt = $conn->prepare("UPDATE stock SET stock = ? WHERE idbarang = ?");
        $stmt->bind_param("ii", $tambahkanstocksekarangdenganquantity, $barangnya);
        $stmt->execute();

        $conn->commit();
        showSuccess("Barang masuk berhasil ditambahkan");
    } catch (Exception $e) {
        $conn->rollback();
        showError("Gagal: " . $e->getMessage());
    }
    header('location:masuk.php');
    exit();
}

//menambah barang keluar
if(isset($_POST['addbarangkeluar'])){
    $barangnya = (int)$_POST['barangnya'];
    $penerima = cleanInput($_POST['penerima']);
    $qty = (int)$_POST['qty'];

    if($qty <= 0) {
        showError("Quantity harus lebih dari 0");
        header('location:keluar.php');
        exit();
    }

    // Menggunakan transaction untuk memastikan data konsisten
    $conn->begin_transaction();
    try {
        // Cek stok sekarang
        $stmt = $conn->prepare("SELECT stock FROM stock WHERE idbarang = ? FOR UPDATE");
        $stmt->bind_param("i", $barangnya);
        $stmt->execute();
        $result = $stmt->get_result();
        $stockData = $result->fetch_assoc();
        
        if(!$stockData) {
            throw new Exception("Barang tidak ditemukan");
        }

        $stocksekarang = $stockData['stock'];
        
        if($stocksekarang < $qty) {
            throw new Exception("Stok tidak mencukupi");
        }
        
        $tambahkanstocksekarangdenganquantity = $stocksekarang - $qty;

        // Insert ke tabel keluar
        $stmt = $conn->prepare("INSERT INTO keluar (idbarang, penerima, qty) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $barangnya, $penerima, $qty);
        $stmt->execute();

        // Update stok
        $stmt = $conn->prepare("UPDATE stock SET stock = ? WHERE idbarang = ?");
        $stmt->bind_param("ii", $tambahkanstocksekarangdenganquantity, $barangnya);
        $stmt->execute();

        $conn->commit();
        showSuccess("Barang keluar berhasil ditambahkan");
    } catch (Exception $e) {
        $conn->rollback();
        showError("Gagal: " . $e->getMessage());
    }
    header('location:keluar.php');
    exit();
}

//update info barang
if(isset($_POST['updatedatabarang'])){
    $idb = (int)$_POST['idb'];
    $namabarang = cleanInput($_POST['namabarang']);
    $deskripsi = cleanInput($_POST['deskripsi']);

    try {
        $stmt = $conn->prepare("UPDATE stock SET namabarang = ?, deskripsi = ? WHERE idbarang = ?");
        $stmt->bind_param("ssi", $namabarang, $deskripsi, $idb);
        
        if(!$stmt->execute()) {
            throw new Exception("Gagal mengupdate data barang");
        }
        
        showSuccess("Data barang berhasil diupdate");
    } catch (Exception $e) {
        showError("Gagal: " . $e->getMessage());
    }
    header('location:index.php');
    exit();
}

//menghapus barang dari stock
if(isset($_POST['hapusbarang'])){
    $idb = (int)$_POST['idb'];

    try {
        $stmt = $conn->prepare("DELETE FROM stock WHERE idbarang = ?");
        $stmt->bind_param("i", $idb);
        
        if(!$stmt->execute()) {
            throw new Exception("Gagal menghapus barang");
        }
        
        showSuccess("Barang berhasil dihapus");
    } catch (Exception $e) {
        showError("Gagal: " . $e->getMessage());
    }
    header('location:index.php');
    exit();
}

//mengubah data barang masuk
if (isset($_POST['updatedatabarangmasuk'])) {
    $idb = (int)$_POST['idb'];  // ID barang
    $idm = (int)$_POST['idm'];  // ID masuk
    $keterangan = cleanInput($_POST['keterangan']);
    $qtyBaru = (int)$_POST['qty'];

    if($qtyBaru <= 0) {
        showError("Quantity harus lebih dari 0");
        header('location:masuk.php');
        exit();
    }

    $conn->begin_transaction();
    try {
        // Ambil data qty lama
        $stmt = $conn->prepare("SELECT qty FROM masuk WHERE idmasuk = ?");
        $stmt->bind_param("i", $idm);
        $stmt->execute();
        $result = $stmt->get_result();
        $dataLama = $result->fetch_assoc();
        
        if(!$dataLama) {
            throw new Exception("Data masuk tidak ditemukan");
        }
        
        $qtyLama = $dataLama['qty'];

        // Ambil stok sekarang
        $stmt = $conn->prepare("SELECT stock FROM stock WHERE idbarang = ? FOR UPDATE");
        $stmt->bind_param("i", $idb);
        $stmt->execute();
        $result = $stmt->get_result();
        $dataStok = $result->fetch_assoc();
        
        if(!$dataStok) {
            throw new Exception("Barang tidak ditemukan");
        }
        
        $stokSekarang = $dataStok['stock'];

        // Hitung selisih qty dan stok baru
        $selisih = $qtyBaru - $qtyLama;
        $stokBaru = $stokSekarang + $selisih;

        if ($stokBaru < 0) {
            throw new Exception("Stok tidak mencukupi");
        }

        // Update stok di tabel stock
        $stmt = $conn->prepare("UPDATE stock SET stock = ? WHERE idbarang = ?");
        $stmt->bind_param("ii", $stokBaru, $idb);
        $stmt->execute();

        // Update qty di tabel masuk
        $stmt = $conn->prepare("UPDATE masuk SET qty = ?, keterangan = ? WHERE idmasuk = ?");
        $stmt->bind_param("isi", $qtyBaru, $keterangan, $idm);
        $stmt->execute();

        $conn->commit();
        showSuccess("Data barang masuk berhasil diupdate");
    } catch (Exception $e) {
        $conn->rollback();
        showError("Gagal: " . $e->getMessage());
    }
    header('location:masuk.php');
    exit();
}

//menghapus barang masuk
if(isset($_POST['hapusbarangmasuk'])){
    $idb = (int)$_POST['idb'];
    $qty = (int)$_POST['kty'];
    $idm = (int)$_POST['idm'];

    $conn->begin_transaction();
    try {
        // Get current stock
        $stmt = $conn->prepare("SELECT stock FROM stock WHERE idbarang = ? FOR UPDATE");
        $stmt->bind_param("i", $idb);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        
        if(!$data) {
            throw new Exception("Barang tidak ditemukan");
        }
        
        $stok = $data['stock'];
        $stokBaru = $stok - $qty;

        if($stokBaru < 0) {
            throw new Exception("Stok tidak mencukupi untuk dihapus");
        }

        // Update stock
        $stmt = $conn->prepare("UPDATE stock SET stock = ? WHERE idbarang = ?");
        $stmt->bind_param("ii", $stokBaru, $idb);
        $stmt->execute();

        // Delete from masuk
        $stmt = $conn->prepare("DELETE FROM masuk WHERE idmasuk = ?");
        $stmt->bind_param("i", $idm);
        $stmt->execute();

        $conn->commit();
        showSuccess("Data barang masuk berhasil dihapus");
    } catch (Exception $e) {
        $conn->rollback();
        showError("Gagal: " . $e->getMessage());
    }
    header('location:masuk.php');
    exit();
}

// Menyimpan data solar keluar
if (isset($_POST['addsolar'])) {
    $forklift = cleanInput($_POST['forklift']);
    $user = cleanInput($_POST['user']);
    $sebelum = (float)str_replace(',', '.', $_POST['sebelum']);
    $sesudah = (float)str_replace(',', '.', $_POST['sesudah']);
    $tanggal = date('Y-m-d H:i:s');

    if($sebelum < 0 || $sesudah < 0) {
        showError("Nilai tidak boleh negatif");
        header('location:solar.php');
        exit();
    }

    $total = abs($sebelum - $sesudah);

    $stmt = $conn->prepare("INSERT INTO solar_keluar (tanggal, forklift, user, sebelum, sesudah, total) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssddd", $tanggal, $forklift, $user, $sebelum, $sesudah, $total);
    
    if($stmt->execute()) {
        showSuccess("Data solar berhasil disimpan");
    } else {
        showError("Gagal menyimpan data solar");
    }
    $stmt->close();
    header('location:solar.php');
    exit();
}

function tambahSolarKeluar($data) {
    global $conn;
    
    $forklift = htmlspecialchars($data['forklift']);
    $user = htmlspecialchars($data['user']);
    $sebelum = floatval($data['sebelum']);
    $sesudah = floatval($data['sesudah']);
    
    try {
        // Start transaction
        mysqli_begin_transaction($conn);
        
        // Insert into solar_keluar
        $query = "INSERT INTO solar_keluar (tanggal, forklift, user, sebelum, sesudah) VALUES (NOW(), ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        
        if (!$stmt) {
            throw new Exception("Error preparing statement: " . mysqli_error($conn));
        }
        
        mysqli_stmt_bind_param($stmt, "ssdd", $forklift, $user, $sebelum, $sesudah);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error executing statement: " . mysqli_error($conn));
        }
        
        // Commit transaction
        mysqli_commit($conn);
        return true;
        
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        error_log("Error in tambahSolarKeluar: " . $e->getMessage());
        return false;
    }
}

function editSolarKeluar($data) {
    global $conn;
    
    $id = $data['id'];
    $forklift = htmlspecialchars($data['forklift']);
    $user = htmlspecialchars($data['user']);
    $sebelum = floatval($data['sebelum']);
    $sesudah = floatval($data['sesudah']);
    
    try {
        // Start transaction
        mysqli_begin_transaction($conn);
        
        // Get old sesudah value
        $query = "SELECT sesudah FROM solar_keluar WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $oldSesudah = floatval($row['sesudah']);
        
        // Update solar_keluar
        $query = "UPDATE solar_keluar SET forklift = ?, user = ?, sebelum = ?, sesudah = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssddi", $forklift, $user, $sebelum, $sesudah, $id);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Gagal mengupdate data solar keluar");
        }
        
        // Update stock in stock table
        // Add back old value and subtract new value
        $query = "UPDATE stock SET stock = stock + ? - ? WHERE namabarang = 'Solar'";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "dd", $oldSesudah, $sesudah);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Gagal mengupdate stok solar");
        }
        
        // Commit transaction
        mysqli_commit($conn);
        return true;
        
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        return false;
    }
}

function hapusSolarKeluar($id) {
    global $conn;
    
    try {
        // Start transaction
        mysqli_begin_transaction($conn);
        
        // Get sesudah value before deleting
        $query = "SELECT sesudah FROM solar_keluar WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $sesudah = floatval($row['sesudah']);
        
        // Delete from solar_keluar
        $query = "DELETE FROM solar_keluar WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Gagal menghapus data solar keluar");
        }
        
        // Update stock in stock table
        // Add back the deleted value
        $query = "UPDATE stock SET stock = stock + ? WHERE namabarang = 'Solar'";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "d", $sesudah);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Gagal mengupdate stok solar");
        }
        
        // Commit transaction
        mysqli_commit($conn);
        return true;
        
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        return false;
    }
}

function tambahStokSolar($data) {
    global $conn;
    
    $jumlah = floatval($data['jumlah']);
    $tanggal = $data['tanggal_kedatangan'];
    
    try {
        // Start transaction
        mysqli_begin_transaction($conn);
        
        // Insert into solar_masuk for tracking
        $query = "INSERT INTO solar_masuk (tanggal, jumlah) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sd", $tanggal, $jumlah);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Gagal mencatat data kedatangan solar");
        }
        
        // Update or insert into stock table
        $query = "INSERT INTO stock (namabarang, deskripsi, stock) 
                 VALUES ('Solar', 'Bahan bakar solar', ?) 
                 ON DUPLICATE KEY UPDATE stock = stock + ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "dd", $jumlah, $jumlah);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Gagal mengupdate stok solar");
        }
        
        // Commit transaction
        mysqli_commit($conn);
        return true;
        
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        return false;
    }
}

// Add new stock solar
if(isset($_POST['addstocksolar'])) {
    $jumlah = floatval($_POST['jumlah']);
    $tanggal = $_POST['tanggal'];
    $keterangan = cleanInput($_POST['keterangan']);

    if($jumlah <= 0) {
        showError("Jumlah harus lebih dari 0");
        header('location:stock_solar.php');
        exit();
    }

    try {
        $stmt = $conn->prepare("INSERT INTO solar_masuk (tanggal, jumlah, keterangan) VALUES (?, ?, ?)");
        $stmt->bind_param("sds", $tanggal, $jumlah, $keterangan);
        
        if($stmt->execute()) {
            showSuccess("Stock solar berhasil ditambahkan");
        } else {
            throw new Exception("Gagal menambahkan stock solar");
        }
    } catch (Exception $e) {
        showError("Error: " . $e->getMessage());
    }
    header('location:stock_solar.php');
    exit();
}

// Update stock solar
if(isset($_POST['updatestocksolar'])) {
    $id = (int)$_POST['id'];
    $jumlahBaru = floatval($_POST['jumlah']);
    $keterangan = cleanInput($_POST['keterangan']);

    if($jumlahBaru <= 0) {
        showError("Jumlah harus lebih dari 0");
        header('location:stock_solar.php');
        exit();
    }

    $conn->begin_transaction();
    try {
        // Get old amount
        $stmt = $conn->prepare("SELECT jumlah FROM solar_masuk WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        
        if(!$data) {
            throw new Exception("Data tidak ditemukan");
        }
        
        $jumlahLama = $data['jumlah'];
        
        // Update the record
        $stmt = $conn->prepare("UPDATE solar_masuk SET jumlah = ?, keterangan = ? WHERE id = ?");
        $stmt->bind_param("dsi", $jumlahBaru, $keterangan, $id);
        
        if(!$stmt->execute()) {
            throw new Exception("Gagal mengupdate data");
        }

        $conn->commit();
        showSuccess("Data berhasil diupdate");
    } catch (Exception $e) {
        $conn->rollback();
        showError("Error: " . $e->getMessage());
    }
    header('location:stock_solar.php');
    exit();
}

// Delete stock solar
if(isset($_POST['hapusstocksolar'])) {
    $id = (int)$_POST['id'];
    $jumlah = floatval($_POST['jumlah']);

    $conn->begin_transaction();
    try {
        // Delete the record
        $stmt = $conn->prepare("DELETE FROM solar_masuk WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if(!$stmt->execute()) {
            throw new Exception("Gagal menghapus data");
        }

        $conn->commit();
        showSuccess("Data berhasil dihapus");
    } catch (Exception $e) {
        $conn->rollback();
        showError("Error: " . $e->getMessage());
    }
    header('location:stock_solar.php');
    exit();
}

?>