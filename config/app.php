<?php
// Base da aplicação — prefixo de URL onde a pasta public/ está publicada.
// Em produção com subcaminho: export APP_BASE=/pinkglow/public
// Em raiz: deixe APP_BASE vazio ou não defina — a base é detectada automaticamente.
$envBase = trim((string)(getenv('APP_BASE') ?: ''));

if ($envBase !== '') {
    $base = $envBase;
} else {
    // Auto-detecção: extrai o prefixo até a pasta /public a partir do
    // caminho do script atual. Funciona na raiz, em subpasta ou subcaminho.
    $script = (string)($_SERVER['SCRIPT_NAME'] ?? '');
    $base   = '';
    $pos    = strpos($script, '/public/');
    if ($pos !== false) {
        $base = substr($script, 0, $pos + 7);          // inclui "/public"
    } elseif (substr($script, -7) === '/public') {
        $base = $script;
    }
}

$base = '/' . trim($base, '/');
if ($base === '/') { $base = ''; }
if (!defined('BASE')) { define('BASE', $base); }

// Prefixa caminhos absolutos locais ('/assets/...', '/carrinho/') com BASE.
// URLs externas (http/https) e caminhos relativos são devolvidos como estão.
function url_base(string $caminho): string {
    if ($caminho !== '' && $caminho[0] === '/' && !str_starts_with($caminho, '//')) {
        return BASE . $caminho;
    }
    return $caminho;
}
