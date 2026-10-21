<?php
// Área do cliente — solicitações de devolução (RN03).
header('Content-Type: text/html; charset=utf-8');
session_start();
require __DIR__ . '/../config/db.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: ' . BASE . '/login.php?erro=acesso');
    exit;
}
$uid = (int)$_SESSION['usuario_id'];

$stmt = $pdo->prepare(
    'SELECT d.id, d.pedido_id, d.motivo, d.produto_lacrado, d.status, d.criado_em
     FROM devolucoes d
     WHERE d.usuario_id = :u
     ORDER BY d.criado_em DESC, d.id DESC'
);
$stmt->execute([':u' => $uid]);
$devolucoes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Minhas Devoluções — Pink Glow</title>
<script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-[#FFFDFE] text-[#4A1D2F] p-6">
<div class="max-w-3xl mx-auto">
  <h1 class="text-2xl font-bold text-pink-700">Minhas devoluções</h1>
  <p class="text-sm text-pink-900/60 mb-6"><a href="<?= BASE ?>/pedidos.php" class="text-pink-600 underline">← Voltar aos pedidos</a></p>

  <?php if (empty($devolucoes)): ?>
    <p class="text-pink-900/60">Você ainda não solicitou nenhuma devolução.</p>
  <?php endif; ?>

  <?php foreach ($devolucoes as $d): ?>
  <div class="bg-white border border-pink-100 rounded-2xl p-4 mb-4 text-sm">
    <div class="flex justify-between">
      <strong class="text-pink-700">Devolução #<?= (int)$d['id'] ?> · Pedido #<?= (int)$d['pedido_id'] ?></strong>
      <span class="text-pink-900/60"><?= htmlspecialchars($d['criado_em']) ?></span>
    </div>
    <p class="mt-1">Motivo: <?= htmlspecialchars($d['motivo']) ?></p>
    <p>Produto lacrado: <?= $d['produto_lacrado'] ? 'sim' : 'não' ?> · Status: <strong><?= htmlspecialchars($d['status']) ?></strong></p>
  </div>
  <?php endforeach; ?>
</div>
</body></html>
