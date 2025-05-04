<?php
// config/config.php
session_start();

// Получаем настройки из окружения (env_file в docker-compose)
$host = getenv('DB_HOST') ?: 'localhost';
$db   = getenv('DB_NAME') ?: 'tickets';
$user = getenv('DB_USER') ?: 'app';
$pass = getenv('DB_PASS') ?: '';

// Подключаемся к БД
try {
    $db = new PDO(
        "mysql:host={$host};dbname={$db};charset=utf8",
        $user,
        $pass,
        [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ]
    );
} catch (PDOException $e) {
    // На проде не выводите ошибку — логируйте в файл
    die("Database connection failed: " . $e->getMessage());
}
