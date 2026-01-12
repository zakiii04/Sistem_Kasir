<?php
session_start();
include "../conn/koneksi.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    die("Akses ditolak!");
}

if (isset($_POST['simpan'])) {

    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $role = $_POST['role'];

    $mysqli->query("INSERT INTO user (nama,username,password,role)
                    VALUES('$nama','$username','$password','$role')");

    echo "<script>alert('Kasir berhasil ditambahkan'); window.location='karyawan.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Tambah Kasir</title>
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

        .card-form {
            width: 420px;
            background: white;
            border-radius: 18px;
            padding: 30px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            animation: fadeIn .4s ease;
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
            width: 100%;
            border-radius: 10px;
        }

        .btn-secondary {
            width: 100%;
            margin-top: 8px;
            border-radius: 10px;
        }
    </style>
</head>

<body>

    <div class="card-form">
        <h4 class="text-center mb-4"><i class="bi bi-person-plus"></i> Tambah Kasir</h4>

        <form method="POST">

            <label>Nama</label>
            <input type="text" name="nama" class="form-control mb-3" required>

            <label>Username</label>
            <input type="text" name="username" class="form-control mb-3" required>

            <label>Password</label>
            <input type="password" name="password" class="form-control mb-3" required>

            <label>Role</label>
            <select name="role" class="form-control mb-4">
                <option value="kasir">Kasir</option>
                <option value="admin">Admin</option>
            </select>

            <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
            <a href="karyawan.php" class="btn btn-secondary">Kembali</a>
        </form>

    </div>

</body>

</html>