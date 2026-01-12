<?php
session_start();
include "../conn/koneksi.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    die("Akses ditolak!");
}

$id = $_GET['id'];

$mysqli->query("DELETE FROM user WHERE id='$id'");

header("Location: karyawan.php");
exit;
?>
