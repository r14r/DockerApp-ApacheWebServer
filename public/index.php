<?php
$host = getenv('MYSQL_HOST') ?: 'db';
$user = getenv('MYSQL_USER') ?: 'appuser';
$pass = getenv('MYSQL_PASSWORD') ?: 'apppass';
$db   = getenv('MYSQL_DATABASE') ?: 'appdb';

echo "<h1>Strato Docker Mirror</h1>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    $ver = $pdo->query("SELECT VERSION()")->fetchColumn();
    echo "<p>✅ MySQL connected — version $ver</p>";
} catch (Exception $e) {
    echo "<p>❌ MySQL connection failed: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
