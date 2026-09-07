<?php
header('Content-Type: application/json');

$host = getenv('DB_HOST') ?: 'mysql-service';
$user = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: 'password';
$database = getenv('DB_NAME') ?: 'guestbook';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $user, $password);
    $pdo->query("SELECT 1");
    echo json_encode([
        'status' => 'healthy',
        'database' => 'connected',
        'php_version' => phpversion()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'unhealthy',
        'error' => $e->getMessage()
    ]);
}
?>
