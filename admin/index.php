<?php
// Admin index - redirect na novosti ili login
define('IN_APP', true);
require __DIR__ . '/../includes/config.php';

// Preusmjeri na novosti ako je prijavljen, inače na login
if (is_admin()) {
    header('Location: novosti.php');
} else {
    header('Location: login.php');
}
exit;
