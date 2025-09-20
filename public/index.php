<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

require_once __DIR__ . '/../core/functions.php';
require_once __DIR__ . '/../core/router.php';
require_once __DIR__ . '/../core/exceptions.php';

set_error_handler('custom_error_handler');
set_exception_handler('custom_exception_handler');

require_once __DIR__ . '/../routes/web.php';
dispatch();
