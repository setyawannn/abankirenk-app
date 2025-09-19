<?php

/**
 * Menampilkan file view beserta datanya.
 *
 * @param string $viewName Nama file view (tanpa .php).
 * @param array $data Data yang akan diekstrak menjadi variabel.
 */
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

    $viewPath = __DIR__ . '/../app/views/' . str_replace('.', '/', $viewName) . '.php';
    $cachedPath = __DIR__ . '/../storage/views/';
    $cachedFile = $cachedPath . md5($viewName) . '.php';

    if (!is_dir($cachedPath)) {
        mkdir($cachedPath, 0755, true);
    }

    if (config('app.env') === 'local' || !file_exists($cachedFile) || filemtime($viewPath) > filemtime($cachedFile)) {
        $content = file_get_contents($viewPath);

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
