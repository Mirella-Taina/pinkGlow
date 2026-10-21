<?php
// Redireciona a raiz do projeto para a pasta public/.
// Usado apenas quando o servidor expõe a pasta do projeto diretamente
// (ex.: http://servidor/pinkglow/ -> http://servidor/pinkglow/public/).
// Em produção com nginx, esse redirecionamento já é feito em deploy/nginx/pinkglow.conf.
$uri  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$uri  = rtrim($uri, '/');

// Evita loop: só redireciona se ainda não estiver em /public
if (substr($uri, -7) !== '/public') {
    header('Location: ' . $uri . '/public/', true, 302);
    exit;
}
