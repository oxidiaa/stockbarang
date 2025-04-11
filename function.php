<?php
date_default_timezone_set('Asia/Jakarta');



//Membuat koneksi ke database
$conn = mysqli_connect("localhost","root","","stockbarang");


//Menambah barang baru
if(isset($_POST['addnewbarang'])){
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];
    $stock = $_POST['stock'];

    $addtotable = mysqli_query($conn,"insert into stock (namabarang, deskripsi, stock) values('$namabarang','$deskripsi','$stock')");
    if($addtotable){
        header('location:index.php');
    }else{
        echo'Gagal';
        header('location:index.php');
    }
}


//menambah barang masuk
if(isset($_POST['barangmasuk'])){
    $barangnya = $_POST['barangnya'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $cekstocksekarang = mysqli_query($conn,"select * from stock where idbarang='$barangnya'");
    $ambildatanya = mysqli_fetch_array($cekstocksekarang);

    $stocksekarang = $ambildatanya['stock'];
    $tambahkanstocksekarangdenganquantity = $stocksekarang+$qty;

    $addtomasuk = mysqli_query($conn,"insert into masuk (idbarang, keterangan, qty) values('$barangnya','$penerima','$qty')");
    $updatestockmasuk = mysqli_query($conn,"update stock set stock='$tambahkanstocksekarangdenganquantity' where idbarang='$barangnya'");
    if($addtomasuk&&$updatestockmasuk){
        header('location:masuk.php');
    }else{
        echo'Gagal';
        header('location:masuk.php');
    }
}


//menambah barang keluar
if(isset($_POST['addbarangkeluar'])){
    $barangnya = $_POST['barangnya'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $cekstocksekarang = mysqli_query($conn,"select * from stock where idbarang='$barangnya'");
    $ambildatanya = mysqli_fetch_array($cekstocksekarang);

    $stocksekarang = $ambildatanya['stock'];
    $tambahkanstocksekarangdenganquantity = $stocksekarang-$qty;

    $addtokeluar = mysqli_query($conn,"insert into keluar (idbarang, penerima, qty) values('$barangnya','$penerima','$qty')");
    $updatestockmasuk = mysqli_query($conn,"update stock set stock='$tambahkanstocksekarangdenganquantity' where idbarang='$barangnya'");
    if($addtokeluar&&$updatestockmasuk){
        header('location:keluar.php');
    }else{
        echo'Gagal';
        header('location:keluar.php');
    }
}



//update info barang
if(isset($_POST['updatedatabarang'])){
    $idb = $_POST['idb'];
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];

    $update = mysqli_query($conn,"update stock set namabarang='$namabarang', deskripsi='$deskripsi' where idbarang='$idb'");
    if($update){
        header('location:index.php');
    }else{
        echo'Gagal';
        header('location:index.php');
    }
}


//menghapus barang dari stock
if(isset($_POST['hapusbarang'])){
    $idb = $_POST['idb'];

    $hapus = mysqli_query($conn,"delete from stock where idbarang='$idb'");
    if($hapus){
        header('location:index.php');
    }else{
        echo'Gagal';
        header('location:index.php');
    }
};



//mengubah data barang masuk
if (isset($_POST['updatedatabarangmasuk'])) {
    $idb = $_POST['idb'];  // ID barang
    $idm = $_POST['idm'];  // ID masuk
    $deskripsi = htmlspecialchars($_POST['keterangan']);
    $qtyBaru = (int)$_POST['qty'];

    // Ambil data qty lama dan stok sekarang
    $ambilData = mysqli_query($conn, "SELECT qty FROM masuk WHERE idmasuk='$idm'");
    $dataLama = mysqli_fetch_array($ambilData);
    $qtyLama = $dataLama['qty'];

    $ambilStok = mysqli_query($conn, "SELECT stock FROM stock WHERE idbarang='$idb'");
    $dataStok = mysqli_fetch_array($ambilStok);
    $stokSekarang = $dataStok['stock'];

    // Hitung selisih qty
    $selisih = $qtyBaru - $qtyLama;
    $stokBaru = $stokSekarang + $selisih;

    if ($stokBaru >= 0) {  // Pastikan stok tidak negatif
        // Update stok di tabel stock
        $updateStock = mysqli_query($conn, "UPDATE stock SET stock='$stokBaru' WHERE idbarang='$idb'");

        // Update qty di tabel masuk
        $updateMasuk = mysqli_query($conn, "UPDATE masuk SET qty='$qtyBaru', keterangan='$deskripsi' WHERE idmasuk='$idm'");

        if ($updateStock && $updateMasuk) {
            header('location:masuk.php?status=success');
        } else {
            echo 'Gagal memperbarui data.';
        }
    } else {
        echo 'Gagal: Stok tidak mencukupi.';
    }
}







//menghapus barang masuk
if(isset($_POST['hapusbarangmasuk'])){
    $idb = $_POST['idb'];
    $qty = $_POST['kty'];
    $idm = $_POST['idm'];

    $getdatastock = mysqli_query($conn,"select * from stock where idbarang='$idb'");
    $data = mysqli_fetch_array($getdatastock);
    $stok = $data['stock'];

    $selisih = $stok=$qty;

    $update = mysqli_query($conn,"update stock set stock='$selisih' where idbarang='$idb'");
    $hapusdata = mysqli_query($conn,"delete from masuk where idmasuk='$idm'");

    if($update&&$hapusdata){
        header('location:masuk.php');
    }else{
        header('location:masuk.php');
    }
}



// Menyimpan data solar keluar
if (isset($_POST['addsolar'])) {
    $forklift = $_POST['forklift'];
    $user = $_POST['user'];
    $sebelum = (float)str_replace(',', '.', $_POST['sebelum']);
    $sesudah = (float)str_replace(',', '.', $_POST['sesudah']);
    $tanggal = date('Y-m-d H:i:s');

    $total = abs($sebelum - $sesudah); // Selalu hasil positif

    $insert = mysqli_query($conn, "INSERT INTO solar_keluar (tanggal, forklift, user, sebelum, sesudah, total) VALUES ('$tanggal', '$forklift', '$user', '$sebelum', '$sesudah', '$total')");

    if ($insert) {
        header('location:solar.php');
    } else {
        echo "Gagal menyimpan data solar.";
    }
}




?>