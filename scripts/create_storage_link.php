<?php
// scripts/create_storage_link.php
echo "Attempting to create storage link...\n";

$publicDir = __DIR__ . '/../public';
$linkName = 'storage';
$targetDir = '..\storage';
$targetDirUnix = '../storage';

if (!chdir($publicDir)) {
  echo "ERROR: Failed to change directory to 'public'.\n";
  exit(1);
}

$currentDir = getcwd();
echo "Current directory: $currentDir\n";

if (is_link($linkName) || file_exists($linkName)) {
  echo "SUCCESS: The [public/storage] link already exists.\n";
  exit(0);
}

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
  $command = 'mklink /D "' . $linkName . '" "' . $targetDir . '"';
} else {
  $command = 'ln -s "' . $targetDirUnix . '" "' . $linkName . '"';
}

echo "Running command:\n> $command\n\n";
passthru($command, $return_var);

if ($return_var === 0) {
  echo "\nSUCCESS: Symbolic link created.\n";
} else {
  echo "\nFAILED: Could not create symbolic link.\n";
  if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    echo "==============================================================\n";
    echo "  Windows requires this command to be run as Administrator.\n";
    echo "  Please close this terminal and open a new one \n";
    echo "  (CMD, PowerShell, or Git Bash) using 'Run as administrator'.\n";
    echo "==============================================================\n";
  }
}

exit($return_var);
