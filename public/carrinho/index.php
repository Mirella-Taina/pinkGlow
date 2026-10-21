<?php
// RF04 — Carrinho: lista itens da sessão, permite +/-, remover e mostra subtotal.
// Subtotal recalculado no servidor a cada requisição (preços e estoque do banco).
header('Content-Type: text/html; charset=utf-8');
session_start();
require __DIR__ . '/../../config/db.php';

$carrinho = $_SESSION['carrinho'] ?? [];

// Ações via POST (atualizar quantidade, remover) — sempre com recálculo no servidor
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $vid  = (int)($_POST['variacao_id'] ?? 0);

    if ($acao === 'remover') {
        unset($_SESSION['carrinho'][$vid]);
        $_SESSION['flash_ok'] = 'Item removido da sacola.';
    } elseif (isset($_SESSION['carrinho'][$vid])) {
        $stmt = $pdo->prepare('SELECT estoque FROM produto_variacoes WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $vid]);
        $estoque = (int)($stmt->fetchColumn() ?: 0);
        $qtd = (int)$_SESSION['carrinho'][$vid]['quantidade'];

        if ($acao === 'mais') {
            // RN01: não ultrapassar o estoque da variação
            if ($qtd + 1 > $estoque) {
                $_SESSION['flash_erro'] = "Quantidade máxima disponível: {$estoque} un.";
            } else {
                $_SESSION['carrinho'][$vid]['quantidade'] = $qtd + 1;
            }
        } elseif ($acao === 'menos') {
            if ($qtd <= 1) {
                unset($_SESSION['carrinho'][$vid]);
                $_SESSION['flash_ok'] = 'Item removido da sacola.';
            } else {
                $_SESSION['carrinho'][$vid]['quantidade'] = $qtd - 1;
            }
        } elseif ($acao === 'atualizar') {
            $nova = (int)($_POST['quantidade'] ?? 0);
            if ($nova <= 0) {
                unset($_SESSION['carrinho'][$vid]);
                $_SESSION['flash_ok'] = 'Item removido da sacola.';
            } elseif ($nova > $estoque) {
                $_SESSION['carrinho'][$vid]['quantidade'] = $estoque;
                $_SESSION['flash_erro'] = "Ajustado para o estoque disponível: {$estoque} un.";
            } else {
                $_SESSION['carrinho'][$vid]['quantidade'] = $nova;
            }
        }
    }
    header('Location: ' . BASE . '/carrinho/');
    exit;
}

// Reidrata itens a partir do banco (preço atual, estoque atual — server-side)
$itens = [];
$total = 0.0;
if (!empty($carrinho)) {
    $ids = implode(',', array_map('intval', array_keys($carrinho)));
    $rows = $pdo->query(
        "SELECT v.id AS variacao_id, v.cor_tom, v.estoque, v.preco_extra,
                p.id AS produto_id, p.nome, p.preco, p.imagem_url
         FROM produto_variacoes v
         JOIN produtos p ON p.id = v.produto_id
         WHERE v.id IN ($ids)"
    )->fetchAll();
    foreach ($rows as $r) {
        $vid = (int)$r['variacao_id'];
        if (!isset($carrinho[$vid])) continue;
        $qtd = (int)$carrinho[$vid]['quantidade'];
        // RN01: se estoque caiu abaixo, ajusta e avisa
        if ($qtd > (int)$r['estoque']) {
            $qtd = max(0, (int)$r['estoque']);
            $_SESSION['carrinho'][$vid]['quantidade'] = $qtd;
            $_SESSION['flash_erro'] = "Quantidade de '{$r['nome']}' ajustada ao estoque ({$r['estoque']} un.).";
            if ($qtd === 0) { unset($_SESSION['carrinho'][$vid]); continue; }
        }
        $unitario = (float)$r['preco'] + (float)$r['preco_extra'];
        $subtotal = $unitario * $qtd;
        $total += $subtotal;
        $itens[] = [
            'variacao_id' => $vid,
            'nome'        => $r['nome'],
            'cor_tom'     => $r['cor_tom'],
            'imagem_url'  => $r['imagem_url'],
            'estoque'     => (int)$r['estoque'],
            'quantidade'  => $qtd,
            'unitario'    => $unitario,
            'subtotal'    => $subtotal,
        ];
    }
}

$flash_ok  = $_SESSION['flash_ok']  ?? '';
$flash_err = $_SESSION['flash_erro'] ?? '';
unset($_SESSION['flash_ok'], $_SESSION['flash_erro']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Minha Sacola — Pink Glow</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
<style>body{font-family:'Plus Jakarta Sans',sans-serif}</style>
</head>
<body class="bg-[#FFFDFE] text-[#4A1D2F]">
<div class="max-w-4xl mx-auto px-4 py-10">
  <h1 class="font-serif text-3xl font-bold mb-2">Minha Sacola</h1>
  <p class="text-sm text-pink-900/60 mb-6"><a href="<?= BASE ?>/" class="text-pink-600 underline">← Voltar à vitrine</a></p>

  <?php if ($flash_ok): ?><p class="mb-4 px-4 py-2 rounded-lg bg-emerald-50 text-emerald-700 text-sm"><?= htmlspecialchars($flash_ok) ?></p><?php endif; ?>
  <?php if ($flash_err): ?><p class="mb-4 px-4 py-2 rounded-lg bg-rose-50 text-rose-700 text-sm"><?= htmlspecialchars($flash_err) ?></p><?php endif; ?>

  <?php if (empty($itens)): ?>
    <p class="text-pink-900/60">Sua sacola está vazia. <a href="<?= BASE ?>/" class="text-pink-600 underline">Ver produtos</a></p>
  <?php else: ?>
    <div class="space-y-4">
      <?php foreach ($itens as $it): ?>
      <div class="flex gap-4 bg-white rounded-2xl shadow-sm border border-pink-100 p-4">
        <img src="<?= htmlspecialchars(url_base($it['imagem_url'] ?: '/assets/img/placeholder.svg')) ?>" alt="" class="w-20 h-20 rounded-xl object-cover bg-pink-50"/>
        <div class="flex-1">
          <h2 class="font-semibold"><?= htmlspecialchars($it['nome']) ?></h2>
          <p class="text-xs text-pink-900/60">Tom: <?= htmlspecialchars($it['cor_tom']) ?> · Estoque: <?= $it['estoque'] ?></p>
          <p class="text-sm mt-1">R$ <?= number_format($it['unitario'], 2, ',', '.') ?> / un.</p>
          <div class="flex items-center gap-2 mt-2">
            <form method="post" class="inline"><input type="hidden" name="acao" value="menos"/><input type="hidden" name="variacao_id" value="<?= $it['variacao_id'] ?>"/><button class="w-8 h-8 rounded-full border border-pink-200 text-pink-600" type="submit">−</button></form>
            <span class="min-w-[2rem] text-center font-semibold"><?= $it['quantidade'] ?></span>
            <form method="post" class="inline"><input type="hidden" name="acao" value="mais"/><input type="hidden" name="variacao_id" value="<?= $it['variacao_id'] ?>"/><button class="w-8 h-8 rounded-full border border-pink-200 text-pink-600" type="submit">+</button></form>
            <form method="post" class="inline"><input type="hidden" name="acao" value="remover"/><input type="hidden" name="variacao_id" value="<?= $it['variacao_id'] ?>"/><button class="ml-3 text-xs text-rose-600 underline" type="submit">Remover</button></form>
          </div>
        </div>
        <div class="text-right font-bold text-pink-700">R$ <?= number_format($it['subtotal'], 2, ',', '.') ?></div>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="mt-6 flex items-center justify-between bg-pink-50 rounded-2xl p-4">
      <span class="font-semibold">Subtotal</span>
      <span class="text-2xl font-bold text-pink-700">R$ <?= number_format($total, 2, ',', '.') ?></span>
    </div>
    <a href="<?= BASE ?>/checkout/" class="mt-4 inline-flex items-center justify-center w-full rounded-full bg-pink-600 text-white py-3 font-semibold hover:bg-pink-700 transition">Finalizar compra →</a>
  <?php endif; ?>
</div>
</body>
</html>
