<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
date_default_timezone_set('Asia/Jakarta');
error_reporting(E_ALL);
session_start();

$config = [
    'APP_URL' => getenv('APP_URL') ?: 'http://localhost/Fajru/cikadongdong-river-tubing',
    'DATABASE' => [
        'HOST' => getenv('DB_HOST') ?: 'localhost',
        'PORT' => getenv('DB_PORT') ?: '3306',
        'DB_NAME' => getenv('DB_DATABASE') ?: 'db_fajru_cikadongdong',
        'USERNAME' => getenv('DB_USERNAME') ?: 'root',
        'PASSWORD' => getenv('DB_PASSWORD') ?: ''
    ]
];

// init database connection..
$db = mysqli_connect($config['DATABASE']['HOST'], $config['DATABASE']['USERNAME'], $config['DATABASE']['PASSWORD'], $config['DATABASE']['DB_NAME'], $config['DATABASE']['PORT']);

require 'helper.php';
