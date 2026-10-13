<?php
session_start();
require 'koneksi.php';

// Jika sudah login, langsung lempar ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Mendukung password teks biasa (akun lama) atau password_hash (akun baru)
    if ($user && ($password === $user['password'] || password_verify($password, $user['password']))) {
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Username atau Password Anda salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sipersa Desa</title>
    <!-- MENGGUNAKAN FILE LOKAL ANDA -->
    <link href="bootstrap.min.css" rel="stylesheet">
    <style>
        /* NUANSA HIJAU PEDESAAN: Kombinasi gradasi hijau alam asri (Forest & Nature Green) */
        body { 
            background: linear-gradient(135deg, #194d33 0%, #2e7d32 50%, #4caf50 100%); 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            min-height: 100vh; 
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            position: relative;
        }

        /* Overlay efek halus agar gradasi lebih tenang dan elegan */
        body::before {
            content: "";
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle, transparent 20%, rgba(0, 0, 0, 0.15) 100%);
            z-index: 1;
        }

        /* Kartu Login Modern dengan bayangan halus */
        .login-card { 
            background: #ffffff; 
            padding: 40px; 
            border-radius: 16px; 
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25); 
            width: 100%; 
            max-width: 420px; 
            position: relative;
            z-index: 2;
        }

        /* Efek Kotak Input Saat Diklik */
        .form-control:focus {
            border-color: #2e7d32;
            box-shadow: 0 0 0 0.25rem rgba(46, 125, 50, 0.2);
        }

        /* Tombol Hijau Daun Selaras */
        .btn-green-desa {
            background-color: #2e7d32;
            color: white;
            border: none;
            transition: background-color 0.2s ease;
        }
        .btn-green-desa:hover {
            background-color: #1b5e20;
            color: white;
        }
    </style>
</head>
<body>

<div class="login-card">
    <!-- Icon Rumah / Desa Elegan Bermotif Hijau -->
    <div class="text-center mb-3">
        <div class="d-inline-flex p-3 rounded-circle" style="background-color: #e8f5e9; color: #2e7d32;">
            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
        </div>
    </div>

    <h3 class="text-center fw-bold mb-1" style="color: #1b5e20;">Login Sipersa</h3>
    <p class="text-center text-muted small mb-4">Sistem Pelayanan Surat Administrasi Desa</p>
    
    <!-- Notifikasi Error -->
    <?php if (isset($error)): ?>
        <div class="alert alert-danger py-2 px-3 small text-center mb-3 border-0 shadow-sm" role="alert">
            <strong>Galat!</strong> <?= $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label fw-bold small text-secondary">Username</label>
            <input type="text" name="username" class="form-control py-2.5" required placeholder="Masukkan Username (admin)">
        </div>
        
        <div class="mb-4">
            <label class="form-label fw-bold small text-secondary">Password</label>
            <input type="password" name="password" class="form-control py-2.5" required placeholder="Masukkan Password (admin123)">
        </div>
        
        <!-- Tombol Masuk Bertema Hijau Pedesaan -->
        <button type="submit" name="login" class="btn btn-green-desa w-100 fw-bold py-2.5 shadow-sm">Masuk Sistem</button>
    </form>
    
    <div class="text-center mt-4">
        <a href="lupa_password.php" class="text-decoration-none small fw-semibold text-success">Lupa Password Akun?</a>
    </div>
</div>

</body>
</html>
