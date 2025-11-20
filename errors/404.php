<?php
/**
 * 404 Error Page - Page Not Found
 * 
 * Custom error page for missing pages/resources
 * Displayed when showErrorPage(404, ...) is called
 */

define('IN_APP', true);
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Stranica nije pronađena | Moto Gymkhana Croatia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        .error-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 60px 40px;
            max-width: 600px;
            text-align: center;
        }
        .error-icon {
            font-size: 120px;
            color: #667eea;
            margin-bottom: 20px;
        }
        .error-code {
            font-size: 80px;
            font-weight: bold;
            color: #667eea;
            margin: 0;
            line-height: 1;
        }
        .error-title {
            font-size: 28px;
            color: #333;
            margin: 20px 0;
            font-weight: 600;
        }
        .error-message {
            font-size: 18px;
            color: #666;
            margin: 20px 0;
            line-height: 1.6;
        }
        .error-actions {
            margin-top: 40px;
        }
        .btn-primary {
            background: #667eea;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background: #764ba2;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-outline-secondary {
            padding: 12px 30px;
            font-size: 16px;
            border-radius: 8px;
            border: 2px solid #999;
            color: #666;
            transition: all 0.3s;
        }
        .btn-outline-secondary:hover {
            background: #999;
            color: white;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <i class="bi bi-signpost-split error-icon"></i>
        <h1 class="error-code">404</h1>
        <h2 class="error-title">Stranica nije pronađena</h2>
        <p class="error-message">
            Žao nam je, ali stranica koju tražite ne postoji ili je premještena.
            <br>Provjerite jeste li ispravno upisali adresu.
        </p>
        <div class="error-actions">
            <a href="/" class="btn btn-primary me-2">
                <i class="bi bi-house-door"></i> Početna stranica
            </a>
            <a href="/novosti.php" class="btn btn-outline-secondary">
                <i class="bi bi-newspaper"></i> Novosti
            </a>
        </div>
    </div>
</body>
</html>
