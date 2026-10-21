<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Pink &amp; Glow Cosméticos | Realce Sua Beleza</title>
<!-- Google Fonts for Editorial Luxury Feel -->
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400;1,600&amp;family=Plus+Jakarta+Sans:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<!-- Tailwind CSS CDN with plugins -->
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<!-- Tailwind Config (tokens do DESIGN.md) -->
<script id="tailwind-config">
    tailwind.config = {
     theme: {
       extend: {
         colors: {
          brand: { 50: '#FDF2F5', 100: '#FCE7F0', 200: '#F9D0DF', 300: '#F4A8C4', 400: '#F472B6', 500: '#E11D74', 600: '#DB2777', 700: '#BE185D', 800: '#9D174D', 900: '#58102B' },
          warmnude: { 50: '#FDFBFA', 100: '#F7F3EF', 200: '#EFE7DF', 300: '#E2D3C4', 800: '#463A38' },
          gold: { 300: '#FCD34D', 400: '#F59E0B', 500: '#D4AF37', 600: '#B48811' },
          'surface': '#fff8f8', 'surface-dim': '#e9d5d9', 'surface-bright': '#fff8f8',
          'surface-container-lowest': '#ffffff', 'surface-container-low': '#fff0f2',
          'surface-container': '#fde9ed', 'surface-container-high': '#f7e3e7',
          'surface-container-highest': '#f1dee2', 'on-surface': '#23191c',
          'on-surface-variant': '#594047', 'inverse-surface': '#392d31',
          'inverse-on-surface': '#ffecf0', 'outline': '#8d6f77', 'outline-variant': '#e1bec6',
          'surface-tint': '#ba0060', 'primary': '#b7005e', 'on-primary': '#ffffff',
          'primary-container': '#db2777', 'on-primary-container': '#fffdff',
          'inverse-primary': '#ffb1c7', 'secondary': '#735c00', 'on-secondary': '#ffffff',
          'secondary-container': '#fed65b', 'on-secondary-container': '#745c00',
          'tertiary': '#a22f71', 'on-tertiary': '#ffffff', 'tertiary-container': '#c1488b',
          'on-tertiary-container': '#fffdff', 'error': '#ba1a1a', 'on-error': '#ffffff',
          'error-container': '#ffdad6', 'on-error-container': '#93000a',
          'primary-fixed': '#ffd9e2', 'primary-fixed-dim': '#ffb1c7', 'on-primary-fixed': '#3f001c',
          'on-primary-fixed-variant': '#8e0048', 'secondary-fixed': '#ffe088',
          'secondary-fixed-dim': '#e9c349', 'on-secondary-fixed': '#241a00',
          'on-secondary-fixed-variant': '#574500', 'tertiary-fixed': '#ffd8e7',
          'tertiary-fixed-dim': '#ffafd3', 'on-tertiary-fixed': '#3d0026',
          'on-tertiary-fixed-variant': '#85145a', 'background': '#fff8f8',
          'on-background': '#23191c', 'surface-variant': '#f1dee2'
         },
         borderRadius: { 'DEFAULT': '0.25rem', lg: '0.5rem', xl: '0.75rem', full: '9999px' },
         spacing: { 'gutter': '1.5rem', 'gutter-sm': '1rem', 'margin': '2rem', 'margin-sm': '1rem',
                    'space-xs': '0.25rem', 'space-sm': '0.5rem', 'space-md': '1rem',
                    'space-lg': '1.5rem', 'space-xl': '2.5rem' },
         fontFamily: {
           serif: ['"Playfair Display"', 'Georgia', 'serif'],
           sans: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif'],
           'display-hero': ['"Playfair Display"', 'serif'],
           'headline-lg': ['"Playfair Display"', 'serif'],
           'headline-md': ['"Playfair Display"', 'serif'],
           'title-lg': ['"Plus Jakarta Sans"', 'sans-serif'],
           'title-md': ['"Plus Jakarta Sans"', 'sans-serif'],
           'body-lg': ['"Plus Jakarta Sans"', 'sans-serif'],
           'body-md': ['"Plus Jakarta Sans"', 'sans-serif'],
           'body-sm': ['"Plus Jakarta Sans"', 'sans-serif'],
           'price-display': ['"Plus Jakarta Sans"', 'sans-serif'],
           'label-lg': ['"Plus Jakarta Sans"', 'sans-serif'],
           'label-md': ['"Plus Jakarta Sans"', 'sans-serif'],
           'label-sm': ['"Plus Jakarta Sans"', 'sans-serif']
         },
         fontSize: {
           'display-hero': ['56px', { lineHeight: '64px', letterSpacing: '-0.02em', fontWeight: '700' }],
           'headline-lg': ['36px', { lineHeight: '44px', fontWeight: '600' }],
           'headline-md': ['24px', { lineHeight: '32px', fontWeight: '600' }],
           'title-lg': ['20px', { lineHeight: '28px', fontWeight: '600' }],
           'title-md': ['16px', { lineHeight: '24px', fontWeight: '600' }],
           'body-lg': ['16px', { lineHeight: '26px', fontWeight: '400' }],
           'body-md': ['14px', { lineHeight: '22px', fontWeight: '400' }],
           'body-sm': ['12px', { lineHeight: '18px', fontWeight: '400' }],
           'price-display': ['20px', { lineHeight: '28px', letterSpacing: '-0.01em', fontWeight: '700' }],
           'label-lg': ['14px', { lineHeight: '20px', letterSpacing: '0.02em', fontWeight: '600' }],
           'label-md': ['12px', { lineHeight: '16px', letterSpacing: '0.04em', fontWeight: '600' }],
           'label-sm': ['11px', { lineHeight: '14px', letterSpacing: '0.06em', fontWeight: '700' }]
         }
       }
     }
   }
</script>
<!-- Custom Styling for Soft Glow and Fine Borders -->
<style data-purpose="custom-utilities">
    .glow-shadow {
      box-shadow: 0 10px 30px -10px rgba(219, 39, 119, 0.15);
    }
    .gold-border-gradient {
      border-image: linear-gradient(to right, #F59E0B, #DB2777) 1;
    }
    .blur-blob {
      filter: blur(80px);
    }
</style>
<meta content="web_dashboard" name="shell-type"/>
<link rel="stylesheet" href="<?= defined('BASE') ? BASE : '' ?>/assets/css/style.css"/>
</head>
<body class="bg-[#FFFDFE] text-[#4A1D2F] font-sans antialiased selection:bg-pink-100 selection:text-brand-700">
<!-- BEGIN: TopAnnouncementBar -->
<!-- Top promotional banner with free shipping notice and quick store features -->
<div class="bg-gradient-to-r from-brand-900 via-brand-800 to-brand-900 text-white text-xs py-2 px-4 text-center font-medium tracking-wide flex items-center justify-center gap-2" data-purpose="top-notice-bar">
<span class="inline-block w-2 h-2 rounded-full bg-gold-400 animate-pulse"></span>
<span>FRETE GRÁTIS nas compras a partir de R$ 149,90 | Parcele em até 6x sem juros ou 5% OFF no PIX</span>
<span class="text-gold-300 ml-1 font-semibold">CUPOM: GLOW10</span>
</div>
<!-- END: TopAnnouncementBar -->
<!-- BEGIN: MainHeader -->
<!-- Primary brand header with navigation links, search, user actions and staff login button -->
<header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-pink-100 shadow-sm transition-all duration-300">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="flex items-center justify-between h-20 gap-4">
<!-- Brand Logo -->
<a class="flex-shrink-0 transition-transform hover:scale-[1.02]" data-purpose="site-logo" href="<?= defined('BASE') ? BASE : '' ?>/">
<img alt="Logo Pink &amp; Glow Cosméticos" class="h-14 sm:h-16 w-auto object-contain" src="<?= url_base('/assets/img/logo.jpg') ?>"/>
</a>
<!-- Desktop Navigation Bar -->
<nav class="hidden lg:flex items-center space-x-7 text-sm font-semibold tracking-wide text-brand-900" data-purpose="desktop-nav">
<a class="text-brand-600 transition-colors hover:text-brand-700 border-b-2 border-brand-500 pb-0.5" href="#inicio">Início</a>
<a class="hover:text-brand-600 transition-colors" href="#vitrine">Vitrine &amp; Produtos</a>
<a class="hover:text-brand-600 transition-colors" href="#categorias">Categorias</a>
<a class="hover:text-brand-600 transition-colors" href="#contato">Contato</a>
<a class="inline-flex items-center text-brand-700 hover:text-brand-800" href="#promocoes">
<span class="mr-1 text-gold-500">✨</span> Ofertas Especiais
</a>
</nav>
<!-- Search Bar (busca server-side via GET ?q=, combina com demais filtros) -->
<form class="hidden md:flex flex-1 max-w-xs relative items-center" data-purpose="quick-search" method="get" action="<?= defined('BASE') ? BASE : '' ?>/">
<input type="hidden" name="categoria" value="<?= (int)($categoriaId ?? 0) ?: '' ?>"/>
<input type="hidden" name="preco_min" value="<?= htmlspecialchars((string)($precoMin ?? '')) ?>"/>
<input type="hidden" name="preco_max" value="<?= htmlspecialchars((string)($precoMax ?? '')) ?>"/>
<input type="hidden" name="marca" value="<?= htmlspecialchars($marca ?? '') ?>"/>
<input class="w-full bg-warmnude-50 border border-pink-200 rounded-full py-2 pl-4 pr-10 text-xs sm:text-sm text-brand-900 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 placeholder:text-pink-300 transition-all shadow-inner" id="searchInput" name="q" placeholder="Buscar batom, blush, rímel..." type="search" value="<?= htmlspecialchars($q ?? '') ?>"/>
<button aria-label="Pesquisar" class="absolute right-3 text-brand-400 hover:text-brand-600" type="submit">
<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewbox="0 0 24 24">
<path d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" stroke-linecap="round" stroke-linejoin="round"></path>
</svg>
</button>
</form>
<!-- Action Items: Admin login, User Account, Shopping Cart Bag -->
<div class="flex items-center gap-3" data-purpose="header-user-actions">
<!-- Staff/Employee Login Link as in wireframe 'login do funcionario' -->
<a class="hidden sm:inline-flex items-center text-xs font-semibold px-3 py-1.5 rounded-full border border-pink-200 text-brand-700 hover:bg-pink-50 hover:border-brand-300 transition" href="<?= defined('BASE') ? BASE : '' ?>/admin/">
<svg class="w-3.5 h-3.5 mr-1 text-brand-600" fill="none" stroke="currentColor" stroke-width="2" viewbox="0 0 24 24">
<path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke-linecap="round" stroke-linejoin="round"></path>
</svg>
Área do Funcionário
</a>
<?php if (empty($_SESSION['usuario_id'])): ?>
<a class="inline-flex items-center text-xs font-semibold px-3 py-1.5 rounded-full bg-primary text-on-primary hover:bg-surface-tint transition" href="<?= defined('BASE') ? BASE : '' ?>/login.php">Entrar</a>
<a class="hidden sm:inline-flex items-center text-xs font-semibold px-3 py-1.5 rounded-full border border-pink-200 text-brand-700 hover:bg-pink-50 hover:border-brand-300 transition" href="<?= defined('BASE') ? BASE : '' ?>/cadastro.php">Cadastrar</a>
<?php else: ?>
<a class="inline-flex items-center text-xs font-semibold px-3 py-1.5 rounded-full border border-pink-200 text-brand-700 hover:bg-pink-50 hover:border-brand-300 transition" href="<?= defined('BASE') ? BASE : '' ?>/conta.php">Minha Conta</a>
<?php endif; ?>
<!-- Customer Login / Profile -->
<a class="p-2 text-brand-800 hover:text-brand-600 transition rounded-full hover:bg-pink-50" href="<?= empty($_SESSION['usuario_id']) ? (defined('BASE') ? BASE : '') . '/login.php' : (defined('BASE') ? BASE : '') . '/conta.php' ?>" title="Minha Conta">
<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewbox="0 0 24 24">
<path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-linecap="round" stroke-linejoin="round"></path>
</svg>
</a>
<!-- Cart Button with Counter Badge -->
<a class="relative p-2 text-brand-800 hover:text-brand-600 transition rounded-full hover:bg-pink-50" href="<?= defined('BASE') ? BASE : '' ?>/carrinho/" title="Sacola de Compras">
<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewbox="0 0 24 24">
<path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke-linecap="round" stroke-linejoin="round"></path>
</svg>
<span class="absolute top-1 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white transform bg-brand-600 rounded-full shadow-sm">
<?= array_sum(array_map(fn($i) => (int)($i['quantidade'] ?? 0), $_SESSION['carrinho'] ?? [])) ?>
</span>
</a>
</div>
</div>
</div>
</header>
<!-- END: MainHeader -->
<main id="inicio">
<?php if (!empty($_SESSION['flash_ok'])): ?><p class="max-w-7xl mx-auto mt-4 px-4 py-2 rounded-lg bg-emerald-50 text-emerald-700 text-sm"><?= htmlspecialchars($_SESSION['flash_ok']) ?></p><?php unset($_SESSION['flash_ok']); endif; ?>
<?php if (!empty($_SESSION['flash_erro'])): ?><p class="max-w-7xl mx-auto mt-4 px-4 py-2 rounded-lg bg-rose-50 text-rose-700 text-sm"><?= htmlspecialchars($_SESSION['flash_erro']) ?></p><?php unset($_SESSION['flash_erro']); endif; ?>
<div class="flex flex-col w-full">
<!-- Editorial Showcase Section -->
<section class="relative overflow-hidden bg-surface-bright py-space-xl">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter items-center">
<!-- Text Column -->
<div class="lg:col-span-7 flex flex-col space-y-space-lg">
<div class="inline-flex items-center space-x-space-sm bg-surface-container-high px-space-md py-space-xs rounded-full self-start">
<span class="w-2 h-2 rounded-full bg-primary animate-ping"></span>
<span class="font-label-sm text-primary tracking-wider uppercase">Nova Coleção Luminous Glow</span>
</div>
<h1 class="font-display-hero text-display-hero text-on-surface">
Realce sua luminosidade com <span class="italic font-display-hero text-primary font-normal">dermocosméticos multifuncionais</span> e acabamento acetinado.
</h1>
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-xl">
Texturas aveludadas, pigmentos bio-compatíveis e sensorial supremo. Crie composições radiantes sem esforço, nutrindo sua pele durante todo o dia.
</p>
<div class="flex flex-wrap items-center gap-space-md pt-space-xs">
<a class="inline-flex items-center justify-center font-label-lg text-label-lg bg-primary text-on-primary px-space-xl py-space-md rounded-full shadow-lg hover:bg-surface-tint transition-all duration-200" href="<?= defined('BASE') ? BASE : '' ?>/cadastro.php">
Quero me cadastrar
</a>
<a class="inline-flex items-center justify-center font-label-lg text-label-lg bg-surface-container text-primary px-space-xl py-space-md rounded-full hover:bg-surface-container-high transition-all duration-200" href="#vitrine">
Ver coleção completa
</a>
</div>
<!-- Trust Badges -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-space-md pt-space-lg">
<div class="flex items-center space-x-space-sm bg-surface-container-lowest p-space-sm rounded-xl shadow-sm">
<span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">eco</span>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-on-surface">100% Vegano</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">Cruelty-free certificado</span>
</div>
</div>
<div class="flex items-center space-x-space-sm bg-surface-container-lowest p-space-sm rounded-xl shadow-sm">
<span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">verified</span>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-on-surface">Testado Clinicamente</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">Laudos dermatológicos</span>
</div>
</div>
<div class="flex items-center space-x-space-sm bg-surface-container-lowest p-space-sm rounded-xl shadow-sm">
<span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">local_shipping</span>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-on-surface">Logística Expressa</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">Envio para todo o Brasil</span>
</div>
</div>
</div>
</div>
<!-- Visual Column -->
<div class="lg:col-span-5 relative">
<div class="relative mx-auto max-w-md lg:max-w-none">
<!-- Glow backdrop decoration -->
<div class="absolute -inset-4 bg-gradient-to-tr from-primary-fixed to-secondary-fixed rounded-full blur-2xl opacity-40"></div>
<div class="relative bg-surface-container-lowest rounded-3xl overflow-hidden shadow-xl">
<img alt="Modelo com maquiagem radiante e acabamento acetinado Pink &amp; Glow" class="w-full h-auto object-cover aspect-[4/5]" src="<?= url_base('/assets/img/hero.png') ?>"/>
<!-- Floating Badge -->
<div class="absolute bottom-4 left-4 right-4 bg-surface-bright/90 backdrop-blur-md p-space-md rounded-2xl shadow-md flex items-center justify-between">
<div class="flex items-center space-x-space-sm">
<div class="w-10 h-10 rounded-full bg-primary-fixed flex items-center justify-center text-primary">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">auto_awesome</span>
</div>
<div class="flex flex-col">
<span class="font-title-md text-title-md text-on-surface">Acabamento Profissional</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">Efeito pele de seda sem oleosidade</span>
</div>
</div>
<div class="flex items-center bg-secondary-container text-on-secondary-container px-space-sm py-0.5 rounded-full font-label-sm">
<span>★ 4.9</span>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- Categorias com imagem (Lábios, Olhos, Pele, Acessórios) -->
<section class="py-space-xl bg-surface" id="categorias">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="flex flex-col md:flex-row md:items-end justify-between mb-space-lg gap-space-sm">
<div>
<span class="font-label-sm text-label-sm text-primary tracking-widest uppercase">Navegue por</span>
<h2 class="font-headline-lg text-headline-lg text-on-surface">Categorias</h2>
</div>
<p class="font-body-md text-body-md text-on-surface-variant max-w-sm">
Encontre seus produtos favoritos por categoria e descubra as novidades de cada linha.
</p>
</div>
<div class="grid grid-cols-2 lg:grid-cols-4 gap-gutter">
<?php if (!empty($categorias) && is_iterable($categorias)): ?>
<?php foreach ($categorias as $cat): ?>
<a class="group relative rounded-3xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-200 aspect-[4/5]" href="<?= htmlspecialchars((defined('BASE') ? BASE : '') . '/?categoria=' . (int)$cat['id']) ?>">
<img alt="<?= htmlspecialchars($cat['nome']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" src="<?= htmlspecialchars(url_base(($cat['imagem_url'] ?? '') !== '' ? $cat['imagem_url'] : '/assets/img/placeholder.svg')) ?>"/>
<div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent"></div>
<div class="absolute bottom-0 left-0 right-0 p-space-md flex items-center justify-between">
<span class="font-title-lg text-title-lg text-white"><?= htmlspecialchars($cat['nome']) ?></span>
<span class="material-symbols-outlined text-white/90">arrow_forward</span>
</div>
</a>
<?php endforeach; ?>
<?php endif; ?>
</div>
</div>
</section>
<!-- Interactive Category Pills Filter (busca server-side: links com querystring) -->
<section class="py-space-lg bg-surface" id="vitrine">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="flex flex-col md:flex-row md:items-end justify-between mb-space-lg gap-space-sm">
<div>
<span class="font-label-sm text-label-sm text-primary tracking-widest uppercase">Catálogo Exclusivo</span>
<h2 class="font-headline-lg text-headline-lg text-on-surface">Nossos Destaques</h2>
</div>
<p class="font-body-md text-body-md text-on-surface-variant max-w-sm">
Selecione a categoria desejada e explore formulações de alta performance com infusão de ativos botânicos.
</p>
</div>
<?php
// Monta querystring preservando os filtros ativos (q, preco_min, preco_max, marca, categoria)
$pg_filtros = function (array $sobrescrever = []) use ($q, $precoMin, $precoMax, $marca, $categoriaId) {
    $f = [
        'q'          => (string)($q ?? ''),
        'preco_min'  => (string)($precoMin ?? ''),
        'preco_max'  => (string)($precoMax ?? ''),
        'marca'      => (string)($marca ?? ''),
        'categoria'  => $categoriaId > 0 ? (string)(int)$categoriaId : '',
    ];
    $f = array_merge($f, $sobrescrever);
    $f = array_filter($f, fn($v) => $v !== '' && $v !== null);
    $qs = http_build_query($f);
    return $qs !== '' ? '?' . $qs : '#vitrine';
};
?>
<!-- Scrollable Filter Pills -->
<div class="flex items-center gap-space-sm overflow-x-auto pb-space-sm no-scrollbar" id="categoryFilterContainer">
<a class="category-pill whitespace-nowrap px-space-lg py-space-sm rounded-full font-label-md transition-all duration-150 <?= (int)($categoriaId ?? 0) <= 0 ? 'bg-primary text-on-primary shadow-sm' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' ?>" href="<?= htmlspecialchars($pg_filtros(['categoria' => ''])) ?>" data-category="all">Linha Completa</a>
<?php if (!empty($categorias) && is_iterable($categorias)): ?>
<?php foreach ($categorias as $cat): ?>
<a class="category-pill whitespace-nowrap px-space-lg py-space-sm rounded-full font-label-md transition-all duration-150 <?= (int)($categoriaId ?? 0) === (int)$cat['id'] ? 'bg-primary text-on-primary shadow-sm' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' ?>" href="<?= htmlspecialchars($pg_filtros(['categoria' => (int)$cat['id']])) ?>" data-category="<?= (int)$cat['id'] ?>"><?= htmlspecialchars($cat['nome']) ?></a>
<?php endforeach; ?>
<?php endif; ?>
</div>
<!-- Filtros de preço e marca -->
<form class="mt-space-md flex flex-wrap items-end gap-space-sm" method="get" action="<?= defined('BASE') ? BASE : '' ?>/">
<input type="hidden" name="q" value="<?= htmlspecialchars($q ?? '') ?>"/>
<input type="hidden" name="categoria" value="<?= (int)($categoriaId ?? 0) ?: '' ?>"/>
<label class="flex flex-col text-xs font-semibold text-brand-900">Marca
<select name="marca" class="mt-1 rounded-xl border border-pink-200 bg-white px-3 py-2 text-sm">
<option value="">Todas</option>
<?php if (!empty($marcas) && is_iterable($marcas)): ?>
<?php foreach ($marcas as $m): ?>
<option value="<?= htmlspecialchars($m) ?>"<?= ($marca ?? '') === $m ? ' selected' : '' ?>><?= htmlspecialchars($m) ?></option>
<?php endforeach; ?>
<?php endif; ?>
</select>
</label>
<label class="flex flex-col text-xs font-semibold text-brand-900">Preço mín.
<input type="number" step="0.01" min="0" name="preco_min" value="<?= htmlspecialchars((string)($precoMin ?? '')) ?>" class="mt-1 w-28 rounded-xl border border-pink-200 bg-white px-3 py-2 text-sm" placeholder="0,00"/>
</label>
<label class="flex flex-col text-xs font-semibold text-brand-900">Preço máx.
<input type="number" step="0.01" min="0" name="preco_max" value="<?= htmlspecialchars((string)($precoMax ?? '')) ?>" class="mt-1 w-28 rounded-xl border border-pink-200 bg-white px-3 py-2 text-sm" placeholder="0,00"/>
</label>
<button class="rounded-full bg-primary text-on-primary px-space-lg py-2 text-xs font-semibold hover:bg-surface-tint transition" type="submit">Aplicar filtros</button>
<a class="rounded-full border border-pink-200 text-brand-700 px-space-lg py-2 text-xs font-semibold hover:bg-pink-50 transition" href="<?= defined('BASE') ? BASE : '' ?>/">Limpar</a>
</form>
</div>
</section>
<!-- Product Grid (dinâmico a partir de $produtos) -->
<section class="pb-space-xl bg-surface">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<?php if (empty($produtos)): ?>
<p class="py-space-xl text-center font-body-md text-body-md text-on-surface-variant">Nenhum produto encontrado para os filtros selecionados. Ajuste a busca ou <a class="text-primary underline" href="<?= defined('BASE') ? BASE : '' ?>/">veja a linha completa</a>.</p>
<?php else: ?>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-gutter" id="productsGrid">
<?php foreach ($produtos as $produto): ?>
<!-- Item: <?= htmlspecialchars($produto['nome']) ?> -->
<div class="product-item group flex flex-col bg-surface-container-lowest rounded-3xl p-space-md shadow-sm hover:shadow-md transition-all duration-200" data-category="<?= (int)($produto['id_categoria'] ?? 0) ?>">
<div class="relative w-full aspect-square bg-surface-container-low rounded-2xl overflow-hidden mb-space-md flex items-center justify-center">
<img alt="<?= htmlspecialchars($produto['nome']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" src="<?= htmlspecialchars(url_base(($produto['imagem_url'] ?? '') !== '' ? $produto['imagem_url'] : '/assets/img/placeholder.svg')) ?>"/>
<?php if (!empty($produto['preco_antigo'])): ?>
<span class="absolute top-3 left-3 bg-primary-fixed text-on-primary-fixed-variant font-label-sm px-space-sm py-0.5 rounded-full">
Promoção
</span>
<?php endif; ?>
<?php if ((int)($produto['estoque_total'] ?? 0) <= 0): ?>
<span class="absolute top-3 right-3 bg-surface-container-high text-on-surface-variant font-label-sm px-space-sm py-0.5 rounded-full">
Sem estoque
</span>
<?php endif; ?>
</div>
<div class="flex items-center justify-between mb-space-xs">
<span class="font-label-sm text-label-sm text-primary uppercase tracking-wider"><?= htmlspecialchars($produto['categoria'] ?? '') ?></span>
<?php if (!empty($produto['marca'])): ?>
<span class="font-label-sm text-label-sm text-on-surface-variant tracking-wider"><?= htmlspecialchars($produto['marca']) ?></span>
<?php endif; ?>
</div>
<h3 class="font-title-md text-title-md text-on-surface group-hover:text-primary transition-colors">
<?= htmlspecialchars($produto['nome']) ?>
</h3>
<?php if (!empty($produto['descricao'])): ?>
<p class="font-body-sm text-body-sm text-on-surface-variant line-clamp-2 mt-1 mb-space-md">
<?= htmlspecialchars($produto['descricao']) ?>
</p>
<?php endif; ?>
<div class="mt-auto pt-space-sm flex items-center justify-between">
<div class="flex flex-col">
<?php if (!empty($produto['preco_antigo'])): ?>
<span class="font-body-sm text-body-sm text-outline line-through">R$ <?= number_format((float)$produto['preco_antigo'], 2, ',', '.') ?></span>
<?php endif; ?>
<span class="font-price-display text-price-display text-primary">R$ <?= number_format((float)$produto['preco'], 2, ',', '.') ?></span>
</div>
<?php if ((int)($produto['estoque_total'] ?? 0) <= 0): ?>
<!-- RN01: sem estoque — botão desabilitado -->
<button aria-label="Produto sem estoque" class="w-11 h-11 rounded-full bg-surface-container text-outline flex items-center justify-center cursor-not-allowed" disabled type="button">
<span class="material-symbols-outlined">block</span>
</button>
<?php else: ?>
<form method="post" action="<?= defined('BASE') ? BASE : '' ?>/carrinho/adicionar.php">
<input type="hidden" name="produto_id" value="<?= (int)$produto['id'] ?>"/>
<input type="hidden" name="variacao_id" value="<?= (int)($produto['variacao_padrao_id'] ?? 0) ?>"/>
<input type="hidden" name="quantidade" value="1"/>
<button aria-label="Adicionar à sacola" class="w-11 h-11 rounded-full bg-surface-container text-primary flex items-center justify-center hover:bg-primary hover:text-on-primary transition-colors" title="Adicionar à sacola" type="submit">
<span class="material-symbols-outlined">add_shopping_cart</span>
</button>
</form>
<?php endif; ?>
</div>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>
</div>
</section>
<!-- First Purchase Promo Banner -->
<section class="py-space-xl bg-surface-container-low" id="promocoes">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="relative overflow-hidden bg-gradient-to-r from-primary via-tertiary to-surface-tint rounded-3xl p-8 sm:p-12 text-on-primary shadow-xl">
<!-- Ambient vector rings -->
<svg class="absolute -right-16 -top-16 w-80 h-80 text-on-primary/10 pointer-events-none" fill="currentColor" viewbox="0 0 200 200">
<circle cx="100" cy="100" fill="none" r="80" stroke="currentColor" stroke-dasharray="8 6" stroke-width="4"></circle>
<circle cx="100" cy="100" fill="none" r="50" stroke="currentColor" stroke-width="2"></circle>
</svg>
<div class="relative z-10 max-w-2xl flex flex-col space-y-space-md">
<div class="inline-flex items-center space-x-2 bg-on-primary/15 backdrop-blur-md px-space-md py-1 rounded-full self-start">
<span class="material-symbols-outlined text-secondary-fixed text-sm" style="font-variation-settings: 'FILL' 1;">redeem</span>
<span class="font-label-sm text-secondary-fixed tracking-wider uppercase">Privilégio de Boas-Vindas</span>
</div>
<h2 class="font-headline-lg text-headline-lg text-on-primary">
Sua primeira experiência radiante com 15% OFF
</h2>
<p class="font-body-lg text-body-lg text-surface-container-high">
Aproveite o cupom exclusivo para inaugurar sua rotina de cuidados com produtos veganos desenvolvidos por maquiadores especialistas.
</p>
<div class="pt-space-xs flex flex-wrap items-center gap-space-md">
<div class="flex items-center bg-on-primary text-primary px-space-lg py-space-sm rounded-full shadow-inner font-price-display text-price-display tracking-widest select-all cursor-pointer" id="voucherButton" title="Clique para copiar">
<span>GLOWVIP</span>
<span class="material-symbols-outlined ml-2 text-base" id="copyIcon">content_copy</span>
</div>
<a class="inline-flex items-center font-label-lg text-label-lg bg-secondary-container text-on-secondary-container px-space-xl py-space-sm rounded-full hover:bg-secondary-fixed transition-colors" href="#vitrine">
Resgatar Agora
</a>
</div>
<span class="font-body-sm text-body-sm text-surface-container-high/80">
*Válido apenas na primeira compra de clientes cadastrados. Não cumulativo com outras promoções ativas.
</span>
</div>
</div>
</div>
</section>
</div>
<script>
  // Voucher Copy Interaction
  const voucherBtn = document.getElementById('voucherButton');
  const copyIcon = document.getElementById('copyIcon');

  if (voucherBtn) {
    voucherBtn.addEventListener('click', () => {
      navigator.clipboard.writeText('GLOWVIP').then(() => {
        if (copyIcon) copyIcon.textContent = 'check';
        voucherBtn.classList.add('bg-secondary-fixed');
        setTimeout(() => {
          if (copyIcon) copyIcon.textContent = 'content_copy';
          voucherBtn.classList.remove('bg-secondary-fixed');
        }, 1800);
      });
    });
  }
</script>
</main>
<!-- BEGIN: MainFooter -->
<!-- Site footer complete with corporate links, payment methods and copyright -->
<footer class="bg-brand-900 text-pink-100 pt-16 pb-12 border-t border-brand-800" id="contato">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 pb-12 border-b border-brand-800/80">
<!-- Brand Info -->
<div class="lg:col-span-2 space-y-4">
<div class="bg-white p-2 rounded-lg inline-block">
<img alt="Logo Pink &amp; Glow" class="h-10 w-auto" src="<?= url_base('/assets/img/logo.jpg') ?>"/>
</div>
<p class="text-xs text-pink-200/80 leading-relaxed max-w-sm">
Pink &amp; Glow Cosméticos — Celebrando a autenticidade e o brilho único de cada mulher com fórmulas modernas, textura leve e acabamento radiante.
</p>
<div class="flex items-center space-x-3 text-pink-300">
<span class="text-xs font-semibold">Siga-nos:</span>
<span class="text-xs">@pinkandglow.makeup</span>
</div>
</div>
<!-- Quick Links -->
<div>
<h4 class="text-sm font-serif font-bold text-white mb-4 tracking-wider uppercase">Menu</h4>
<ul class="space-y-2 text-xs">
<li><a class="hover:text-white transition" href="#inicio">Início</a></li>
<li><a class="hover:text-white transition" href="#vitrine">Vitrine Completa</a></li>
<li><a class="hover:text-white transition" href="#categorias">Categorias</a></li>
<li><a class="hover:text-white transition" href="#promocoes">Cupons &amp; Ofertas</a></li>
<li><a class="hover:text-white transition" href="<?= defined('BASE') ? BASE : '' ?>/carrinho/">Minha Sacola</a></li>
</ul>
</div>
<!-- Customer Service -->
<div>
<h4 class="text-sm font-serif font-bold text-white mb-4 tracking-wider uppercase">Atendimento</h4>
<ul class="space-y-2 text-xs">
<li><a class="hover:text-white transition" href="#faq">Central de Ajuda</a></li>
<li><a class="hover:text-white transition" href="#trocas">Trocas e Devoluções</a></li>
<li><a class="hover:text-white transition" href="#frete">Prazos e Entregas</a></li>
<li><a class="hover:text-white transition" href="#rastreio">Rastrear Pedido</a></li>
<li><a class="hover:text-white transition" href="mailto:contato@pinkglow.com.br">contato@pinkglow.com.br</a></li>
</ul>
</div>
<!-- Admin & Staff Portal Links as in wireframe -->
<div>
<h4 class="text-sm font-serif font-bold text-white mb-4 tracking-wider uppercase">Corporativo</h4>
<ul class="space-y-2 text-xs">
<li><a class="hover:text-white transition" href="<?= defined('BASE') ? BASE : '' ?>/admin/">Página do Admin</a></li>
<li><a class="hover:text-white transition" href="<?= defined('BASE') ? BASE : '' ?>/pedidos.php">Relatórios de Pedidos</a></li>
<li><a class="hover:text-white transition" href="<?= defined('BASE') ? BASE : '' ?>/admin/estoque.php">Controle de Estoque</a></li>
<li><a class="hover:text-white transition" href="<?= defined('BASE') ? BASE : '' ?>/conta.php">Consulta de Clientes</a></li>
<li><a class="text-gold-300 font-semibold hover:underline" href="<?= defined('BASE') ? BASE : '' ?>/login.php">Portal do Funcionário →</a></li>
</ul>
</div>
</div>
<!-- Footer Bottom Details: Payment & Copyright -->
<div class="pt-8 flex flex-col md:flex-row items-center justify-between text-xs text-pink-300/70 gap-4">
<div>
© 2025 Pink &amp; Glow Cosméticos Ltda. Todos os direitos reservados. CNPJ: 00.000.000/0001-00
</div>
<!-- Payment Badges Reassurance -->
<div class="flex items-center gap-3">
<span class="text-[11px] font-medium text-pink-200">Formas de Pagamento:</span>
<span class="px-2 py-0.5 bg-brand-800 rounded text-[10px] text-white font-mono">PIX 5% OFF</span>
<span class="px-2 py-0.5 bg-brand-800 rounded text-[10px] text-white">Cartão de Crédito</span>
<span class="px-2 py-0.5 bg-brand-800 rounded text-[10px] text-white">Boleto</span>
</div>
</div>
</div>
</footer>
<!-- END: MainFooter -->
<!-- Interactivity Script for Mini-behaviors -->
<script data-purpose="interaction-handlers">
    // Visual feedback for Add to Bag buttons
    document.querySelectorAll('button').forEach(btn => {
      if (btn.innerText.includes('Adicionar')) {
        btn.addEventListener('click', function() {
          const originalText = this.innerHTML;
          this.innerHTML = '✓ Adicionado!';
          this.classList.add('bg-brand-600', 'text-white');
          setTimeout(() => {
            this.innerHTML = originalText;
            this.classList.remove('bg-brand-600', 'text-white');
          }, 1800);
        });
      }
    });
</script>
</body>
</html>
