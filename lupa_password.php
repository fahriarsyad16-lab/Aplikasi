<?php
require 'koneksi.php';

if (isset($_POST['cek_username'])) {
    $username = trim($_POST['username']);
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && !empty($user['pertanyaan_keamanan'])) {
        $step = 2;
        $user_id = $user['id'];
        $pertanyaan = $user['pertanyaan_keamanan'];
    } else {
        $error = "Username tidak ditemukan atau belum mengatur pertanyaan keamanan!";
    }
}

if (isset($_POST['reset_password'])) {
    $user_id = $_POST['user_id'];
    $jawaban = trim($_POST['jawaban']);
    $password_baru = password_hash(trim($_POST['password_baru']), PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("SELECT jawaban_keamanan FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $real_jawaban = $stmt->fetchColumn();

    if ($real_jawaban && strcasecmp($jawaban, $real_jawaban) == 0) {
        $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update->execute([$password_baru, $user_id]);
        echo "<script>alert('Password berhasil diperbarui! Silakan login kembali.'); window.location='index.php';</script>";
        exit;
    } else {
        $error = "Jawaban keamanan salah! Gagal merestorasi akun.";
        $step = 1;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pulihkan Password</title>
    <link href="https://jsdelivr.net" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; min-height: 100vh; font-family: 'Segoe UI', sans-serif; }
        .reset-card { background: white; padding: 35px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); width: 100%; max-width: 400px; }
    </style>
</head>
<body>
<div class="reset-card">
    <h4 class="text-center fw-bold mb-1">Pemulihan Password</h4>
    <p class="text-center text-muted small mb-4">Gunakan pertanyaan keamanan untuk mereset sandi</p>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger p-2 small text-center"><?= $error; ?></div>
    <?php endif; ?>

    <?php if (!isset($step) || $step == 1): ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label small fw-bold">Masukkan Username Anda</label>
                <input type="text" name="username" class="form-control" required placeholder="admin">
            </div>
            <button type="submit" name="cek_username" class="btn btn-dark w-100 fw-bold">Periksa Akun</button>
        </form>
        <div class="text-center mt-3"><a href="index.php" class="small text-decoration-none">Batal & Kembali</a></div>
    <?php else: ?>
        <form method="POST" action="">
            <input type="hidden" name="user_id" value="<?= $user_id; ?>">
            <div class="alert alert-secondary small mb-3"><strong>Pertanyaan:</strong><br><?= htmlspecialchars($pertanyaan); ?></div>
            <div class="mb-3">
                <label class="form-label small fw-bold">Jawaban Anda</label>
                <input type="text" name="jawaban" class="form-control" required>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-bold">Kata Sandi Baru</label>
                <input type="password" name="password_baru" class="form-control" required placeholder="Masukkan password baru">
            </div>
            <button type="submit" name="reset_password" class="btn btn-danger w-100 fw-bold">Reset & Perbarui Password</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>