<?php
// Skripta za automatsko dodavanje gallery_id stupca u articles tablicu
define('IN_APP', true);
require __DIR__ . '/config.php';

echo "<h2>Provjera i ažuriranje baze podataka</h2>";

try {
    // Provjeri postoji li stupac gallery_id
    $stmt = $pdo->query("SHOW COLUMNS FROM articles LIKE 'gallery_id'");
    $columnExists = $stmt->fetch();

    if ($columnExists) {
        echo "<p style='color: green;'>✓ Stupac 'gallery_id' već postoji u tablici 'articles'.</p>";
    } else {
        echo "<p style='color: orange;'>→ Stupac 'gallery_id' ne postoji. Dodajem...</p>";
        
        // Dodaj stupac
        $pdo->exec("
            ALTER TABLE articles 
            ADD COLUMN gallery_id INT(11) NULL DEFAULT NULL AFTER tags
        ");
        
        echo "<p style='color: green;'>✓ Stupac 'gallery_id' je uspješno dodan!</p>";
        
        // Pokušaj dodati foreign key constraint (ako postoji tablica galleries)
        try {
            $pdo->exec("
                ALTER TABLE articles
                ADD CONSTRAINT fk_articles_gallery 
                    FOREIGN KEY (gallery_id) 
                    REFERENCES galleries(id) 
                    ON DELETE SET NULL
            ");
            echo "<p style='color: green;'>✓ Foreign key constraint je uspješno dodan!</p>";
        } catch (PDOException $e) {
            echo "<p style='color: orange;'>⚠ Foreign key constraint nije dodan (možda već postoji ili tablica galleries ne postoji): " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
    
    // Prikaži strukturu tablice articles
    echo "<h3>Struktura tablice 'articles':</h3>";
    $stmt = $pdo->query("DESCRIBE articles");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($col['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($col['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($col['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($col['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($col['Default'] ?? 'NULL') . "</td>";
        echo "<td>" . htmlspecialchars($col['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<p style='color: green; margin-top: 20px;'><strong>✓ Gotovo! Sada možeš povezivati galerije s člancima.</strong></p>";
    echo "<p><a href='admin-novosti.php'>→ Idi na admin panel za članke</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Greška: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
