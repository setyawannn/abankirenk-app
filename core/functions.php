<?php


use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;

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

function format_role_name(string $role_string): string
{
    $role_string = str_replace('manajer', 'Manager', $role_string);
    $with_spaces = str_replace('_', ' ', $role_string);

    $formatted_name = ucwords($with_spaces);
    return $formatted_name;
}

function handle_file_upload(
    array $fileData,
    string $grouping,
    string $storageType = 'images',
    ?string $old_file_url = null
): array {
    $manager = new ImageManager(Driver::class);

    try {

        if (!in_array($storageType, ['images', 'documents'])) {
            throw new Exception("Tipe storage tidak valid. Hanya 'images' atau 'documents'.");
        }

        if ($fileData['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Gagal meng-upload file. Error code: ' . $fileData['error']);
        }

        $maxSize = 10 * 1024 * 1024; // 10 MB
        if ($fileData['size'] > $maxSize) {
            throw new Exception('Ukuran file tidak boleh lebih dari 10 MB.');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $fileData['tmp_name']);
        finfo_close($finfo);

        $imageMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $documentMimes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];
        $allowedMimeTypes = array_merge($imageMimes, $documentMimes);

        if (!in_array($mimeType, $allowedMimeTypes)) {
            throw new Exception('Format file tidak diizinkan. Hanya (JPG, PNG, PDF, DOCX, XLSX).');
        }

        $baseDir = __DIR__ . "/../storage/{$storageType}";
        $year = date('y');
        $month = date('m');
        $day = date('d');
        $subDir = "{$year}/{$month}/{$day}/{$grouping}";
        $uploadDir = "{$baseDir}/{$subDir}";

        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                throw new Exception('Gagal membuat direktori upload.');
            }
        }

        $originalName = basename($fileData['name']);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $filenameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
        $safeFilename = preg_replace('/[^a-zA-Z0-9-_\.]/', '', str_replace(' ', '-', $filenameWithoutExt));
        $shortRand = substr(md5(uniqid()), 0, 8);

        $originalNewFilename = "{$shortRand}-{$safeFilename}.{$extension}";
        $originalFilePath = "{$uploadDir}/{$originalNewFilename}";

        if (!move_uploaded_file($fileData['tmp_name'], $originalFilePath)) {
            throw new Exception('Gagal memindahkan file yang di-upload.');
        }

        $finalFilename = $originalNewFilename;

        if (in_array($mimeType, $imageMimes)) {
            try {
                $webpFilename = "{$shortRand}-{$safeFilename}.webp";
                $webpFilePath = "{$uploadDir}/{$webpFilename}";
                $image = $manager->read($originalFilePath);

                $image->encode(new WebpEncoder(80))->save($webpFilePath);

                unlink($originalFilePath);

                $finalFilename = $webpFilename;
            } catch (Exception $e) {
                error_log("Konversi ke WebP gagal: " . $e->getMessage());
            }
        }

        $publicUrl = "/storage/{$storageType}/{$subDir}/{$finalFilename}";

        if (!empty($old_file_url)) {
            delete_storage_file($old_file_url);
        }

        return [
            'success' => true,
            'url' => $publicUrl,
            'fileName' => $finalFilename
        ];
    } catch (Exception $e) {
        error_log("Upload Error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}


function delete_storage_file(string $publicUrl): bool
{
    try {
        $projectRoot = __DIR__ . '/../';

        $relativePath = ltrim($publicUrl, '/');

        $serverPath = $projectRoot . $relativePath;
        $serverPath = str_replace('/', DIRECTORY_SEPARATOR, $serverPath);

        if (file_exists($serverPath)) {
            if (unlink($serverPath)) {
                return true;
            } else {
                error_log("Gagal menghapus file (permission issue?): " . $serverPath);
                return false;
            }
        } else {
            return true;
        }
    } catch (Exception $e) {
        error_log("Error saat delete_storage_file: " . $e->getMessage());
        return false;
    }
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
            if (!isset($stacks[$stackName]))
                $stacks[$stackName] = [];
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
            $layoutPath = __DIR__ . '/../app/templates/' . str_replace('.', '/', $layoutName) . '.php';
            if (file_exists($layoutPath)) {
                $content = file_get_contents($layoutPath);
            }
        }

        $content = preg_replace_callback('/@yield\s*\(\s*\'(.*)\'\s*\)/', function ($matches) use ($sections) {
            return $sections[$matches[1]] ?? '';
        }, $content);

        $content = preg_replace_callback(
            '/@include\s*\(\s*\'(.*?)\'\s*(?:,\s*(.*?))?\s*\)/s',
            function ($matches) {
                $viewName = $matches[1];
                $dataString = $matches[2] ?? null;

                if ($dataString) {
                    return '<?php 
                        extract(' . $dataString . ');
                        require compile_view(\'' . $viewName . '\');
                    ?>';
                } else {
                    return '<?php require compile_view(\'' . $viewName . '\'); ?>';
                }
            },
            $content
        );

        $content = preg_replace_callback('/@stack\s*\(\s*\'(.*?)\'\s*\)/', function ($matches) use ($stacks) {
            $stackName = $matches[1];
            if (!isset($stacks[$stackName]))
                return '';

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

        $content = preg_replace('/@for\s*\((.*)\)/', '<?php for($1): ?>', $content);
        $content = preg_replace('/@endfor/', '<?php endfor; ?>', $content);

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
