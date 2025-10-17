<?php

/**
 * Menampilkan file view beserta datanya.
 *
 * @param string $viewName Nama file view (tanpa .php).
 * @param array $data Data yang akan diekstrak menjadi variabel.
 */

/**
 * Membuat URL lengkap berdasarkan BASE_URL yang ada di config.
 *
 * @param string $path Path yang dituju (misal: '/login' atau 'products/1').
 * @return string
 */
function url(string $path = ''): string
{
    $baseUrl = config('app.base_url');
    // Menghapus slash di akhir base_url dan di awal path, lalu menggabungkannya
    return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
}

/**
 * Melakukan redirect ke URL internal dengan aman.
 *
 * @param string $to Path yang dituju.
 */
function redirect(string $to)
{
    header('Location: ' . url($to));
    exit();
}


function flash_message(string $key, ?string $title = null, ?string $message = null)
{
    $_SESSION['flash_message'] = [
        'key' => $key,
        'title' => $title,
        'message' => $message
    ];
}

/**
 * Mengambil data pengguna yang sedang login dari session.
 *
 * @return array|null
 */
function auth()
{
    return $_SESSION['user'] ?? null;
}

function env_get(string $key, $default = null)
{
    return $_ENV[$key] ?? $default;
}

function config(string $key)
{
    static $config = null;

    if ($config === null) {
        $configPath = __DIR__ . '/../config/';
        $configFiles = glob($configPath . '*.php');
        $config = [];

        foreach ($configFiles as $file) {
            $fileName = pathinfo($file, PATHINFO_FILENAME);
            $config[$fileName] = require $file;
        }
    }

    $keys = explode('.', $key);
    $value = $config;

    foreach ($keys as $k) {
        if (isset($value[$k])) {
            $value = $value[$k];
        } else {
            return null;
        }
    }

    return $value;
}

function dd()
{
    $args = func_get_args();

    echo '<pre style="background-color: #1a1a1a; color: #f8f8f2; padding: 15px; border-radius: 5px; margin: 10px; font-family: Consolas, monospace; font-size: 14px; line-height: 1.6; overflow-x: auto;">';

    foreach ($args as $arg) {
        var_dump($arg);
        echo "\n";
    }

    echo '</pre>';
    die();
}

function log_message(string $level, string $message)
{
    $logDir = __DIR__ . '/../storage/logs/';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }

    $logFile = $logDir . 'app.log';
    $level = strtoupper($level);
    $timestamp = date('Y-m-d H:i:s');

    $logEntry = "[{$timestamp}] {$level}: {$message}" . PHP_EOL;

    @file_put_contents($logFile, $logEntry, FILE_APPEND);
}

function abort_403()
{
    http_response_code(403);
    require_once __DIR__ . '/../app/templates/errors/403.php';
    exit();
}

function view(string $viewName, array $data = [])
{
    // Ekstrak data agar bisa diakses di semua view yang di-include/extend
    extract($data);

    // Jalankan file yang sudah di-compile
    require compile_view($viewName);
}

/**
 * Meng-compile file view jika diperlukan dan mengembalikan path ke file cache.
 *
 * @param string $viewName
 * @return string Path ke file cache.
 */
function compile_view(string $viewName): string
{
    static $sections = [];
    static $stacks = [];

    $viewPath = __DIR__ . '/../app/templates/' . str_replace('.', '/', $viewName) . '.php';
    $cachedPath = __DIR__ . '/../storage/templates/';
    $cachedFile = $cachedPath . md5($viewName) . '.php';

    if (!is_dir($cachedPath)) {
        mkdir($cachedPath, 0755, true);
    }

    if (config('app.env') === 'local' || !file_exists($cachedFile) || filemtime($viewPath) > filemtime($cachedFile)) {
        $content = file_get_contents($viewPath);

        preg_match_all('/@push\s*\(\s*\'(.*?)\'\s*\)(.*?)@endpush/s', $content, $pushMatches, PREG_SET_ORDER);
        foreach ($pushMatches as $match) {
            $stackName = $match[1];
            $stackContent = $match[2];
            if (!isset($stacks[$stackName])) $stacks[$stackName] = [];
            $stacks[$stackName][] = $stackContent;
        }
        $content = preg_replace('/@push\s*\(\s*\'(.*?)\'\s*\)(.*?)@endpush/s', '', $content);

        preg_match('/@extends\s*\(\s*\'(.*)\'\s*\)/', $content, $matches);
        $layoutName = $matches[1] ?? null;

        preg_match_all('/@section\s*\(\s*\'(.*?)\'\s*\)(.*?)@endsection/s', $content, $sectionMatches, PREG_SET_ORDER);
        foreach ($sectionMatches as $match) {
            $sections[$match[1]] = $match[2];
        }

        if ($layoutName) {
            $layoutContentPath = compile_view($layoutName);
            $content = file_get_contents($layoutContentPath);
        }

        $content = preg_replace_callback('/@yield\s*\(\s*\'(.*)\'\s*\)/', function ($matches) use ($sections) {
            return $sections[$matches[1]] ?? '';
        }, $content);

        $content = preg_replace_callback('/@include\s*\(\s*\'(.*)\'\s*\)/', function ($matches) {
            return '<?php require compile_view(\'' . $matches[1] . '\'); ?>';
        }, $content);

        $content = preg_replace_callback('/@stack\s*\(\s*\'(.*?)\'\s*\)/', function ($matches) use ($stacks) {
            $stackName = $matches[1];
            if (!isset($stacks[$stackName])) return '';

            return implode("\n", $stacks[$stackName]);
        }, $content);

        $content = preg_replace('/\{\{--\s*(.+?)\s*--\}\}/s', '', $content);
        $content = preg_replace('/\{!!\s*(.+?)\s*!!\}/', '<?php echo $1; ?>', $content);
        $content = preg_replace('/\{\{\s*(.+?)\s*\}\}/', '<?php echo htmlspecialchars($1); ?>', $content);

        $content = preg_replace('/@if\s*\((.*)\)/', '<?php if($1): ?>', $content);
        $content = preg_replace('/@elseif\s*\((.*)\)/', '<?php elseif($1): ?>', $content);
        $content = preg_replace('/@else/', '<?php else: ?>', $content);
        $content = preg_replace('/@endif/', '<?php endif; ?>', $content);

        $content = preg_replace('/@foreach\s*\((.*)\)/', '<?php foreach($1): ?>', $content);
        $content = preg_replace('/@endforeach/', '<?php endforeach; ?>', $content);

        $content = preg_replace('/@auth/', '<?php if(isset($_SESSION[\'user\'])): ?>', $content);
        $content = preg_replace('/@endauth/', '<?php endif; ?>', $content);
        $content = preg_replace('/@guest/', '<?php if(!isset($_SESSION[\'user\'])): ?>', $content);
        $content = preg_replace('/@endguest/', '<?php endif; ?>', $content);

        $content = preg_replace('/@php/', '<?php', $content);
        $content = preg_replace('/@endphp/', '?>', $content);

        file_put_contents($cachedFile, $content);
    }

    return $cachedFile;
}
