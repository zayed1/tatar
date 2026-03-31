<?php
class PageCache {
    private static $cacheDir;

    public static function init() {
        self::$cacheDir = APP_PATH . 'cache-f/pages/';
        if (!is_dir(self::$cacheDir)) mkdir(self::$cacheDir, 0777, true);
    }

    public static function get($key, $ttl = 300) {
        self::init();
        $file = self::$cacheDir . md5($key) . '.cache';
        if (file_exists($file) && (time() - filemtime($file)) < $ttl) {
            return file_get_contents($file);
        }
        return false;
    }

    public static function set($key, $content) {
        self::init();
        $file = self::$cacheDir . md5($key) . '.cache';
        file_put_contents($file, $content);
    }

    public static function clear() {
        self::init();
        array_map('unlink', glob(self::$cacheDir . '*.cache') ?: []);
    }
}

// CSRF Token functions
function csrf_token() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function csrf_verify() {
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) return false;
    return hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}
?>
