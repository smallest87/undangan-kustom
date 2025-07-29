// script.js

/**
 * Memverifikasi apakah perangkat diakses dari lingkungan mobile
 * berdasarkan lebar viewport.
 * @returns {boolean} True jika lebar viewport <= 768px, false jika tidak.
 */
function isMobileDevice() {
    return window.innerWidth <= 768;
}

/**
 * Mengatur fungsionalitas scroll kustom untuk fragmen layar penuh.
 * Fungsi ini hanya akan dipanggil jika perangkat terdeteksi sebagai mobile.
 */
function setupMobileScroll() {
    const fragments = Array.from(document.querySelectorAll('.fragment'));
    const fragmentIds = fragments.map(frag => frag.id);
    const transparentControl = document.getElementById('transparent-control');

    if (!transparentControl) {
        console.error("ERROR: Elemen #transparent-control tidak ditemukan. Pastikan ada di HTML Anda.");
        return;
    }

    // --- Mengambil nilai konfigurasi dari window.appConfig ---
    // Pastikan window.appConfig ada dan memiliki properti yang diharapkan
    const scrollSpeedMs = window.appConfig?.scrollSpeedMs || 800; // Default jika tidak ada config
    const minTimePerFragmentMs = window.appConfig?.minTimePerFragmentMs || 0; // Default jika tidak ada config
    // --- Akhir pengambilan konfigurasi ---

    let currentFragmentIndex = 0;
    let isNavigating = false; // Flag untuk mengunci perpindahan saat sedang dalam proses

    const MIN_SWIPE_DISTANCE = 10; // Jarak minimal (piksel) gerakan untuk dianggap sebagai swipe valid
    const MIN_DELTA_FOR_SWIPE_DIRECTION = 5; // Toleransi kecil untuk deteksi arah swipe

    /**
     * Memperbarui indeks fragmen aktif berdasarkan hash URL saat ini.
     * Digunakan untuk sinkronisasi saat inisialisasi atau perubahan hash eksternal.
     */
    function updateCurrentFragmentIndexFromHash() {
        const hash = window.location.hash.substring(1);
        const indexFromHash = fragmentIds.indexOf(hash);
        currentFragmentIndex = (indexFromHash !== -1) ? indexFromHash : 0;
    }

    /**
     * Melakukan navigasi ke fragmen tertentu menggunakan perubahan bookmark URL.
     * Mengunci navigasi selama proses untuk mencegah "kebablasan".
     * @param {number} index - Indeks fragmen tujuan.
     */
    function navigateToFragment(index) {
        if (index >= 0 && index < fragments.length && !isNavigating) {
            isNavigating = true;

            // Mengubah hash URL; browser akan otomatis menggulir dengan 'scroll-behavior: smooth' CSS
            window.location.hash = fragmentIds[index];
            currentFragmentIndex = index;

            // Cooldown menggunakan nilai konfigurasi
            setTimeout(() => {
                isNavigating = false;
            }, minTimePerFragmentMs); // Menggunakan nilai konfigurasi
        } else if (!isNavigating) {
            // Jika sudah di batas dan tidak ada perpindahan, buka kunci cepat
            setTimeout(() => {
                isNavigating = false;
            }, 100);
        }
    }

    // --- Event Listeners pada Kontrol Transparan (#transparent-control) ---

    // Event 'wheel' untuk gulir mouse
    transparentControl.addEventListener('wheel', (event) => {
        if (!isNavigating) {
            event.preventDefault();

            if (event.deltaY > 0) { // Gulir ke bawah
                navigateToFragment(currentFragmentIndex + 1);
            } else if (event.deltaY < 0) { // Gulir ke atas
                navigateToFragment(currentFragmentIndex - 1);
            }
        }
    }, { passive: false });

    // Event 'touchstart' dan 'touchend' untuk deteksi swipe
    let touchStartY = 0;
    let touchMoved = false;

    transparentControl.addEventListener('touchstart', (event) => {
        if (event.touches.length === 1) {
            touchStartY = event.touches[0].clientY;
            touchMoved = false;
        }
    });

    transparentControl.addEventListener('touchmove', (event) => {
        if (event.touches.length === 1 && !isNavigating) {
            event.preventDefault();
            const currentTouchY = event.touches[0].clientY;
            if (Math.abs(currentTouchY - touchStartY) > MIN_SWIPE_DISTANCE) {
                touchMoved = true;
            }
        }
    }, { passive: false });

    transparentControl.addEventListener('touchend', (event) => {
        if (!isNavigating && event.changedTouches.length === 1 && touchMoved) {
            const touchEndY = event.changedTouches[0].clientY;
            const deltaY = touchStartY - touchEndY;

            if (deltaY > MIN_DELTA_FOR_SWIPE_DIRECTION) {
                navigateToFragment(currentFragmentIndex + 1);
            } else if (deltaY < -MIN_DELTA_FOR_SWIPE_DIRECTION) {
                navigateToFragment(currentFragmentIndex - 1);
            } else {
                isNavigating = false;
            }
        } else if (!touchMoved) {
            isNavigating = false;
        }
        touchMoved = false;
    });

    // --- Inisialisasi Posisi Saat Memuat Halaman ---
    document.addEventListener('DOMContentLoaded', () => {
        updateCurrentFragmentIndexFromHash();
        setTimeout(() => {
            navigateToFragment(currentFragmentIndex);
            isNavigating = false;
        }, 150);
    });
}

// --- Logika Eksekusi Utama Saat DOM Siap ---
document.addEventListener('DOMContentLoaded', () => {
    // Pastikan 'scroll-behavior: smooth;' diatur di CSS html, body agar transisi hash mulus
    const htmlBodyStyle = document.documentElement.style; // html
    const bodyStyle = document.body.style; // body

    // Perbarui 'scroll-behavior' berdasarkan nilai konfigurasi
    // Kita akan atur ini pada html dan body, karena window.scrollTo() atau hash
    // akan dipengaruhi oleh properti ini.
    htmlBodyStyle.scrollBehavior = `smooth`; // Default smooth
    bodyStyle.scrollBehavior = `smooth`; // Default smooth

    if (isMobileDevice()) {
        setupMobileScroll();

        // Nonaktifkan semua perilaku scroll native browser untuk kontrol penuh
        bodyStyle.scrollSnapType = 'none';
        bodyStyle.overflowY = 'hidden';
        
        // Atur durasi smooth scroll di sini berdasarkan config PHP
        // Catatan: Ini mengatur properti CSS, bukan kecepatan langsung JS
        // scroll-behavior: smooth; adalah fitur CSS3 yang durasinya tidak bisa diatur langsung
        // melalui properti CSS. Kecepatan dikendalikan oleh browser.
        // Konfigurasi SCROLL_SPEED_MS dari PHP akan lebih relevan jika kita menggunakan
        // animasi JS kustom (misal: GSAP) daripada window.scrollTo() / hash.
        // Untuk saat ini, kita biarkan saja 'smooth' tanpa durasi spesifik di CSS.
        // Durasi utama dikontrol oleh NAVIGATION_COOLDOWN_MS di JS.
    } else {
        bodyStyle.scrollSnapType = 'none';
        bodyStyle.overflowY = 'auto'; // Pastikan scrollbar muncul di desktop
    }
});

// --- Tangani Event 'resize' ---
let prevIsMobile = isMobileDevice();
window.addEventListener('resize', () => {
    const currentIsMobile = isMobileDevice();
    if (currentIsMobile !== prevIsMobile) {
        if (currentIsMobile) {
            setupMobileScroll();
            document.body.style.overflowY = 'hidden';
            document.body.style.scrollSnapType = 'none';
        } else {
            document.body.style.scrollSnapType = 'none';
            document.body.style.overflowY = 'auto';
        }
        prevIsMobile = currentIsMobile;
    }
});