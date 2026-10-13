REATE DATABASE persuratan_desa;
USE persuratan_desa;

-- 1. Tabel Users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'kades', 'warga') NOT NULL
);

-- 2. Tabel Penduduk
CREATE TABLE penduduk (
    nik VARCHAR(16) PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    tempat_lahir VARCHAR(50),
    tgl_lahir DATE,
    jk ENUM('L', 'P'),
    alamat TEXT,
    rt VARCHAR(3),
    rw VARCHAR(3),
    agama VARCHAR(20),
    status_kawin VARCHAR(20)
);

-- 3. Tabel Klasifikasi Surat
CREATE TABLE klasifikasi_surat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_klasifikasi VARCHAR(20) NOT NULL UNIQUE,
    nama_klasifikasi VARCHAR(100) NOT NULL,
    keterangan TEXT
);

-- 4. Tabel Surat Masuk
CREATE TABLE surat_masuk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_surat VARCHAR(50) NOT NULL,
    asal_surat VARCHAR(100) NOT NULL,
    perihal VARCHAR(255) NOT NULL,
    tanggal_surat DATE NOT NULL,
    tanggal_terima DATE NOT NULL,
    file_scan VARCHAR(255)
);

-- 5. Tabel Surat Keluar
CREATE TABLE surat_keluar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_surat VARCHAR(50),
    nik_pemohon VARCHAR(16),
    jenis_surat VARCHAR(100) NOT NULL,
    tanggal_keluar DATE,
    status ENUM('pending', 'disetujui') DEFAULT 'pending',
    id_user INT,
    FOREIGN KEY (nik_pemohon) REFERENCES penduduk(nik) ON UPDATE CASCADE,
    FOREIGN KEY (id_user) REFERENCES users(id)
);