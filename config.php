<?php
// config.php

// Konfigurasi Database
define('DB_HOST', 'localhost'); // Ganti jika host database Anda berbeda
define('DB_USER', 'root');     // Ganti dengan username database Anda
define('DB_PASS', '');         // Ganti dengan password database Anda
define('DB_NAME', 'nama_database_anda'); // Ganti dengan nama database Anda

// --- PENGATURAN FRAGMEN KUSTOM ---
// Kecepatan Guliran (dalam milidetik): Durasi animasi saat berpindah antar fragmen.
// Nilai lebih kecil = lebih cepat, nilai lebih besar = lebih lambat.
define('SCROLL_SPEED_MS', 800); // Misalnya, 800ms untuk guliran yang mulus

// Batas Minimum Waktu di Tiap Fragmen (dalam milidetik):
// Ini adalah cooldown setelah navigasi selesai sebelum sinyal gulir/swipe baru diterima.
// Juga merupakan durasi flag 'isNavigating' aktif.
// Nilai lebih besar = pengguna harus menunggu lebih lama sebelum bisa menggulir lagi.
define('MIN_TIME_PER_FRAGMENT_MS', 1200); // Misalnya, 1200ms (1.2 detik)

// --- AKHIR PENGATURAN FRAGMEN KUSTOM ---

// Tambahan: Error Reporting (aktifkan saat pengembangan, matikan di produksi)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>