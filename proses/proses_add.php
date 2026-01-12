<?php
include "../conn/koneksi.php";

if (isset($_POST['nama'])) {

    $nama     = $mysqli->real_escape_string($_POST['nama']);
    $harga    = intval($_POST['harga']);
    $jumlah   = intval($_POST['jumlah']);
    $kategori = $mysqli->real_escape_string($_POST['kategori']);

    // ===============================
    // PROSES UPLOAD GAMBAR
    // ===============================
    $gambar = "";

    if (isset($_FILES['gambar']) && $_FILES['gambar']['name'] != "") {

        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $nama_file = uniqid() . "." . $ext;

        $target = "../uploads/" . $nama_file;

        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
            $gambar = $nama_file;
        }
    }

    // ===============================
    // SIMPAN KE DATABASE
    // ===============================
    $sql = "
        INSERT INTO barang (nama, harga, jumlah, kategori, gambar)
        VALUES ('$nama', '$harga', '$jumlah', '$kategori', '$gambar')
    ";

    if ($mysqli->query($sql)) {
        echo "<script>alert('Barang berhasil ditambahkan!'); 
              window.location='../index.php';</script>";
    } else {
        echo "Error: " . $mysqli->error;
    }

} else {
    echo "Invalid request!";
}
?>
