<?php
define('IN_APP', true);
require __DIR__ . '/../includes/config.php';

$_SESSION = [];
session_destroy();

// Preusmjeri na početnu stranicu
header('Location: index.php'); // ili 'Location: /' ako ti je site u rootu domene
exit;