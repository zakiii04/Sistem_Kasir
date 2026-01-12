<?php
session_start();
include "../conn/koneksi.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    die("Akses ditolak!");
}

$id = $_GET['id'];

$q = $mysqli->query("SELECT * FROM user WHERE id='$id'");
$u = $q->fetch_assoc();

if (!$u)
    die("Kasir tidak ditemukan");

if (isset($_POST['update'])) {

    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $role = $_POST['role'];

    if (!empty($_POST['password'])) {
        $password = md5($_POST['password']);
        $mysqli->query("UPDATE user 
                        SET nama='$nama',username='$username',password='$password',role='$role'
                        WHERE id='$id'");
    } else {
        $mysqli->query("UPDATE user 
                        SET nama='$nama',username='$username',role='$role'
                        WHERE id='$id'");
    }

    echo "<script>alert('Kasir berhasil diperbarui'); window.location='karyawan.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Kasir</title>
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
        }
    </style>
</head>

<body>

    <div class="card-form">
        <h4 class="text-center mb-4"><i class="bi bi-pencil-square"></i> Edit Kasir</h4>

        <form method="POST">

            <label>Nama</label>
            <input type="text" name="nama" class="form-control mb-3" value="<?= $u['nama'] ?>" required>

            <label>Username</label>
            <input type="text" name="username" class="form-control mb-3" value="<?= $u['username'] ?>" required>

            <label>Password (kosongkan jika tidak ganti)</label>
            <input type="password" name="password" class="form-control mb-3">

            <label>Role</label>
            <select name="role" class="form-control mb-4">
                <option value="kasir" <?= $u['role'] == 'kasir' ? 'selected' : '' ?>>Kasir</option>
                <option value="admin" <?= $u['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>

            <button type="submit" name="update" class="btn btn-primary w-100">Simpan</button>
            <a href="karyawan.php" class="btn btn-secondary w-100 mt-2">Kembali</a>

        </form>
    </div>

</body>

</html>