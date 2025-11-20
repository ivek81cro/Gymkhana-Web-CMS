<?php
/**
 * 500 Error Page - Internal Server Error
 * 
 * Custom error page for server errors
 * Displayed when showErrorPage(500, ...) is called
 */

define('IN_APP', true);
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Greška na serveru | Moto Gymkhana Croatia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
            color: #f5576c;
            margin-bottom: 20px;
            animation: shake 0.5s ease-in-out;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
        .error-code {
            font-size: 80px;
            font-weight: bold;
            color: #f5576c;
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
            background: #f5576c;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background: #f093fb;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(245, 87, 108, 0.4);
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
        <i class="bi bi-exclamation-triangle error-icon"></i>
        <h1 class="error-code">500</h1>
        <h2 class="error-title">Greška na serveru</h2>
        <p class="error-message">
            Nažalost, dogodila se greška prilikom obrade vašeg zahtjeva.
            <br>Naš tim je obaviješten i radi na rješavanju problema.
            <br><br>
            Molimo pokušajte ponovno za nekoliko trenutaka.
        </p>
        <div class="error-actions">
            <a href="/" class="btn btn-primary me-2">
                <i class="bi bi-house-door"></i> Početna stranica
            </a>
            <button onclick="location.reload()" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-clockwise"></i> Pokušaj ponovno
            </button>
        </div>
    </div>
</body>
</html>
