<?php
session_start();

// Validasi Sesi Login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require_once 'koneksi.php';
$user_id = $_SESSION['user_id'];

$message = '';
$message_type = '';

// --- PROSES UPDATE PROFIL ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profil'])) {
    $nama_lengkap = htmlspecialchars(trim($_POST['nama_lengkap'] ?? ''), ENT_QUOTES, 'UTF-8');
    $instansi     = htmlspecialchars(trim($_POST['instansi'] ?? ''), ENT_QUOTES, 'UTF-8');
    
    // Ambil foto profil saat ini dari DB
    $stmtFoto = $pdo->prepare("SELECT foto_profil FROM users WHERE id = ?");
    $stmtFoto->execute([$user_id]);
    $foto_sekarang = $stmtFoto->fetchColumn();
    
    // Upload foto baru jika ada
    if (!empty($_FILES['foto']['name'])) {
        $file_name = $_FILES['foto']['name'];
        $file_tmp  = $_FILES['foto']['tmp_name'];
        $ext       = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $extensions = ["jpeg", "jpg", "png"];
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $file_tmp);
        finfo_close($finfo);
        $allowed_mimes = ["image/jpeg", "image/jpg", "image/png"];
        
        if (in_array($ext, $extensions) && in_array($mime, $allowed_mimes)) {
            $nama_file_baru = "avatar_" . bin2hex(random_bytes(8)) . "_" . time() . "." . $ext;
            $target_dir     = "img/";
            $target_file    = $target_dir . $nama_file_baru;
            
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }
            
            if (move_uploaded_file($file_tmp, $target_file)) {
                // Hapus foto lama jika ada
                if (!empty($foto_sekarang) && file_exists($foto_sekarang)) {
                    @unlink($foto_sekarang);
                }
                $foto_sekarang = $target_file; 
            }
        } else {
            $message = 'Format file tidak valid! Gunakan format JPG, JPEG, atau PNG.';
            $message_type = 'danger';
        }
    }

    // Simpan ke Database
    if (empty($message)) {
        try {
            $sqlUp = "UPDATE users SET nama_lengkap = ?, instansi = ?, foto_profil = ? WHERE id = ?";
            $stmtUp = $pdo->prepare($sqlUp);
            $stmtUp->execute([$nama_lengkap, $instansi, $foto_sekarang, $user_id]);
            
            $message = 'Profil berhasil diperbarui!';
            $message_type = 'success';
        } catch (PDOException $e) {
            $message = 'Gagal menyimpan data ke database!';
            $message_type = 'danger';
        }
    }
}

// --- AMBIL DATA USER & STATISTIK ---
$stmtUser = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmtUser->execute([$user_id]);
$userData = $stmtUser->fetch(PDO::FETCH_ASSOC);

$totalPenduduk = $pdo->query("SELECT COUNT(*) FROM penduduk")->fetchColumn();
$suratMasuk    = $pdo->query("SELECT COUNT(*) FROM surat_masuk")->fetchColumn();
$suratKeluar   = $pdo->query("SELECT COUNT(*) FROM surat_keluar")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Utama - Sipersa</title>
    
    <!-- Bootstrap CSS -->
    <link href="bootstrap.min.css" rel="stylesheet">
    
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: #f4f6f9; 
            display: flex; 
            min-height: 100vh;
            overflow-x: hidden; 
            margin: 0; 
        }
        .sidebar { 
            width: 260px; 
            background-color: #1e2229; 
            color: #dee2e6; 
            min-height: 100vh; 
            padding: 25px 20px; 
            display: flex; 
            flex-direction: column; 
            justify-content: space-between; 
            transition: margin-left 0.3s ease; 
        }
        .sidebar.sembunyi { 
            margin-left: -260px; 
        }
        .sidebar h4 { 
            text-align: center; 
            margin-bottom: 25px; 
            border-bottom: 1px solid #3a4149; 
            padding-bottom: 15px; 
            color: #fff; 
            font-weight: 600; 
        }
        .sidebar a { 
            display: block; 
            color: #adb5bd; 
            padding: 12px 15px; 
            text-decoration: none; 
            border-radius: 6px; 
            margin-bottom: 8px; 
            font-size: 14px; 
            transition: all 0.2s;
        }
        .sidebar a:hover, .sidebar a.active { 
            background-color: #2c313c; 
            color: #fff; 
            text-decoration: none; 
        }
        .main-content { 
            flex: 1; 
            padding: 30px; 
            min-width: 0; 
        }
        .header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            border-bottom: 1px solid #dee2e6; 
            padding-bottom: 15px; 
            margin-bottom: 30px; 
        }
        .avatar-circle { 
            width: 32px; 
            height: 32px; 
            border-radius: 50%; 
            object-fit: cover; 
        }
        .row-cards { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); 
            gap: 20px; 
            margin-bottom: 30px; 
        }
        .card-kpi { 
            padding: 25px; 
            border-radius: 10px; 
            color: white; 
            border: none; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.05); 
        }
        .logo-box-img { 
            width: 90px; 
            height: 90px; 
            border-radius: 50%; 
            object-fit: cover; 
            border: 2px solid #dee2e6; 
        }
    </style>
</head>
<body>

    <!-- SIDEBAR NAVIGATION -->
    <div class="sidebar" id="mySidebar">
        <div>
            <h4>Sipersa Desa</h4>
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="surat_keluar.php">Surat Keluar</a>
            <a href="surat_masuk.php">Surat Masuk</a>
            <a href="data_penduduk.php">Data Penduduk</a>
        </div>
        
        <!-- MENU KELUAR SIDEBAR -->
        <div class="mt-auto">
            <a href="logout.php" class="bg-danger text-white text-center fw-bold py-2 rounded" onclick="return confirm('Apakah Anda yakin ingin keluar aplikasi?')">🚪 Keluar Aplikasi</a>
            <div class="bg-dark p-2 text-center rounded small text-muted mt-2">Sipersa v2.0</div>
        </div>
    </div>

    <!-- CONTENT WRAPPER -->
    <div class="main-content" id="mainPanel">
        
        <!-- HEADER BAR -->
        <div class="header">
            <div class="d-flex align-items-center">
                <button type="button" class="btn btn-dark me-3" onclick="toggleSidebar()">☰</button>
                <h2 class="h4 m-0 fw-bold">Beranda Utama</h2>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <div class="btn btn-light border d-flex align-items-center gap-2 py-1 px-3 rounded-pill" style="cursor: default;">
                    <?php if (!empty($userData['foto_profil']) && file_exists($userData['foto_profil'])): ?>
                        <img src="<?= htmlspecialchars($userData['foto_profil']); ?>" class="avatar-circle" alt="Avatar">
                    <?php else: ?>
                        <div class="avatar-circle bg-primary text-white d-inline-flex align-items-center justify-content-center fw-bold small">
                            <?= strtoupper(substr($userData['username'] ?? 'A', 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                    <span class="small fw-semibold text-dark"><?= htmlspecialchars($userData['username'] ?? 'Admin'); ?> (Admin)</span>
                </div>
            </div>
        </div>

        <!-- NOTIFIKASI -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $message_type; ?> alert-dismissible fade show" role="alert">
                <?= $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- KPI CARDS STATISTIK -->
        <div class="row-cards">
            <div class="card-kpi bg-primary">
                <h6 class="mb-2">TOTAL PENDUDUK</h6>
                <h2 class="m-0 font-weight-bold"><?= number_format($totalPenduduk); ?></h2>
            </div>
            <div class="card-kpi bg-success">
                <h6 class="mb-2">SURAT MASUK</h6>
                <h2 class="m-0 font-weight-bold"><?= number_format($suratMasuk); ?></h2>
            </div>
            <div class="card-kpi bg-warning text-dark">
                <h6 class="mb-2">SURAT KELUAR</h6>
                <h2 class="m-0 font-weight-bold"><?= number_format($suratKeluar); ?></h2>
            </div>
        </div>

        <!-- RINGKASAN PROFIL USER -->
        <div class="card border-0 shadow-sm p-4 mb-4 d-flex flex-row gap-4 align-items-center bg-white rounded-3">
            <?php if (!empty($userData['foto_profil']) && file_exists($userData['foto_profil'])): ?>
                <img src="<?= htmlspecialchars($userData['foto_profil']); ?>" class="logo-box-img" alt="Foto Profil">
            <?php else: ?>
                <div class="logo-box-img bg-light d-flex align-items-center justify-content-center">
                    <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
            <?php endif; ?>
            <div>
                <h5 class="fw-bold mb-2">Profil Petugas Operasional</h5>
                <p class="m-0 text-muted small"><strong>Nama:</strong> <?= htmlspecialchars($userData['nama_lengkap'] ?? 'Belum Diatur'); ?></p>
                <p class="m-0 text-muted small"><strong>Instansi:</strong> <?= htmlspecialchars($userData['instansi'] ?? 'Kantor Desa Sejahtera'); ?></p>
            </div>
        </div>

        <!-- FORM UPDATE PROFIL -->
        <div class="card border-0 shadow-sm p-4 bg-white rounded-3">
            <h5 class="fw-bold mb-4">Perbarui Profil & Informasi Akun</h5>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="update_profil" value="1">
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($userData['nama_lengkap'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Nama Instansi</label>
                        <input type="text" name="instansi" class="form-control" value="<?= htmlspecialchars($userData['instansi'] ?? ''); ?>" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label small fw-bold">Foto Profil Baru</label>
                    <input type="file" name="foto" class="form-control" accept="image/jpeg, image/jpg, image/png">
                </div>
                
                <button type="submit" class="btn btn-dark fw-bold px-4">Simpan Perubahan</button>
            </form>
        </div>

    </div>

    <!-- JAVASCRIPT -->
    <script>
        function toggleSidebar() {
            document.getElementById("mySidebar").classList.toggle("sembunyi");
        }
    </script>
</body>
</html>