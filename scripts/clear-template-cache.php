<?php

echo "Clearing template cache...\n";

$cacheDir = __DIR__ . '/../storage/templates/';

if (!is_dir($cacheDir)) {
  echo "Cache directory not found. Nothing to clear.\n";
  exit(0);
}

$files = glob($cacheDir . '*');

$fileCount = 0;
foreach ($files as $file) {
  if (is_file($file) && basename($file) !== '.gitkeep') {
    if (unlink($file)) {
      $fileCount++;
    } else {
      echo "ERROR: Could not delete file: " . basename($file) . "\n";
    }
  }
}

echo "Template cache cleared successfully! Total {$fileCount} files deleted.\n";
