<?php
require 'function.php';

$sql = "CREATE TABLE IF NOT EXISTS `solar_masuk` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `tanggal` datetime NOT NULL,
    `jumlah` decimal(10,2) NOT NULL,
    `keterangan` text DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

if (mysqli_query($conn, $sql)) {
    echo "Table solar_masuk created successfully<br>";
} else {
    echo "Error creating table solar_masuk: " . mysqli_error($conn) . "<br>";
}

// Create keluar table
$sql = "CREATE TABLE IF NOT EXISTS `keluar` (
    `idkeluar` int(11) NOT NULL AUTO_INCREMENT,
    `idbarang` int(11) NOT NULL,
    `qty` int(11) NOT NULL,
    `penerima` varchar(100) NOT NULL,
    `tanggal` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`idkeluar`),
    KEY `idbarang` (`idbarang`),
    CONSTRAINT `keluar_ibfk_1` FOREIGN KEY (`idbarang`) REFERENCES `stock` (`idbarang`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

if (mysqli_query($conn, $sql)) {
    echo "Table keluar created successfully<br>";
} else {
    echo "Error creating table keluar: " . mysqli_error($conn) . "<br>";
}

// Create masuk table
$sql = "CREATE TABLE IF NOT EXISTS `masuk` (
    `idmasuk` int(11) NOT NULL AUTO_INCREMENT,
    `idbarang` int(11) NOT NULL,
    `qty` int(11) NOT NULL,
    `keterangan` text DEFAULT NULL,
    `tanggal` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`idmasuk`),
    KEY `idbarang` (`idbarang`),
    CONSTRAINT `masuk_ibfk_1` FOREIGN KEY (`idbarang`) REFERENCES `stock` (`idbarang`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

if (mysqli_query($conn, $sql)) {
    echo "Table masuk created successfully<br>";
} else {
    echo "Error creating table masuk: " . mysqli_error($conn) . "<br>";
}
?> 