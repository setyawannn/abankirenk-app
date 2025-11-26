<?php

function test_logic($scriptName, $requestUri, $description)
{
    echo "Testing: $description\n";
    echo "SCRIPT_NAME: $scriptName\n";
    echo "REQUEST_URI: $requestUri\n";

    $basePath = dirname($scriptName);

    // LOGIC FROM router.php START
    $basePath = str_replace('\\', '/', $basePath);

    if ($basePath !== '/' && strpos($requestUri, $basePath) === 0) {
        $uri = substr($requestUri, strlen($basePath));
    } else {
        $uri = $requestUri;
    }

    $uri = '/' . trim($uri, '/');
    // LOGIC FROM router.php END

    echo "Result URI: $uri\n";
    echo "--------------------------------------------------\n";
}

// Case 1: Docker (Root) - Nested Path
test_logic('/index.php', '/admin/dashboard', 'Docker Root - Nested Path');

// Case 2: XAMPP (Subdir) - Nested Path
test_logic('/abankirenk-app/index.php', '/abankirenk-app/admin/dashboard', 'XAMPP Subdir - Nested Path');

// Case 3: XAMPP (Subdir) - Root Path
test_logic('/abankirenk-app/index.php', '/abankirenk-app/', 'XAMPP Subdir - Root Path');

// Case 4: Docker (Root) - Root Path
test_logic('/index.php', '/', 'Docker Root - Root Path');

// Case 5: Windows Path Style (Simulation)
test_logic('\\index.php', '/admin/dashboard', 'Windows Root - Nested Path');
