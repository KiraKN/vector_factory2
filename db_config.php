<?php
$host = 'localhost:8889'; // Порт MySQL в MAMP
$dbname = 'vector_factory_db';
$username = 'root'; // По умолчанию в MAMP
$password = 'root'; // По умолчанию в MAMP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}
?>