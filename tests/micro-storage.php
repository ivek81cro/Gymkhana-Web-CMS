<?php
// Exercise storage in an isolated directory, without touching live results.
$dir = sys_get_temp_dir() . '/mgc-storage-' . bin2hex(random_bytes(8));
if (!mkdir($dir)) throw new RuntimeException('Cannot create test directory');
copy(__DIR__ . '/../micro/lib.php', $dir . '/lib.php');
file_put_contents($dir . '/config.php', '<?php define("DATA_DIR", __DIR__); define("DATA_FILE", __DIR__ . "/data.json");');
require $dir . '/lib.php';
try {
    $rows = [['id' => 1, 'ime' => 'Željko']];
    if (!write_data($rows) || read_data() !== $rows) throw new RuntimeException('Round trip failed');
    file_put_contents(DATA_FILE, '{invalid');
    try {
        read_data();
        throw new RuntimeException('Invalid JSON was accepted');
    } catch (RuntimeException $e) {
        if ($e->getMessage() === 'Invalid JSON was accepted') throw $e;
    }
    unlink(DATA_FILE);
    try {
        read_data();
        throw new RuntimeException('Missing data was accepted');
    } catch (RuntimeException $e) {
        if ($e->getMessage() === 'Missing data was accepted') throw $e;
    }
    echo "Storage checks passed\n";
} finally {
    foreach (glob($dir . '/*') as $file) unlink($file);
    rmdir($dir);
}
