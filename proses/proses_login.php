<?php
session_start();
include "../conn/koneksi.php";

$username = $_POST['username'];
$password = md5($_POST['password']);

$sql = "SELECT * FROM user WHERE username='$username' AND password='$password'";
$query = mysqli_query($mysqli, $sql);

if (mysqli_num_rows($query) > 0) {

    // Ambil data user (penting!)
    $row = mysqli_fetch_assoc($query);

    // Set session lengkap
    $_SESSION['username'] = $row['username'];
    $_SESSION['role'] = $row['role'];   // <--- PENTING!
    $_SESSION['id_user'] = $row['id'];     // opsional

    // Redirect sesuai role
    if ($row['role'] == 'admin') {
        header("Location: ../dashboard.php");
    } else {
        header("Location: ../dashboard_kasir.php"); // Redirect kasir ke dashboard kasir
    }

    exit;

} else {
    echo "<script>alert('Login gagal! Username atau password salah'); window.location='../login.php';</script>";
}
?>