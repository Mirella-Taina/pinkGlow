<?php
// Layout comum do painel administrativo — protege por sessão e padroniza visual (DESIGN.md).
require_once __DIR__ . '/../../config/app.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (($_SESSION['usuario_tipo'] ?? null) !== 'admin') {
    header('Location: ' . BASE . '/login.php?erro=acesso');
    exit;
}

function admin_ini(string $titulo): void {
    header('Content-Type: text/html; charset=utf-8');
    $links = [
        '/admin/'           => 'Dashboard',
        '/admin/produtos.php' => 'Produtos',
        '/admin/produtos/novo.php' => 'Novo Produto',
        '/admin/categorias.php' => 'Categorias',
        '/admin/cupons.php' => 'Cupons',
        '/admin/estoque.php' => 'Estoque',
        '/admin/pedidos.php' => 'Pedidos',
    ];
    echo '<!DOCTYPE html><html lang="pt-BR"><head><meta charset="utf-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<title>' . htmlspecialchars($titulo) . ' — Pink Glow Admin</title>';
    echo '<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>';
    echo '<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">';
    echo '<script>tailwind.config = { theme: { extend: { colors: { primary: "#b7005e", "primary-container": "#db2777", gold: "#D4AF37", brand: { 900: "#58102B", 700: "#BE185D", 600: "#DB2777" } }, fontFamily: { serif: ["Playfair Display", "serif"], sans: ["Plus Jakarta Sans", "sans-serif"] } } } }</script>';
    echo '</head><body class="bg-[#FFF8F8] text-[#4A1D2F] font-sans antialiased">';
    echo '<header class="bg-white border-b border-pink-100 shadow-sm"><div class="max-w-6xl mx-auto px-4 py-4 flex flex-wrap items-center justify-between gap-3">';
    echo '<h1 class="font-serif text-xl text-brand-900">Pink Glow — Painel</h1>';
    echo '<nav class="flex flex-wrap gap-2 text-xs font-semibold">';
    foreach ($links as $href => $label) {
        echo '<a class="px-3 py-1.5 rounded-full border border-pink-200 text-brand-700 hover:bg-pink-50 transition" href="' . BASE . $href . '">' . $label . '</a>';
    }
    echo '<a class="px-3 py-1.5 rounded-full bg-primary text-white hover:bg-primary-container transition" href="' . BASE . '/">Vitrine</a>';
    echo '<a class="px-3 py-1.5 rounded-full bg-rose-50 text-rose-700 hover:bg-rose-100 transition" href="' . BASE . '/logout.php">Sair</a>';
    echo '</nav></div></header>';
    echo '<main class="max-w-6xl mx-auto px-4 py-8">';
    echo '<h2 class="font-serif text-2xl text-brand-900 mb-6">' . htmlspecialchars($titulo) . '</h2>';
    if (!empty($_SESSION['flash_ok'])) { echo '<p class="mb-4 px-4 py-2 rounded-lg bg-emerald-50 text-emerald-700 text-sm">' . htmlspecialchars($_SESSION['flash_ok']) . '</p>'; unset($_SESSION['flash_ok']); }
    if (!empty($_SESSION['flash_erro'])) { echo '<p class="mb-4 px-4 py-2 rounded-lg bg-rose-50 text-rose-700 text-sm">' . htmlspecialchars($_SESSION['flash_erro']) . '</p>'; unset($_SESSION['flash_erro']); }
}

function admin_fim(): void {
    echo '</main></body></html>';
}
