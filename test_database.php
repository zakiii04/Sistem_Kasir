<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Koneksi Database - PHP Barang</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            max-width: 800px;
            width: 100%;
        }
        h1 {
            color: #667eea;
            margin-bottom: 30px;
            text-align: center;
            font-size: 2em;
        }
        .status-box {
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: bold;
            text-align: center;
            font-size: 1.2em;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 2px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 2px solid #f5c6cb;
        }
        .info-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .info-section h3 {
            color: #495057;
            margin-bottom: 15px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        th {
            background: #667eea;
            color: white;
            font-weight: bold;
        }
        tr:hover {
            background: #f8f9fa;
        }
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: bold;
        }
        .badge-admin {
            background: #dc3545;
            color: white;
        }
        .badge-kasir {
            background: #28a745;
            color: white;
        }
        .credential-box {
            background: #e7f3ff;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 10px 0;
        }
        .credential-box strong {
            color: #667eea;
        }
        .back-btn {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            transition: background 0.3s;
        }
        .back-btn:hover {
            background: #5568d3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔌 Test Koneksi Database</h1>
        
        <?php
        require_once 'conn/koneksi.php';
        
        // Test koneksi
        if ($mysqli->connect_error) {
            echo '<div class="status-box error">
                    ❌ Koneksi Gagal: ' . $mysqli->connect_error . '
                  </div>';
        } else {
            echo '<div class="status-box success">
                    ✅ Koneksi Database Berhasil!
                  </div>';
            
            // Informasi Database
            echo '<div class="info-section">
                    <h3>📊 Informasi Database</h3>
                    <p><strong>Host:</strong> ' . $databaseHost . '</p>
                    <p><strong>Database:</strong> ' . $databaseName . '</p>
                    <p><strong>Character Set:</strong> ' . $mysqli->character_set_name() . '</p>
                    <p><strong>Server Version:</strong> ' . $mysqli->server_info . '</p>
                  </div>';
            
            // Cek Tabel
            echo '<div class="info-section">
                    <h3>📋 Tabel Database</h3>';
            
            $tables = $mysqli->query("SHOW TABLES");
            if ($tables) {
                echo '<ul>';
                while ($row = $tables->fetch_array()) {
                    $tableName = $row[0];
                    $count = $mysqli->query("SELECT COUNT(*) as total FROM $tableName")->fetch_assoc()['total'];
                    echo "<li><strong>$tableName</strong> - $count records</li>";
                }
                echo '</ul>';
            }
            echo '</div>';
            
            // User Credentials
            echo '<div class="info-section">
                    <h3>👤 User Credentials</h3>';
            
            $users = $mysqli->query("SELECT id, nama, username, role FROM user");
            if ($users && $users->num_rows > 0) {
                echo '<table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Password</th>
                                <th>Role</th>
                            </tr>
                        </thead>
                        <tbody>';
                
                while ($user = $users->fetch_assoc()) {
                    $badgeClass = $user['role'] == 'admin' ? 'badge-admin' : 'badge-kasir';
                    echo '<tr>
                            <td>' . $user['id'] . '</td>
                            <td>' . $user['nama'] . '</td>
                            <td><code>' . $user['username'] . '</code></td>
                            <td><code>password</code></td>
                            <td><span class="badge ' . $badgeClass . '">' . strtoupper($user['role']) . '</span></td>
                          </tr>';
                }
                
                echo '</tbody></table>';
            }
            echo '</div>';
            
            // Quick Info
            echo '<div class="info-section">
                    <h3>🚀 Login Information</h3>
                    <div class="credential-box">
                        <p><strong>Admin:</strong></p>
                        <p>Username: <code>admin</code> | Password: <code>password</code></p>
                    </div>
                    <div class="credential-box">
                        <p><strong>Kasir:</strong></p>
                        <p>Username: <code>kasir1</code> | Password: <code>password</code></p>
                    </div>
                  </div>';
        }
        ?>
        
        <div style="text-align: center;">
            <a href="login.php" class="back-btn">🔐 Login ke Aplikasi</a>
        </div>
    </div>
</body>
</html>
