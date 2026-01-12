<?php
session_start();
include "../conn/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    die("Akses ditolak!");
}

$id = $_GET['id'];
$mysqli->query("DELETE FROM kategori WHERE id='$id'");
echo "<script>alert('Kategori berhasil dihapus'); window.location='kategori.php';</script>";
exit;
?>