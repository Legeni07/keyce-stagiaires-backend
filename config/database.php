<?php
// config/database.php

define('DB_HOST',     getenv('DB_HOST')     ?: 'db');
define('DB_PORT',     getenv('DB_PORT')     ?: '3306');
define('DB_NAME',     getenv('DB_NAME')     ?: 'keyce_stagiaires');
define('DB_USER',     getenv('DB_USER')     ?: 'keyce');
define('DB_PASS',     getenv('DB_PASS')     ?: 'keyce2025');
define('JWT_SECRET',  getenv('JWT_SECRET')  ?: 'keyce_secret_jwt_2025_!@#');
define('JWT_EXPIRY',  86400); // 24h en secondes
define('UPLOAD_DIR',  __DIR__ . '/../uploads/');
define('MAX_FILE_MB', 10);

function getPDO(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}
