<?php
define('IN_APP', true);
require __DIR__ . '/config.php';

echo "Spojeno na bazu OK!<br>";

$stmt = $pdo->query("SHOW TABLES");
$tables = $stmt->fetchAll();

echo "<pre>";
print_r($tables);
echo "</pre>";
