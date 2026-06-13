<?php
$host = "acela.proxy.rlwy.net";
$port = 16565;
$db   = "railway";
$user = "root";
$pass = "RrljnhIsxffyrMGlrbYXyzqYhNbWuowW";

$pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass);
$stmt = $pdo->query("SHOW COLUMNS FROM attendances");
while ($row = $stmt->fetch()) {
    echo $row["Field"] . "\n";
}
?>
