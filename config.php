<?php
session_start();

// Hata raporlama (geliştirme modunda)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// AJAX isteklerinde hata gösterimi
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($isAjax) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// Veritabanı bağlantısı
$host = 'localhost';
$dbname = 'admin_crt';
$username = 'admin_crt';
$password = 'CRT.115511';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}

// Otomatik site URL'si
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'); // scriptin bulunduğu klasör

define('SITE_URL', $protocol . $host . $scriptDir);

// Upload klasörü
define('UPLOAD_PATH', __DIR__ . '/uploads/');


// Oturum kontrolü
function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: admin_login.php');
        exit;
    }
}

// Güvenlik fonksiyonları
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// JSON yanıtı
function jsonResponse($success, $message = '', $data = []) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}
?>