<?php
// RF04 — Adicionar item ao carrinho (POST).
// Carrinho persiste em $_SESSION mesmo para visitante (login opcional).
header('Content-Type: text/html; charset=utf-8');
session_start();
require __DIR__ . '/../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE . '/');
    exit;
}

$produto_id  = (int)($_POST['produto_id'] ?? 0);
$variacao_id = (int)($_POST['variacao_id'] ?? 0);
$quantidade  = max(1, (int)($_POST['quantidade'] ?? 1));

// Valida produto + variação existente e ativa (RN01: estoque > 0)
$stmt = $pdo->prepare(
    'SELECT v.id AS variacao_id, v.estoque, v.cor_tom, p.nome, p.ativo
     FROM produto_variacoes v
     JOIN produtos p ON p.id = v.produto_id
     WHERE v.id = :vid AND v.produto_id = :pid LIMIT 1'
);
$stmt->execute([':vid' => $variacao_id, ':pid' => $produto_id]);
$v = $stmt->fetch();

if (!$v || !$v['ativo']) {
    $_SESSION['flash_erro'] = 'Produto ou variação inválidos.';
    header('Location: ' . BASE . '/');
    exit;
}

if (!isset($_SESSION['carrinho']) || !is_array($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

$atual = (int)($_SESSION['carrinho'][$variacao_id]['quantidade'] ?? 0);
$nova  = $atual + $quantidade;

// RN01 — quantidade no carrinho não pode exceder o estoque da variação
if ($v['estoque'] <= 0 || $nova > (int)$v['estoque']) {
    $_SESSION['flash_erro'] = "Estoque insuficiente para '{$v['nome']}' ({$v['cor_tom']}). Disponível: {$v['estoque']} un.";
    header('Location: ' . BASE . '/');
    exit;
}

$_SESSION['carrinho'][$variacao_id] = [
    'produto_id'  => $produto_id,
    'variacao_id' => $variacao_id,
    'quantidade'  => $nova,
];
$_SESSION['flash_ok'] = "'{$v['nome']}' ({$v['cor_tom']}) adicionado à sacola.";
header('Location: ' . BASE . '/carrinho/');
exit;
