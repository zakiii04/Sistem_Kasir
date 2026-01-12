<?php 
include_once("../conn/koneksi.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil data barang dari database
    $result = mysqli_query($mysqli, "SELECT * FROM barang WHERE id=$id");
    $barang_data = mysqli_fetch_array($result);

        $nama = $barang_data['nama'];
        $harga = $barang_data['harga'];
        $jumlah = $barang_data['jumlah'];
        $gambar = $barang_data['gambar'];
}
?>
