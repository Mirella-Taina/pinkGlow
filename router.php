<?php
// Roteador para o servidor embutido do PHP (APENAS desenvolvimento).
//
// Permite testar o subcaminho sem nginx. Use:
//   Windows:  $env:APP_BASE='/pinkglow/public'; php -S localhost:8000 -t public router.php
//   Linux:    APP_BASE=/pinkglow/public php -S localhost:8000 -t public router.php
//
// Sem APP_BASE, funciona como servidor normal em http://localhost:8000/

$base = rtrim((string) (getenv('APP_BASE') ?: ''), '/');
$uri  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

// Se APP_BASE não foi informado, detecta o prefixo até /public a partir da URL
// (ex.: /pinkglow/public/assets/x.css -> base /pinkglow/public).
if ($base === '') {
    $pos = strpos($uri, '/public/');
    if ($pos !== false) {
        $base = rtrim(substr($uri, 0, $pos + 7), '/');
    }
}

// Informa a base para a aplicação (config/app.php lê APP_BASE)
if ($base !== '') {
    putenv('APP_BASE=' . $base);
}

// Remove o prefixo da aplicação (ex.: /pinkglow/public)
if ($base !== '' && str_starts_with($uri, $base)) {
    $uri = substr($uri, strlen($base));
}
if ($uri === '') {
    $uri = '/';
}

$publicDir = realpath(__DIR__ . '/public');
$target    = realpath($publicDir . $uri);

// 1) Arquivo existente dentro de public/
if ($target && is_file($target) && str_starts_with($target, $publicDir)) {
    $ext = strtolower(pathinfo($target, PATHINFO_EXTENSION));

    if ($ext === 'php') {
        require $target;
        return true;
    }

    $types = [
        'css'   => 'text/css; charset=utf-8',
        'js'    => 'application/javascript; charset=utf-8',
        'svg'   => 'image/svg+xml',
        'png'   => 'image/png',
        'jpg'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'gif'   => 'image/gif',
        'webp'  => 'image/webp',
        'ico'   => 'image/x-icon',
        'woff'  => 'font/woff',
        'woff2' => 'font/woff2',
    ];
    header('Content-Type: ' . ($types[$ext] ?? 'application/octet-stream'));
    readfile($target);
    return true;
}

// 2) Diretório → index.php (ex.: /pinkglow/public/ ou /pinkglow/public/carrinho/)
$index = realpath($publicDir . rtrim($uri, '/') . '/index.php');
if ($index && is_file($index) && str_starts_with($index, $publicDir)) {
    require $index;
    return true;
}

// 3) Fallback: front controller
require $publicDir . '/index.php';
return true;
