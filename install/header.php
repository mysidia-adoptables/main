<?php
declare(strict_types=1);

if (file_exists(__DIR__ . '/../config.php')) {
    require_once __DIR__ . '/../config.php';
}

if (defined('MYS_INSTALLED')) {
    http_response_code(404);
    die();
}
