<?php
include "../conn/koneksi.php";

if (isset($_POST['update'])) {

    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $jumlah = $_POST['jumlah'];
    $kategori = $_POST['kategori'];

    $gambar_lama = $_POST['gambar_lama'];
    $gambar_baru = $gambar_lama;

    // Jika upload gambar baru
    if (!empty($_FILES['gambar']['name'])) {

        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $nama_file = uniqid() . "." . $ext;
        $upload_path = "../uploads/" . $nama_file;

        // Upload gambar baru
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {

            // hapus gambar lama
            if (!empty($gambar_lama) && file_exists("../uploads/" . $gambar_lama)) {
                unlink("../uploads/" . $gambar_lama);
            }

            $gambar_baru = $nama_file;
        }
    }

    // UPDATE DATABASE
    $update = $mysqli->query("
        UPDATE barang 
        SET nama='$nama',
            harga='$harga',
            jumlah='$jumlah',
            kategori='$kategori',
            gambar='$gambar_baru'
        WHERE id=$id
    ");

    if ($update) {
        echo "<script>alert('Produk berhasil diperbarui'); window.location='../index.php';</script>";
    } else {
        echo "<script>alert('Gagal update data'); window.location='../index.php';</script>";
    }
}
?>