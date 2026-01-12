<?php
session_start();
include "conn/koneksi.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Toko Berkah Jaya - Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            display: flex;
            height: 100vh;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #eef2f3, #dfe9f3);
            font-family: Poppins, sans-serif;
        }



        .card {
            padding: 25px;
            border-radius: 15px;
            width: 380px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            text-align: center;
        }
    </style>

</head>

<body>

    <div class="card">

        <!-- ICON TOKO BAGUS -->
        <i class="bi bi-shop" style="font-size: 60px; color:#007bff;"></i>

        <h3 class="mt-2 mb-4">Toko Berkah Jaya</h3>

        <form id="loginForm" method="POST" action="proses/proses_login.php">

            <label>Username</label>
            <input type="text" name="username" class="form-control mb-3" required>

            <label>Password</label>
            <input type="password" name="password" id="passwordField" class="form-control mb-3" required>

            <button type="submit" class="btn btn-primary w-100">Login</button>



        </form>
    </div>




</body>

</html>