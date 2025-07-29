<?php
// classes/PostModel.php

class PostModel {
    private $conn;

    public function __construct() {
        // Memuat konfigurasi database
        require_once __DIR__ . '/../config.php';

        // Membuat koneksi database menggunakan PDO
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $this->conn = new PDO($dsn, DB_USER, DB_PASS);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Koneksi database gagal: " . $e->getMessage());
        }
    }

    /**
     * Mengambil semua postingan dari database.
     * @return array Array asosiatif dari postingan.
     */
    public function getAllPosts() {
        try {
            $stmt = $this->conn->prepare("SELECT id, title, content FROM posts ORDER BY id ASC");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Gagal mengambil postingan: " . $e->getMessage());
            return []; // Kembalikan array kosong jika terjadi kesalahan
        }
    }

    // Anda bisa menambahkan metode lain di sini, misal:
    // public function getPostById($id) { ... }
    // public function createPost($title, $content) { ... }
}
?>