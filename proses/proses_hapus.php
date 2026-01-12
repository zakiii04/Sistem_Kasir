<?php
include "../conn/koneksi.php";

// CEK APAKAH ADA ID
if (!isset($_GET['id'])) {
    echo "<script>alert('ID tidak ditemukan!'); window.location='../index.php';</script>";
    exit;
}

$id = intval($_GET['id']);

// AMBIL DATA BARANG
$q = $mysqli->query("SELECT gambar FROM barang WHERE id = $id");

if ($q->num_rows == 0) {
    echo "<script>alert('Barang tidak ditemukan!'); window.location='../index.php';</script>";
    exit;
}

$data = $q->fetch_assoc();
$gambar = $data['gambar'];

// HAPUS FILE GAMBAR
if (!empty($gambar) && file_exists("../uploads/" . $gambar)) {
    unlink("../uploads/" . $gambar);
}

// HAPUS DATA BARANG DI DATABASE
$hapus = $mysqli->query("DELETE FROM barang WHERE id = $id");

// CEK BERHASIL ATAU TIDAK
if ($hapus) {
    echo "<script>alert('Barang berhasil dihapus'); window.location='../index.php';</script>";
} else {
    echo "<script>alert('Gagal menghapus barang'); window.location='../index.php';</script>";
}

?>
