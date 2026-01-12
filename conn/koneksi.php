<?php
$databaseHost     = 'localhost';
$databaseName     = 'php_barang';
$databaseUsername = 'root';
$databasePassword = '';

// Membuat koneksi ke database
$mysqli = new mysqli($databaseHost, $databaseUsername, $databasePassword, $databaseName);

// Mengecek koneksi
if ($mysqli->connect_error) {
    die("Koneksi database gagal: " . $mysqli->connect_error);
}
?>
