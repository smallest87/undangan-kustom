<?php
// config.php

/**
 * Fungsi untuk membaca file .env secara manual dan mengurai nilainya.
 * @param string $path Jalur ke direktori tempat file .env berada.
 * @return array Array asosiatif dari variabel lingkungan.
 */
function loadEnv($path) {
    $envFilePath = rtrim($path, '/') . '/.env';

    if (!file_exists($envFilePath)) {
        throw new Exception("File .env tidak ditemukan di: " . $envFilePath);
    }

    $lines = file($envFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $env = [];

    foreach ($lines as $line) {
        // Abaikan baris komentar
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Cari tanda '='
        $parts = explode('=', $line, 2);

        if (count($parts) === 2) {
            $key = trim($parts[0]);
            $value = trim($parts[1]);

            // Hapus tanda kutip jika ada
            if (preg_match('/^"(.*)"$/', $value, $matches)) {
                $value = $matches[1];
            } elseif (preg_match("/^'(.*)'$/", $value, $matches)) {
                $value = $matches[1];
            }

            $env[$key] = $value;
        }
    }
    return $env;
}

try {
    // Sesuaikan jalur ke direktori tempat file .env Anda berada
    // Misalnya, jika config.php berada di root proyek dan .env juga, gunakan __DIR__ . '/..'
    // Atau jika .env berada di direktori yang sama dengan config.php, gunakan __DIR__
    $env = loadEnv(__DIR__);

    // Konfigurasi Database
    define('DB_HOST', $env['DB_HOST'] ?? 'localhost'); // Memberikan nilai default jika tidak ada di .env
    define('DB_USER', $env['DB_USER'] ?? 'root');
    define('DB_PASS', $env['DB_PASS'] ?? '');
    define('DB_NAME', $env['DB_NAME'] ?? 'nama_database_anda');

} catch (Exception $e) {
    // Tangani kesalahan jika file .env tidak ditemukan atau ada masalah
    die("Error loading environment variables: " . $e->getMessage());
}


// Tambahan: Error Reporting (aktifkan saat pengembangan, matikan di produksi)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>