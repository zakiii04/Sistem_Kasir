<?php
session_start();
include "../conn/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    die("Akses ditolak!");
}

if (isset($_POST['simpan'])) {
    $nama = $_POST['nama'];
    $mysqli->query("INSERT INTO kategori (nama) VALUES ('$nama')");
    echo "<script>alert('Kategori berhasil ditambahkan'); window.location='kategori.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Tambah Kategori</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #0d6efd, #4facfe);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Poppins, sans-serif;
        }

        .card-modern {
            width: 420px;
            padding: 35px;
            background: #ffffff;
            border-radius: 18px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
            animation: fadeIn .3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(.97);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .btn-primary {
            background: #0d6efd;
            border-radius: 10px;
            padding: 10px;
            font-weight: 600;
        }

        .btn-secondary {
            border-radius: 10px;
            padding: 10px;
            margin-top: 5px;
        }
    </style>
</head>

<body>

    <div class="card-modern">
        <h4 class="mb-3 fw-bold"><i class="bi bi-tags"></i> Tambah Kategori</h4>

        <form method="POST">
            <label>Nama Kategori</label>
            <input type="text" name="nama" class="form-control mb-3" required>

            <button class="btn btn-primary w-100" name="simpan">Simpan</button>
            <a href="kategori.php" class="btn btn-secondary w-100">Kembali</a>
        </form>
    </div>

</body>

</html>