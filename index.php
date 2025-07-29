<?php
// index.php (Controller)

// 1. Memuat konfigurasi database dan kelas Model
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/classes/PostModel.php';

// 2. Deteksi apakah perangkat adalah mobile (fungsi yang sama dari sebelumnya)
function isMobile() {
    $mobile_agents = array(
        'midp', '240x320', 'blackberry', 'netfront', 'nokia', 'palm', 'psp', 'phone',
        'windows ce', 'windows phone', 'opera mini', 'operamobi', 'symbian', 'android',
        'ipod', 'iphone', 'ipad', 'iemobile', 'webos', 'fennec', 'series60', 'motorola'
    );
    $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    foreach ($mobile_agents as $agent) {
        if (strpos($user_agent, $agent) !== false) {
            return true;
        }
    }
    if (isset($_SERVER['HTTP_ACCEPT'])) {
        if (strpos($_SERVER['HTTP_ACCEPT'], 'wap') !== false || strpos($_SERVER['HTTP_ACCEPT'], 'wml') !== false) {
            return true;
        }
    }
    return false;
}

$is_mobile_device = isMobile(); // Jalankan deteksi sekali

// 3. Inisialisasi Model dan ambil data
$postModel = new PostModel();
$posts = $postModel->getAllPosts(); // Ambil semua postingan dari database

require_once __DIR__ . '/views/fragments.php';
?>