<?php
$conn = mysqli_connect("localhost", "root", "", "stockbarang");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

echo "Koneksi database berhasil!<br>";

// Cek tabel stock
$result = mysqli_query($conn, "SHOW TABLES LIKE 'stock'");
if (mysqli_num_rows($result) > 0) {
    echo "Tabel 'stock' ditemukan<br>";
    
    // Tampilkan struktur tabel
    $result = mysqli_query($conn, "DESCRIBE stock");
    echo "<br>Struktur tabel stock:<br>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo $row['Field'] . " - " . $row['Type'] . "<br>";
    }
    
    // Cek data solar
    $result = mysqli_query($conn, "SELECT * FROM stock WHERE namabarang = 'Solar'");
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        echo "<br>Data Solar:<br>";
        echo "ID: " . $row['idbarang'] . "<br>";
        echo "Nama: " . $row['namabarang'] . "<br>";
        echo "Stock: " . $row['stock'] . "<br>";
    } else {
        echo "<br>Tidak ada data Solar di tabel stock<br>";
    }
} else {
    echo "Tabel 'stock' tidak ditemukan<br>";
}

// Cek tabel solar_keluar
$result = mysqli_query($conn, "SHOW TABLES LIKE 'solar_keluar'");
if (mysqli_num_rows($result) > 0) {
    echo "<br>Tabel 'solar_keluar' ditemukan<br>";
    
    // Tampilkan struktur tabel
    $result = mysqli_query($conn, "DESCRIBE solar_keluar");
    echo "<br>Struktur tabel solar_keluar:<br>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo $row['Field'] . " - " . $row['Type'] . "<br>";
    }
} else {
    echo "<br>Tabel 'solar_keluar' tidak ditemukan<br>";
}

mysqli_close($conn);
?> 